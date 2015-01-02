<?php

class BPGE extends BP_Group_Extension {
	var $bpge = false;

	var $slug             = 'extras';
	var $page_slug        = 'gpages';
	var $name             = false;
	var $nav_item_name    = false;
	var $gpages_item_name = false;

	var $home_name = false;

	var $gpage_id = false;

	/* By default - Is it visible to non-members of a group? Options: public/private */
	var $visibility = false;

	var $create_step_position = 5;
	var $nav_gpages_position  = 12;
	var $nav_item_position    = 14;

	var $enable_create_step = false; // will set to true in future version
	var $enable_nav_item    = false;
	var $enable_gpages_item = false;
	var $enable_edit_item   = false;

	var $display_hook  = 'groups_extras_group_boxes';
	var $template_file = 'groups/single/plugins';

	var $bpge_glob = false;

	function __construct() {
		global $bp;

		$this->bpge_glob = bpge_get_options();

		// we are on a single group page
		if ( ! empty( $bp->groups->current_group ) ) {
			// populate extras data in global var
			$bpge = groups_get_groupmeta( $bp->groups->current_group->id, 'bpge' );
			if ( ! empty( $bpge ) ) {
				$bp->groups->current_group->extras = $bpge;
			}
		}

		if ( ! empty( $bp->groups->current_group ) && ! empty( $bp->groups->current_group->extras['gpage_id'] ) ) {
			$this->gpage_id = $bp->groups->current_group->extras['gpage_id'];
		} elseif ( ! empty( $bp->groups->current_group ) ) {
			$this->gpage_id = $this->get_gpage_by( 'group_id' );
		}

		// Display or Hide top custom Fields menu from group non-members
		$this->visibility = isset( $bp->groups->current_group->extras['display_page'] ) ? $bp->groups->current_group->extras['display_page'] : 'public';
		if ( isset( $bp->groups->current_group->extras['display_page'] ) &&
		     $bp->groups->current_group->extras['display_page'] == 'public' &&
		     $bp->groups->current_group->user_has_access
		) {
			$this->enable_nav_item = true;
		}

		// Display or hide Extras admin menu in group admin
		if ( bpge_user_can( 'group_extras_admin' ) ) {
			$this->enable_edit_item = true;
		}

		if ( bp_is_single_item() && ! empty( $bp->groups->current_group ) && empty( $bp->groups->current_group->extras['display_page_layout'] ) ) {
			$bp->groups->current_group->extras['display_page_layout'] = 'profile';
		}

		// gPages Page
		$this->gpages_item_name = isset( $bp->groups->current_group->extras['gpage_name'] ) ? $bp->groups->current_group->extras['gpage_name'] : bpge_names( 'gpages' );
		if ( isset( $bp->groups->current_group->extras['display_gpages'] ) &&
		     $bp->groups->current_group->extras['display_gpages'] == 'public' &&
		     $bp->groups->current_group->user_has_access
		) {
			$this->enable_gpages_item = true;
		}

		// In Admin
		$this->name = bpge_names( 'nav' );

		// Public page
		$this->nav_item_name = isset( $bp->groups->current_group->extras['display_page_name'] ) ? $bp->groups->current_group->extras['display_page_name'] : bpge_names( 'nav' );

		// Home page
		if ( ! empty( $bp->groups->current_group->extras['home_name'] ) ) {
			$this->home_name                                                        = $bp->groups->current_group->extras['home_name'];
			$bp->bp_options_nav[ $bp->groups->current_group->slug ]['home']['name'] = $this->home_name;
		}

		add_action( 'groups_custom_group_fields_editable', array( $this, 'edit_group_fields' ) );
		add_action( 'groups_group_details_edited', array( $this, 'edit_group_fields_save' ) );

		$order = groups_get_groupmeta( $bp->groups->current_group->id, 'bpge_nav_order' );
		if ( ! empty( $order['extras'] ) ) {
			$this->nav_item_position = $order['extras'];
		}

		if ( $this->enable_gpages_item ) {
			if ( bp_is_group() && bp_is_single_item() ) {
				if ( empty( $order[ $this->page_slug ] ) ) {
					$order[ $this->page_slug ] = 99;
				}
				bp_core_new_subnav_item( array(
					                         'name'            => $this->gpages_item_name,
					                         'slug'            => $this->page_slug,
					                         'parent_slug'     => $bp->groups->current_group->slug,
					                         'parent_url'      => bp_get_group_permalink( $bp->groups->current_group ),
					                         'position'        => $order[ $this->page_slug ],
					                         'item_css_id'     => $this->page_slug,
					                         'screen_function' => array( &$this, 'gpages' )
				                         ) );
			}
		}
	}

	/**
	 * Display the list of fields
	 */
	function display() {
		global $bp;

		// get all to display
		$fields = bpge_get_group_fields( 'publish' );

		if ( empty( $fields ) ) {
			return;
		}

		if ( isset( $bp->groups->current_group->extras['display_page_layout'] ) &&
		     $bp->groups->current_group->extras['display_page_layout'] == 'plain'
		) {
			bpge_view( 'front/display_fields_plain',
			           array(
				           'fields' => $fields
			           )
			);
		} else {
			bpge_view( 'front/display_fields_table',
			           array(
				           'fields' => $fields
			           )
			);
		}
	}

	/************************************************************************/

	/**
	 * Public gPages screen function
	 */
	function gpages() {
		add_action( 'bp_before_group_body', array( &$this, 'gpages_screen_nav' ) );
		add_action( 'bp_template_content', array( &$this, 'gpages_screen_content' ) );

		do_action( 'bpge_gpages', $this );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
	}

	/**
	 * Pages second level navigation
	 */
	function gpages_screen_nav() {
		/** @var $wpdb WPDB */
		global $bp, $wpdb;

		$pages = $this->get_all_gpages( 'publish' );

		switch_to_blog( bpge_get_main_site_id() );

		if ( empty( $bp->action_variables ) ) {
			$bp->action_variables[0] = $wpdb->get_var(
				"SELECT `post_name` FROM {$wpdb->prefix}posts
                WHERE `post_parent` = {$bp->groups->current_group->extras['gpage_id']}
                    AND `post_type` = '{$this->page_slug}'
                ORDER BY `menu_order` ASC
                LIMIT 1"
			);
		}

		restore_current_blog();

		$pages = apply_filters( 'bpge_gpages_nav_in', $pages );

		do_action( 'bpge_gpages_nav_before', $this, $pages );

		// display list of pages if there are more than of 1 of them
		if ( count( $pages ) > 1 ) {
			bpge_view( 'front/display_gpages_nav',
			           array(
				           'pages'     => $pages,
				           'page_slug' => $this->page_slug
			           )
			);
		}

		do_action( 'bpge_gpages_nav_after', $this, $pages );
	}

	/**
	 * Group Pages content
	 */
	function gpages_screen_content() {
		/** @var $wpdb WPDB */
		global $bp, $wpdb;

		switch_to_blog( bpge_get_main_site_id() );

		$page = $wpdb->get_row( $wpdb->prepare(
			"SELECT * FROM {$wpdb->prefix}posts
                    WHERE `post_name` = %s
                        AND `post_type` = %s
                        AND `post_parent` = %d",
			$bp->action_variables[0],
			$this->page_slug,
			$bp->groups->current_group->extras['gpage_id']
		) );

		restore_current_blog();

		do_action( 'bpge_gpages_content_display_before', $this, $page );

		if ( ! empty( $page ) ) {
			bpge_view( 'front/display_gpages_content',
			           array(
				           'page' => $page
			           )
			);
		}

		do_action( 'bpge_gpages_content_display_after', $this, $page );
	}

	/************************************************************************/

	/**
	 * Display exra fields on edit group details page
	 */
	function edit_group_fields() {
		global $bpge;

		$fields = bpge_get_group_fields( 'publish' );

		if ( empty( $fields ) ) {
			return;
		}

		switch_to_blog( bpge_get_main_site_id() );

		foreach ( $fields as $field ) {
			$field->desc    = get_post_meta( $field->ID, 'bpge_field_desc', true );
			$field->options = get_post_meta( $field->ID, 'bpge_field_options', true );

			$req_text = $req_class = false;
			if ( $field->pinged == 'req' ) {
				$req_text  = __( '(required)', 'bpge' );
				$req_class = 'required';
			}

			echo '<label class="' . $req_class . '" for="bpge-' . $field->ID . '">' . stripslashes( $field->post_title ) . ' ' . $req_text . '</label>';

			switch ( $field->post_excerpt ) {
				case 'text':
					echo '<input id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '" type="text" value="' . $field->post_content . '" />';
					break;
				case 'textarea':
					if ( function_exists( 'wp_editor' ) && isset( $bpge['re_fields'] ) && $bpge['re_fields'] == 'yes' ) {
						wp_editor(
							stripslashes( $field->post_content ), // initial content
							'bpge-' . $field->ID, // ID attribute value for the textarea
							array(
								'media_buttons' => false,
								'textarea_name' => 'bpge-' . $field->ID,
							)
						);
					} else {
						echo '<textarea id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '">' . $field->post_content . '</textarea>';
					}
					break;
				case 'select':
					echo '<select id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '">';
					echo '<option value="">-------</option>';
					foreach ( $field->options as $option ) {
						echo '<option ' . ( $field->post_content == $option ? 'selected="selected"' : '' ) . ' value="' . $option . '">' . $option . '</option>';
					}
					echo '</select>';
					break;
				case 'checkbox':
					echo '<span id="bpge-' . $field->ID . '">';
					$content = json_decode( $field->post_content );
					foreach ( $field->options as $option ) {
						echo '<input ' . ( in_array( $option, (array) $content ) ? 'checked="checked"' : '' ) . ' type="checkbox" name="bpge-' . $field->ID . '[]" value="' . $option . '"> ' . $option . '<br />';
					}
					echo '</span>';
					break;
				case 'radio':
					echo '<span id="bpge-' . $field->ID . '">';
					foreach ( $field->options as $option ) {
						echo '<input ' . ( $field->post_content == $option ? 'checked="checked"' : '' ) . ' type="radio" name="bpge-' . $field->ID . '" value="' . $option . '"> ' . $option . '<br />';
					}
					echo '</span>';
					if ( $req_text ) {
						echo '<a class="clear-value" href="javascript:clear( \'bpge-' . $field->ID . '\' );">' . __( 'Clear', 'bpge' ) . '</a>';
					}
					break;
				case 'datebox':
					echo '<input id="bpge-' . $field->ID . '" class="datebox" name="bpge-' . $field->ID . '" type="text" value="' . $field->post_content . '" />';
					break;
			}
			if ( ! empty( $field->desc ) ) {
				echo '<p class="description">' . $field->desc . '</p>';
			}
		}

		restore_current_blog();

		do_action( 'bpge_group_fields_edit', $this, $fields );
	}

	/**
	 * Save extra fields in DB
	 *
	 * @return bool
	 */
	function edit_group_fields_save() {
		/** @var $wpdb WPDB */
		global $wpdb, $bp;

		// If the edit form has been submitted, save the edited details
		if (
			( bp_is_group() && 'edit-details' == $bp->action_variables[0] ) &&
			( $bp->is_item_admin || $bp->is_item_mod ) &&
			isset( $_POST['save'] )
		) {
			/* Check the nonce first. */
			if ( ! check_admin_referer( 'groups_edit_group_details' ) ) {
				return;
			}

			$to_save = array();

			foreach ( $_POST as $data => $value ) {
				if ( substr( $data, 0, 5 ) === 'bpge-' ) {
					$to_save[ $data ] = $value;
				}
			}

			switch_to_blog( bpge_get_main_site_id() );

			foreach ( $to_save as $ID => $value ) {
				$ID = substr( $ID, 5 );

				$field = get_post( $ID );
				if ( $field && $field->pinged == 'req' && empty( $value ) ) {
					$error[] = $field->post_title;
				}

				if ( ! empty( $error ) ) {
					bp_core_add_message(
						sprintf(
							__( 'Required fields should not be empty. Please fill in: %s', 'bpge' ),
							'<strong>' . implode( ', ', $error ) . '</strong>'
						),
						'error' );
				}

				if ( ! is_array( $value ) ) {
					$data = wp_kses_data( $value );
					$data = force_balance_tags( $data );
				} else {
					$value = array_map( "wp_kses_data", $value );
					$value = array_map( "force_balance_tags", $value );
					$data  = json_encode( $value );
				}

				$wpdb->update(
					$wpdb->posts,
					array(
						'post_content' => $data,    // [data]
					),
					array( 'ID' => $ID ),           // [where]
					array( '%s' ),                  // data format
					array( '%d' )                   // where format
				);
			}

			restore_current_blog();

			do_action( 'bpge_group_fields_save', $this, $to_save );
		}
	}

	/************************************************************************/

	/**
	 * Admin area - Main
	 *
	 * @param null $group_id
	 */
	function edit_screen( $group_id = null ) {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		global $bp;

		if ( 'admin' == $bp->current_action && isset( $bp->action_variables[1] ) && $bp->action_variables[1] == 'fields' ) {
			$this->edit_screen_fields( $bp );
		} elseif ( 'admin' == $bp->current_action && isset( $bp->action_variables[1] ) && $bp->action_variables[1] == 'pages' ) {
			$this->edit_screen_pages( $bp );
		} elseif ( 'admin' == $bp->current_action && isset( $bp->action_variables[1] ) && $bp->action_variables[1] == 'fields-manage' ) {
			$this->edit_screen_fields_manage( $bp );
		} elseif ( 'admin' == $bp->current_action && isset( $bp->action_variables[1] ) && $bp->action_variables[1] == 'pages-manage' ) {
			$this->edit_screen_pages_manage( $bp );
		} else {
			$this->edit_screen_general( $bp );
		}
	}

	/**
	 * Admin area - General Settings
	 *
	 * @param $bp
	 */
	function edit_screen_general( $bp ) {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		if ( ! isset( $bp->groups->current_group->extras['display_page'] ) ) {
			$bp->groups->current_group->extras['display_page'] = 'public';
		}
		if ( ! isset( $bp->groups->current_group->extras['display_gpages'] ) ) {
			$bp->groups->current_group->extras['display_gpages'] = 'public';
		}
		if ( ! isset( $bp->groups->current_group->extras['display_page_layout'] ) ) {
			$bp->groups->current_group->extras['display_page_layout'] = 'plain';
		}

		$this->edit_screen_head( 'general' );

		bpge_view( 'front/extras_general',
		           array(
			           'nav_item_name'    => $this->nav_item_name,
			           'gpages_item_name' => $this->gpages_item_name,
			           'group_nav' => bpge_get_nav_order(),
			           'home_name'        => ( $this->home_name ? $this->home_name : bpge_names( 'nav' ) )
		           )
		);

		wp_nonce_field( 'groups_edit_group_extras' );
	}

	/**
	 * Admin area - All Fields
	 */
	function edit_screen_fields() {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$this->edit_screen_head( 'fields' );

		// get all groups fields
		$fields = bpge_get_group_fields( 'any' );

		switch_to_blog( bpge_get_main_site_id() );

		// get set of fields
		$def_set_fields = get_posts( array(
			                             'posts_per_page' => 50,
			                             'numberposts'    => 50,
			                             'order'          => 'ASC',
			                             'orderby'        => 'ID',
			                             'post_type'      => BPGE_FIELDS_SET
		                             ) );

		restore_current_blog();

		if ( empty( $fields ) ) {
			$this->notices( 'no_fields' );
		}

		if ( ! empty( $def_set_fields ) ) {
			bpge_view( 'front/extras_fields_import', array( 'def_set_fields' => $def_set_fields ) );
		}

		bpge_view( 'front/extras_fields_list', array( 'fields' => $fields, 'slug' => $this->slug ) );
	}

	/**
	 * Admin area - All Pages
	 */
	function edit_screen_pages() {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$this->edit_screen_head( 'pages' );

		$pages = $this->get_all_gpages();

		if ( empty( $pages ) ) {
			$this->notices( 'no_pages' );

			return;
		}

		bpge_view( 'front/extras_pages_list',
		           array(
			           'pages'     => $pages,
			           'page_slug' => $this->page_slug,
			           'slug'      => $this->slug
		           )
		);
	}

	/**
	 * Add / Edit 1 field form
	 */
	function edit_screen_fields_manage() {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		switch_to_blog( bpge_get_main_site_id() );

		// if Editing page - get data
		if ( isset( $_GET['edit'] ) && ! empty( $_GET['edit'] ) ) {
			$field       = get_post( intval( $_GET['edit'] ) );
			$field->desc = get_post_meta( $field->ID, 'bpge_field_desc', true );
		} else {
			// get empty values for Adding page
			$field = bpge_get_field_defaults();
		}

		restore_current_blog();

		$this->edit_screen_head( 'fields-manage' );

		bpge_view( 'front/extras_fields_add_edit',
		           array(
			           'field'         => $field,
			           'nav_item_name' => $this->nav_item_name
		           )
		);
	}

	/**
	 * Add / Edit pages form
	 */
	function edit_screen_pages_manage() {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$is_edit = false;
		$page    = new Stdclass;
		// defaults
		$page->ID = $page->post_title = $page->post_name = $page->post_content = $page->post_status = '';

		if ( isset( $_GET['edit'] ) && ! empty( $_GET['edit'] ) && is_numeric( $_GET['edit'] ) ) {
			$is_edit = true;
			$page    = $this->get_gpage_by( 'id', $_GET['edit'] );
		}

		$this->edit_screen_head( 'pages-manage' );

		bpge_view( 'front/extras_pages_add_edit',
		           array(
			           'page'       => $page,
			           'is_edit'    => $is_edit,
			           'enabled_re' => ( isset( $this->bpge_glob['re'] ) && $this->bpge_glob['re'] == '1' ) ? true : false
		           )
		);

		wp_nonce_field( 'groups_edit_group_extras' );
	}

	/**
	 * Save all changes into DB
	 *
	 * @param null $group_id
	 *
	 * @return bool|void
	 */
	function edit_screen_save( $group_id = null ) {
		// check user access to group extras management pages
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return false;
		}

		global $bp;

		switch_to_blog( bpge_get_main_site_id() );

		if ( bp_is_group() && 'extras' == $bp->action_variables[0] ) {
			if ( ! $bp->is_item_admin ) {
				return false;
			}

			// Import set of fields
			if ( isset( $_POST['approve_import'] ) && $_POST['approve_import'] == true && ! empty( $_POST['import_def_set_fields'] ) ) {
				$this->import_set_fields();
				die;
			}

			// Save general settings
			if ( isset( $_POST['save_general'] ) ) {
				/* Check the nonce first. */
				if ( ! check_admin_referer( 'groups_edit_group_extras' ) ) {
					return false;
				}

				$meta = $bp->groups->current_group->extras;

				$meta['display_page']        = $_POST['group-extras-display'];
				$meta['display_page_name']   = stripslashes( strip_tags( $_POST['group-extras-display-name'] ) );
				$meta['display_page_layout'] = $_POST['group-extras-display-layout'];

				$meta['gpage_name']     = stripslashes( strip_tags( $_POST['group-gpages-display-name'] ) );
				$meta['display_gpages'] = $_POST['group-gpages-display'];

				$meta['home_name'] = stripslashes( strip_tags( $_POST['group-extras-home-name'] ) );

				// now save nav order
				if ( ! empty( $_POST['bpge_group_nav_position'] ) ) {
					// preparing vars
					parse_str( $_POST['bpge_group_nav_position'], $tab_order );
					$nav_old = bpge_get_nav_order(); //$bp->bp_options_nav[$bp->groups->current_group->slug];
					$order   = array();
					$pos     = 1;

					// update menu_order for each nav item
					foreach ( $tab_order['position'] as $index => $old_position ) {
						foreach ( $nav_old as $nav ) {
							if ( $nav['position'] == $old_position ) {
								$order[ $nav['slug'] ] = $pos;
							}
							$pos ++;
						}
					}

					// save to DB
					groups_update_groupmeta( $bp->groups->current_group->id, 'bpge_nav_order', $order );
				}

				do_action( 'bpge_save_general', $this, $meta );

				// Save into groupmeta table some general settings
				groups_update_groupmeta( $bp->groups->current_group->id, 'bpge', $meta );

				$this->notices( 'settings_updated' );

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/' );
			}

			// Save new field
			if ( isset( $_POST['save_fields_add'] ) ) {
				/* Check the nonce first. */
				if ( ! check_admin_referer( 'groups_edit_group_extras' ) ) {
					return false;
				}

				$field                 = array();
				$field['post_title']   = $_POST['extra-field-title'];
				$field['post_excerpt'] = $_POST['extra-field-type'];
				$field['pinged']       = $_POST['extra-field-required'];
				$field['post_status']  = $_POST['extra-field-display'];
				$field['post_parent']  = $_POST['group-id'];
				$field['post_type']    = BPGE_GFIELDS;

				$field = apply_filters( 'bpge_new_field', $field );

				$options = array();
				if ( ! empty( $_POST['options'] ) ) {
					foreach ( $_POST['options'] as $option ) {
						$options[] = htmlspecialchars( strip_tags( $option ) );
					}
				}

				do_action( 'bpge_save_new_field', $this, $field );

				// Save Field
				$field_id = wp_insert_post( $field );

				if ( is_integer( $field_id ) ) {
					// Save field options
					update_post_meta( $field_id, 'bpge_field_options', $options );

					$field_desc = apply_filters( 'bpge_new_field_desc', $_POST['extra-field-desc'] );
					update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

					$this->notices( 'added_field' );
				}

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/' );
			}

			// Save new page
			if ( isset( $_POST['save_pages_add'] ) ) {
				/* Check the nonce first. */
				if ( ! check_admin_referer( 'groups_edit_group_extras' ) ) {
					return false;
				}

				global $current_blog;
				if ( empty( $current_blog ) || ! isset( $current_blog->blog_id ) ) {
					$current_blog          = new Stdclass;
					$current_blog->blog_id = 1;
				}

				$admin = get_user_by( 'email', get_blog_option( $current_blog->blog_id, 'admin_email' ) );

				// Save as a post_type
				$page = array(
					'comment_status' => 'open',
					'ping_status'    => 'open',
					'post_author'    => $admin->ID,
					'post_title'     => $_POST['extra-page-title'],
					'post_name'      => isset( $_POST['extra-page-slug'] ) ? $_POST['extra-page-slug'] : '',
					'post_content'   => $_POST['extra-page-content'],
					'post_parent'    => $bp->groups->current_group->extras['gpage_id'],
					'post_status'    => $_POST['extra-page-status'],
					'menu_order'     => count( $this->get_all_gpages() ) + 1,
					'post_type'      => $this->page_slug
				);

				$page = apply_filters( 'bpge_new_page', $page );

				do_action( 'bpge_save_new_page_before', $this, $page );
				$page_id = wp_insert_post( $page );
				update_post_meta( $page_id, 'group_id', $bp->groups->current_group->id );
				do_action( 'bpge_save_new_page_after', $this, $page, $page_id );

				$this->notices( 'added_page' );

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/pages/' );
			}

			// Edit existing field
			if ( isset( $_POST['save_fields_edit'] ) ) {
				/* Check the nonce first. */
				if ( ! check_admin_referer( 'groups_edit_group_extras' ) ) {
					return false;
				}

				$new                 = array();
				$new['ID']           = $_POST['extra-field-id'];
				$new['post_title']   = $_POST['extra-field-title'];
				$new['post_excerpt'] = $_POST['extra-field-type'];
				$new['pinged']       = $_POST['extra-field-required'];
				$new['post_status']  = $_POST['extra-field-display'];
				$new['post_parent']  = $_POST['group-id'];
				$new['post_type']    = BPGE_GFIELDS;

				$new = apply_filters( 'bpge_update_field', $new );

				if ( ! empty( $_POST['options'] ) ) {
					$options = array();
					foreach ( $_POST['options'] as $option ) {
						$options[] = htmlspecialchars( strip_tags( $option ) );
					}
				}

				// Save into DB
				$field_id = wp_update_post( $new );

				do_action( 'bpge_update_field', $this, $new );

				if ( is_integer( $field_id ) ) {
					$field_desc = apply_filters( 'bpge_update_field_desc', htmlspecialchars( strip_tags( $_POST['extra-field-desc'] ) ) );
					update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

					$this->notices( 'edited_field' );
				}

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/' );

			}

			// Edit existing page
			if ( isset( $_POST['save_pages_edit'] ) ) {
				/* Check the nonce first. */
				if ( ! check_admin_referer( 'groups_edit_group_extras' ) ) {
					return false;
				}

				$page['ID']           = $_POST['extra-page-id'];
				$page['post_title']   = apply_filters( 'bpge_updated_page_title', $_POST['extra-page-title'] );
				$page['post_name']    = apply_filters( 'bpge_updated_page_slug', isset( $_POST['extra-page-slug'] ) ? $_POST['extra-page-slug'] : '' );
				$page['post_content'] = apply_filters( 'bpge_updated_page_content', $_POST['extra-page-content'] );
				$page['post_status']  = apply_filters( 'bpge_updated_page_status', $_POST['extra-page-status'] );

				do_action( 'bpge_save_updated_page_before', $this, $page );
				$updated = wp_update_post( $page );
				do_action( 'bpge_save_updated_page_after', $this, $updated );

				$this->notices( 'edited_page' );

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/pages/' );
			}
		}

		restore_current_blog();

		return true;
	}

	/**
	 * Display Header and Extra-Nav
	 *
	 * @param string $cur
	 */
	function edit_screen_head( $cur = 'general' ) {
		global $bp;
		$group_link = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug;

		bpge_view( 'front/extras_top_menu',
		           array(
			           'group_link' => $group_link,
			           'cur'        => $cur
		           )
		);

		do_action( 'bpge_extra_menus', $cur );
	}

	/************************************************************************/

	/**
	 * Getting all extra items (fields or pages) for defined group
	 *
	 * @param string $type
	 * @param integer $id
	 *
	 * @return mixed|void
	 */
	function get_all_items( $type, $id ) {
		// get all fields
		$items = array();

		if ( $type == 'fields' ) {
			$items = groups_get_groupmeta( $id, 'bpge_fields' );
		} elseif ( $type == 'pages' ) {
			$items = groups_get_groupmeta( $id, 'bpge_pages' );
		}

		if ( empty( $items ) ) {
			$items = false;
		} else {
			$items = json_decode( $items );
		}

		return apply_filters( 'bpge_get_all_items', $items );
	}

	/**
	 * Retrieve groups pages
	 *
	 * @param string $post_status
	 *
	 * @return array
	 */
	function get_all_gpages( $post_status = 'any' ) {
		global $bp;

		switch_to_blog( bpge_get_main_site_id() );

		$args = array(
			'post_parent' => $bp->groups->current_group->extras['gpage_id'],
			'post_type'   => $this->page_slug,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
			'numberposts' => 999,
			'post_status' => $post_status
		);

		$data = get_posts( $args );

		restore_current_blog();

		return $data;
	}

	/**
	 * Get item (field or page) by slug - reusable
	 *
	 * @param $type
	 * @param $slug
	 *
	 * @return mixed|void
	 */
	function get_item_by_slug( $type, $slug ) {
		global $bp;
		// just in case...
		if ( ! is_string( $type ) || ! is_string( $slug ) ) {
			return false;
		}

		$items    = array();
		$searched = array();

		$type = apply_filters( 'bpge_items_by_slug_type', $type );
		$slug = apply_filters( 'bpge_items_by_slug_slug', $slug );

		if ( $type == 'field' ) {
			$items = $this->get_all_items( 'fields', $bp->groups->current_group->id );
		} elseif ( $type == 'page' ) {
			$items = $this->get_all_items( 'pages', $bp->groups->current_group->id );
		}

		foreach ( $items as $item ) {
			if ( $slug == $item->slug ) {
				$searched = $item;
			}
		}

		return apply_filters( 'bpge_items_by_slug', $searched );
	}

	/**
	 * Import set of fields
	 */
	function import_set_fields() {
		/** @var $wpdb WPDB */
		global $wpdb, $bp;

		switch_to_blog( bpge_get_main_site_id() );

		$redirect = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/';

		if ( $_POST['group-id'] != $bp->groups->current_group->id ) {
			restore_current_blog();
			wp_redirect( $redirect );
			die;
		}

		$group_id = $bp->groups->current_group->id;

		// get all fields from a set with appropriate options from a db
		$fields = $wpdb->get_results( $wpdb->prepare(
			"SELECT ID, post_title, post_content, post_excerpt
            FROM {$wpdb->posts}
            WHERE post_parent = %d
                AND post_type = %s
            ORDER BY ID ASC",
			intval( $_POST['import_def_set_fields'] ),
			BPGE_FIELDS
		) );

		// save everything to a group
		foreach ( $fields as $field ) {
			$field->options = array();
			// get options if needed
			if ( in_array( $field->post_excerpt, array( 'select', 'radio', 'checkbox' ) ) ) {
				$field->options = get_post_meta( $field->ID, 'bpge_field_options', true );
			}

			// display option
			$field_display = get_post_meta( $field->ID, 'bpge_field_display', true );
			if ( empty( $field_display ) ) {
				$field_display = 'no';
			}

			// save the field
			$new                 = array();
			$new['post_type']    = BPGE_GFIELDS;
			$new['post_parent']  = $group_id;
			$new['post_title']   = $field->post_title;
			$new['post_excerpt'] = $field->post_excerpt;
			$new['post_status']  = $field_display == 'yes' ? 'publish' : 'draft';

			$field_id = wp_insert_post( $new );

			if ( is_int( $field_id ) ) {
				$field_desc = strip_tags( $field->post_content );
				update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

				// ... and options
				if ( ! empty( $field->options ) ) {
					update_post_meta( $field_id, 'bpge_field_options', $field->options );
				}
			}
		}

		restore_current_blog();

		wp_redirect( $redirect );
		die;
	}

	/**
	 * Notices about user actions
	 *
	 * @param $type
	 */
	function notices( $type ) {
		switch ( $type ) {
			case 'settings_updated';
				bp_core_add_message( __( 'Group Extras settings were succefully updated.', 'bpge' ) );
				break;
			case 'added_field';
				bp_core_add_message( __( 'New field was successfully added.', 'bpge' ) );
				break;
			case 'edited_field';
				bp_core_add_message( __( 'The field was successfully updated.', 'bpge' ) );
				break;
			case 'added_page';
				bp_core_add_message( __( 'New page was successfully added.', 'bpge' ) );
				break;
			case 'edited_page';
				bp_core_add_message( __( 'The page was successfully updated.', 'bpge' ) );
				break;
			case 'no_fields':
				echo '<div class="" id="message"><p>' . __( 'Please create at least 1 extra field to show it in a list.', 'bpge' ) . '</p></div>';
				break;
			case 'no_pages':
				echo '<div class="" id="message"><p>' . __( 'Please create at least 1 extra page to show it in a list.', 'bpge' ) . '</p></div>';
				break;
		}
		do_action( 'bpge_notices', $type );
	}

	/**
	 * Create a storage for groups pages
	 *
	 * @param $what
	 * @param bool $input
	 *
	 * @return int|null|WP_Post
	 */
	function get_gpage_by( $what, $input = false ) {
		global $bp;

		$data = null;

		switch_to_blog( bpge_get_main_site_id() );

		switch ( $what ) {
			case 'group_id':
				global $current_blog;
				if ( empty( $current_blog ) || ! isset( $current_blog->blog_id ) ) {
					$current_blog          = new Stdclass;
					$current_blog->blog_id = 1;
				}
				$admin    = get_user_by( 'email', get_blog_option( $current_blog->blog_id, 'admin_email' ) );
				$old_data = groups_get_groupmeta( $bp->groups->current_group->id, 'bpge' );
				// create a gpage...
				$old_data['gpage_id'] = wp_insert_post( array(
					                                        'comment_status' => 'closed',
					                                        'ping_status'    => 'closed',
					                                        'post_author'    => $admin->ID,
					                                        'post_content'   => $bp->groups->current_group->description,
					                                        'post_name'      => $bp->groups->current_group->slug,
					                                        'post_status'    => 'publish',
					                                        'post_title'     => $bp->groups->current_group->name,
					                                        'post_type'      => $this->page_slug
				                                        ) );
				// ...and save it to reuse later
				groups_update_groupmeta( $bp->groups->current_group->id, 'bpge', $old_data );

				$data = $old_data['gpage_id'];
				break;

			case 'id':
				$data = get_post( $input );
				break;
		}

		restore_current_blog();

		return $data;
	}

	/************************************************************************/

	/**
	 * Creation step - enter the data
	 *
	 * @param null $group_id
	 */
	function create_screen( $group_id = null ) {
		do_action( 'bpge_create_screen', $this );
	}

	/**
	 * Creation step - save the data
	 *
	 * @param null $group_id
	 */
	function create_screen_save( $group_id = null ) {
		do_action( 'bpge_create_save', $this );
	}

	// Load if was not already loaded
	private static $instance = false;

	static function getInstance() {
		if ( ! self::$instance ) {
			self::$instance = new BPGE;
		}

		return self::$instance;
	}
}

bp_register_group_extension( 'BPGE' );
