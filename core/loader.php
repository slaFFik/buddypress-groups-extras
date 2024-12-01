<?php

/**
 * Class BPGE.
 */
class BPGE extends BP_Group_Extension {

	private static $instance;

	public $bpge = false;

	public $slug             = 'extras';
	public $page_slug        = 'gpages';
	public $name             = '';
	public $nav_item_name    = '';
	public $gpages_item_name = '';

	public $home_name = '';

	public $gpage_id = 0;

	/* By default - Is it visible to non-members of a group? Options: public/private. */
	public $visibility = false;

	public $create_step_position = 5;
	public $nav_gpages_position  = 12;
	public $nav_item_position    = 14;

	public $enable_create_step = false; // will set to true in future version.
	public $enable_nav_item    = false;
	public $enable_gpages_item = false;
	public $enable_edit_item   = false;

	public $display_hook  = 'groups_extras_group_boxes';
	public $template_file = 'groups/single/plugins';

	public $bpge_glob = [];

	/**
	 * BPGE constructor.
	 * Initialize everything.
	 */
	public function __construct() {

		$bp = buddypress();

		$this->bpge_glob = bpge_get_options();

		// We are on a single group page.
		if ( ! empty( $bp->groups->current_group ) ) {
			// Populate extras data in global var.
			$bpge = groups_get_groupmeta( bp_get_current_group_id(), 'bpge' );

			if ( ! empty( $bpge ) ) {
				$bp->groups->current_group->args['extras'] = $bpge;
			}
		}

		if ( ! empty( $bp->groups->current_group ) && ! empty( $bp->groups->current_group->args['extras']['gpage_id'] ) ) {
			$this->gpage_id = $bp->groups->current_group->args['extras']['gpage_id'];
		} elseif ( ! empty( $bp->groups->current_group ) ) {
			$this->gpage_id = $this->get_gpage_by( 'group_id' );
		}

		// Save or Hide top custom Fields menu from group non-members.
		$this->visibility = isset( $bp->groups->current_group->args['extras']['display_page'] ) ? $bp->groups->current_group->args['extras']['display_page'] : 'public';

		if ( isset( $bp->groups->current_group->args['extras']['display_page'] ) &&
			$bp->groups->current_group->args['extras']['display_page'] === 'public' &&
			$bp->groups->current_group->user_has_access
		) {
			$this->enable_nav_item = true;
		}

		// Save or hide Extras admin menu in group admin.
		if ( bpge_user_can( 'group_extras_admin' ) ) {
			$this->enable_edit_item = true;
		}

		if ( bp_is_single_item() && ! empty( $bp->groups->current_group ) && empty( $bp->groups->current_group->args['extras']['display_page_layout'] ) ) {
			if ( isset( $bp->groups->current_group->args['extras'] ) ) {
				$current_group_extras = (array) $bp->groups->current_group->args['extras'];
			} else {
				$current_group_extras = [];
			}
			$current_group_extras['display_page_layout'] = 'profile';
			$bp->groups->current_group->args['extras']   = $current_group_extras;
		}

		// gPages Page.
		$this->gpages_item_name = isset( $bp->groups->current_group->args['extras']['gpage_name'] ) ? $bp->groups->current_group->args['extras']['gpage_name'] : bpge_names( 'gpages' );

		if (
			isset( $bp->groups->current_group->args['extras']['display_gpages'] ) &&
			$bp->groups->current_group->args['extras']['display_gpages'] === 'public' &&
			$bp->groups->current_group->user_has_access
		) {
			$this->enable_gpages_item = true;
		}

		// In Admin.
		$this->name = bpge_names( 'nav' );

		// Public page.
		$this->nav_item_name = isset( $bp->groups->current_group->args['extras']['display_page_name'] ) ? $bp->groups->current_group->args['extras']['display_page_name'] : bpge_names( 'nav' );

		// Home page.
		if ( isset( $bp->groups->current_group->args['extras'] ) && ! empty( $bp->groups->current_group->args['extras']['home_name'] ) ) {
			$this->home_name = $bp->groups->current_group->args['extras']['home_name'];

			if ( bpge_is_bp_26() ) {
				buddypress()->groups->nav->edit_nav( [ 'name' => $this->home_name ], 'home', bp_current_item() );
			} else {
				$bp->bp_options_nav[ $bp->groups->current_group->slug ]['home']['name'] = $this->home_name;
			}
		}

		add_action( 'groups_custom_group_fields_editable', [ $this, 'edit_group_fields' ] );
		add_action( 'groups_group_details_edited', [ $this, 'edit_group_fields_save' ] );

		add_filter( 'bp_group_admin_form_action', [ $this, 'edit_group_admin_form_action' ], 10, 2 );

		$order = (array) groups_get_groupmeta( bp_get_current_group_id(), 'bpge_nav_order' );

		if ( ! empty( $order['extras'] ) ) {
			$this->nav_item_position = $order['extras'];
		}

		/*
		 * Group Pages.
		 */
		if ( ! $this->enable_gpages_item ) {
			return;
		}

		if ( bp_is_group() && bp_is_single_item() ) {
			if ( empty( $order[ $this->page_slug ] ) ) {
				$order[ $this->page_slug ] = 99;
			}

			bp_core_new_subnav_item(
				[
					'name'            => $this->gpages_item_name,
					'slug'            => $this->page_slug,
					'parent_slug'     => $bp->groups->current_group->slug,
					'parent_url'      => bp_get_group_permalink( $bp->groups->current_group ),
					'position'        => $order[ $this->page_slug ],
					'item_css_id'     => $this->page_slug,
					'screen_function' => [ &$this, 'gpages' ],
				]
			);
		}
	}

	/**
	 * Display the list of fields.
	 *
	 * @param int|null $group_id
	 */
	public function display( $group_id = null ) {

		$bp = buddypress();

		// Get all to display.
		$fields = bpge_get_group_fields( 'publish' );

		if ( empty( $fields ) ) {
			return;
		}

		$template = 'front/display_fields_table';

		if (
			isset( $bp->groups->current_group->args['extras']['display_page_layout'] ) &&
			$bp->groups->current_group->args['extras']['display_page_layout'] === 'plain'
		) {
			$template = 'front/display_fields_plain';
		}

		bpge_view(
			$template,
			[
				'fields' => $fields,
			]
		);
	}

	/************************************************************************/

	/**
	 * Public gPages screen function.
	 */
	public function gpages() {

		add_action( 'bp_before_group_body', [ &$this, 'gpages_screen_nav' ] );
		add_action( 'bp_template_content', [ &$this, 'gpages_screen_content' ] );

		do_action( 'bpge_gpages', $this );
		bp_core_load_template(
			// phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
			apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' )
		);
	}

	/**
	 * Pages second level navigation.
	 */
	public function gpages_screen_nav() {

		global $wpdb;
		$bp = buddypress();

		$pages = $this->get_all_gpages( 'publish' );

		switch_to_blog( bpge_get_main_site_id() );

		if ( empty( $bp->action_variables ) ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$bp->action_variables[0] = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT `post_name`
					FROM {$wpdb->posts}
                	WHERE `post_parent` = %d
                    	AND `post_type` = %s
                    	AND `post_status` = 'publish'
                	ORDER BY `menu_order` ASC
	                LIMIT 1",
                    (int) $bp->groups->current_group->args['extras']['gpage_id'],
                    $this->page_slug
                )
            );
		}

		restore_current_blog();

		$pages = apply_filters( 'bpge_gpages_nav_in', $pages );

		do_action( 'bpge_gpages_nav_before', $this, $pages );

		// Display the list of pages if there are more than 1 of them.
		if ( count( $pages ) > 1 ) {
			bpge_view(
				'front/display_gpages_nav',
				[
					'pages'     => $pages,
					'page_slug' => $this->page_slug,
				]
			);
		}

		do_action( 'bpge_gpages_nav_after', $this, $pages );
	}

	/**
	 * Group Pages content.
	 */
	public function gpages_screen_content() {

		global $wpdb;
		$bp = buddypress();

		switch_to_blog( bpge_get_main_site_id() );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$page = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}posts
                    WHERE `post_name` = %s
                        AND `post_type` = %s
                        AND `post_parent` = %d",
                $bp->action_variables[0],
                $this->page_slug,
                $bp->groups->current_group->args['extras']['gpage_id']
            )
        );

		restore_current_blog();

		do_action( 'bpge_gpages_content_display_before', $this, $page );

		if ( ! empty( $page ) ) {
			bpge_view(
				'front/display_gpages_content',
				[
					'page' => $page,
				]
			);
		}

		do_action( 'bpge_gpages_content_display_after', $this, $page );
	}

	/************************************************************************/

	/**
	 * Display exra fields on edit group details page.
	 */
	public function edit_group_fields() {

		global $bpge;

		$fields = bpge_get_group_fields( 'publish' );

		if ( empty( $fields ) ) {
			return;
		}

		switch_to_blog( bpge_get_main_site_id() );

		foreach ( $fields as $field ) {
			$field->desc    = get_post_meta( $field->ID, 'bpge_field_desc', true );
			$field->options = get_post_meta( $field->ID, 'bpge_field_options', true );

			$req_text  = '';
			$req_class = '';

			if ( $field->pinged === 'req' ) {
				$req_text  = __( '(required)', 'buddypress-groups-extras' );
				$req_class = 'required';
			}

			echo '<label class="' . esc_attr( $req_class ) . '" for="bpge-' . (int) $field->ID . '">' . esc_html( $field->post_title ) . ' ' . esc_html( $req_text ) . '</label>';

			switch ( $field->post_excerpt ) {
				case 'text':
					echo '<input id="bpge-' . (int) $field->ID . '" name="bpge-' . (int) $field->ID . '" type="text" value="' . esc_attr( $field->post_content ) . '" />';
					break;

				case 'textarea':
					if ( function_exists( 'wp_editor' ) && isset( $bpge['re_fields'] ) && $bpge['re_fields'] === 'yes' ) {
						wp_editor(
							stripslashes( $field->post_content ), // Initial content.
							'bpge-' . $field->ID, // ID attribute value for the textarea.
							[
								'media_buttons' => false,
								'textarea_name' => 'bpge-' . $field->ID,
							]
						);
					} else {
						echo '<textarea id="bpge-' . (int) $field->ID . '" name="bpge-' . (int) $field->ID . '">' . esc_textarea( $field->post_content ) . '</textarea>';
					}
					break;

				case 'select':
					echo '<select id="bpge-' . (int) $field->ID . '" name="bpge-' . (int) $field->ID . '">';
					echo '<option value="">-------</option>';
					foreach ( $field->options as $option ) {
						echo '<option ' . ( $field->post_content === $option ? 'selected="selected"' : '' ) . ' value="' . esc_attr( $option ) . '">' . esc_html( $option ) . '</option>';
					}
					echo '</select>';
					break;

				case 'checkbox':
					echo '<span id="bpge-' . (int) $field->ID . '">';

					$content = json_decode( $field->post_content );

					foreach ( $field->options as $option ) {
						echo '<input ' . ( in_array( $option, (array) $content, true ) ? 'checked="checked"' : '' ) . ' type="checkbox" name="bpge-' . (int) $field->ID . '[]" value="' . esc_attr( $option ) . '"> ' . esc_html( $option ) . '<br />';
					}
					echo '</span>';
					break;

				case 'radio':
					echo '<span id="bpge-' . (int) $field->ID . '">';
					foreach ( $field->options as $option ) {
						echo '<input ' . ( $field->post_content === $option ? 'checked="checked"' : '' ) . ' type="radio" name="bpge-' . (int) $field->ID . '" value="' . esc_attr( $option ) . '"> ' . esc_html( $option ) . '<br />';
					}
					echo '</span>';
					if ( $req_text ) {
						echo '<a class="clear-value" href="javascript:clear( \'bpge-' . (int) $field->ID . '\' );">' . esc_html__( 'Clear', 'buddypress-groups-extras' ) . '</a>';
					}
					break;

				case 'datebox':
					echo '<input id="bpge-' . (int) $field->ID . '" class="datebox" name="bpge-' . (int) $field->ID . '" type="text" value="' . esc_attr( $field->post_content ) . '" />';
					break;
			}

			if ( ! empty( $field->desc ) ) {
				echo '<p class="description">' . esc_html( $field->desc ) . '</p>';
			}
		}

		restore_current_blog();

		do_action( 'bpge_group_fields_edit', $this, $fields );
	}

	/**
	 * Save extra fields in DB.
	 */
	public function edit_group_fields_save() {

		global $wpdb;
		$bp = buddypress();

		// If the edit form has been submitted, save the edited details.
		if (
			( bp_is_group() && $bp->action_variables[0] === 'edit-details' ) &&
			( $bp->is_item_admin || $bp->is_item_mod ) &&
			isset( $_POST['save'] )
		) {
			// Check the nonce first.
			if ( ! check_admin_referer( 'groups_edit_group_details' ) ) {
				return;
			}

			$to_save = [];
			$error   = [];

			foreach ( $_POST as $data => $value ) {
				if ( strpos( $data, 'bpge-' ) === 0 ) {
					$to_save[ $data ] = $value;
				}
			}

			switch_to_blog( bpge_get_main_site_id() );

			foreach ( $to_save as $id => $value ) {
				$id    = substr( $id, 5 );
				$field = get_post( $id );

				if ( $field && $field->pinged === 'req' && empty( $value ) ) {
					$error[] = $field->post_title;
				}

				if ( ! empty( $error ) ) {
					bp_core_add_message(
						sprintf( /* translators: %s - List of empty fields. */
							esc_html__( 'Required fields should not be empty. Please fill in: %s', 'buddypress-groups-extras' ),
							implode( ', ', $error )
						),
                        'error'
                    );
				}

				if ( ! is_array( $value ) ) {
					// Textarea and text.
					$data = force_balance_tags( wp_kses( $value, wp_kses_allowed_html( 'post' ) ) );
				} else {
					$value = array_map( 'wp_kses_data', $value );
					$value = array_map( 'force_balance_tags', $value );
					$data  = wp_json_encode( $value );
				}

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->update(
					$wpdb->posts,
					[
						'post_content' => $data,
					],
					[ 'ID' => (int) $id ],
					[ '%s' ],
					[ '%d' ]
				);

				clean_post_cache( (int) $id );
			}

			restore_current_blog();

			do_action( 'bpge_group_fields_save', $this, $to_save );
		}
	}

	/************************************************************************/

	/**
	 * Group Admin - Main Logic.
	 *
	 * @param int|null $group_id
	 */
	public function edit_screen( $group_id = null ) {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$bp = buddypress();

		if ( $bp->current_action === 'admin' && isset( $bp->action_variables[1] ) && $bp->action_variables[1] === 'fields' ) {
			$this->edit_screen_fields();
		} elseif ( $bp->current_action === 'admin' && isset( $bp->action_variables[1] ) && $bp->action_variables[1] === 'pages' ) {
			$this->edit_screen_pages();
		} elseif ( $bp->current_action === 'admin' && isset( $bp->action_variables[1] ) && $bp->action_variables[1] === 'fields-manage' ) {
			$this->edit_screen_fields_manage();
		} elseif ( $bp->current_action === 'admin' && isset( $bp->action_variables[1] ) && $bp->action_variables[1] === 'pages-manage' ) {
			$this->edit_screen_pages_manage();
		} else {
			$this->edit_screen_general();
		}
	}

	/**
	 * Group Admin - General Settings.
	 * Nonce: bpge_group_edit_general.
	 */
	protected function edit_screen_general() {

		$bp = buddypress();

		if ( empty( $bp->groups->current_group->args['extras']['display_page'] ) ) {
			$bp->groups->current_group->args['extras']['display_page'] = 'public';
		}
		if ( empty( $bp->groups->current_group->args['extras']['display_gpages'] ) ) {
			$bp->groups->current_group->args['extras']['display_gpages'] = 'public';
		}
		if ( empty( $bp->groups->current_group->args['extras']['display_page_layout'] ) ) {
			$bp->groups->current_group->args['extras']['display_page_layout'] = 'plain';
		}

		$this->edit_screen_head( 'general' );

		bpge_view(
			'front/extras_general',
			[
				'nav_item_name'    => $this->nav_item_name,
				'gpages_item_name' => $this->gpages_item_name,
				'bpge'             => $this->bpge_glob,
			]
		);

		wp_nonce_field( 'bpge_group_edit_general' );
	}

	/**
	 * Group Admin - All Fields.
	 * Nonce: bpge_group_edit_fields.
	 */
	protected function edit_screen_fields() {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$this->edit_screen_head( 'fields' );

		// Get all groups fields.
		$fields = bpge_get_group_fields( 'any' );

		switch_to_blog( bpge_get_main_site_id() );

		// Get set of fields.
		$def_set_fields = get_posts(
			[
				'posts_per_page' => 50,
				'numberposts'    => 50,
				'order'          => 'ASC',
				'orderby'        => 'ID',
				'post_type'      => BPGE_FIELDS_SET,
			]
		);

		restore_current_blog();

		if ( empty( $fields ) ) {
			$this->notices( 'no_fields' );
		}

		if ( ! empty( $def_set_fields ) ) {
			bpge_view( 'front/extras_fields_import', [ 'def_set_fields' => $def_set_fields ] );
		}

		bpge_view(
            'front/extras_fields_list',
            [
				'fields' => $fields,
				'slug'   => $this->slug,
            ]
        );

		wp_nonce_field( 'bpge_group_edit_fields' );
	}

	/**
	 * Group Admin - All Pages.
	 * Nonce: bpge_group_edit_pages.
	 */
	public function edit_screen_pages() {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$this->edit_screen_head( 'pages' );

		$pages = $this->get_all_gpages();

		if ( empty( $pages ) ) {
			$this->notices( 'no_pages' );

			return;
		}

		bpge_view(
			'front/extras_pages_list',
			[
				'pages'     => $pages,
				'page_slug' => $this->page_slug,
				'slug'      => $this->slug,
			]
		);

		wp_nonce_field( 'bpge_group_edit_pages' );
	}

	/**
	 * Group Admin - Add / Edit 1 field form.
	 * Nonce: bpge_group_manage_fields.
	 */
	public function edit_screen_fields_manage() {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		switch_to_blog( bpge_get_main_site_id() );

		// If Editing a page - get data.
		if ( isset( $_GET['edit'] ) && ! empty( $_GET['edit'] ) ) {
			$field       = get_post( (int) $_GET['edit'] );
			$field->desc = get_post_meta( $field->ID, 'bpge_field_desc', true );
		} else {
			// Get empty values for Adding a page.
			$field = bpge_get_field_defaults();
		}

		restore_current_blog();

		$this->edit_screen_head( 'fields-manage' );

		bpge_view(
			'front/extras_fields_add_edit',
			[
				'field'         => $field,
				'nav_item_name' => $this->nav_item_name,
			]
		);

		wp_nonce_field( 'bpge_group_manage_fields' );
	}

	/**
	 * Add / Edit pages form.
	 * Nonce: bpge_group_manage_pages.
	 */
	public function edit_screen_pages_manage() {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return;
		}

		$is_edit = false;
		$page    = new stdClass();
		// Defaults.
		$page->ID           = '';
		$page->post_title   = '';
		$page->post_name    = '';
		$page->post_content = '';
		$page->post_status  = 'draft';

		if ( isset( $_GET['edit'] ) && ! empty( $_GET['edit'] ) && is_numeric( $_GET['edit'] ) ) {
			$is_edit = true;
			$page    = $this->get_gpage_by( 'id', (int) $_GET['edit'] );
		}

		$this->edit_screen_head( 'pages-manage' );

		bpge_view(
			'front/extras_pages_add_edit',
			[
				'page'       => $page,
				'is_edit'    => $is_edit,
				'enabled_re' => isset( $this->bpge_glob['re'] ) && (int) $this->bpge_glob['re'] === 1,
			]
		);

		wp_nonce_field( 'bpge_group_manage_pages' );
	}

	/**
	 * Save all changes to DB.
	 *
	 * @param int|null $group_id
	 *
	 * @return bool
	 */
	public function edit_screen_save( $group_id = null ) {

		// Check user access to group extras management pages.
		if ( ! bpge_user_can( 'group_extras_admin' ) ) {
			return false;
		}

		$bp = buddypress();

		switch_to_blog( bpge_get_main_site_id() );

		if ( $bp->action_variables[0] === 'extras' && bp_is_group() ) {
			if ( ! $bp->is_item_admin ) {
				return false;
			}

			// Import set of fields.
			if (
				isset( $_POST['approve_import'] ) &&
				(bool) $_POST['approve_import'] === true &&
				! empty( $_POST['import_def_set_fields'] )
			) {
				$this->import_set_fields();
				die;
			}

			// Save general settings.
			if ( isset( $_POST['save_general'] ) ) {
				// Check the nonce first.
				check_ajax_referer( 'bpge_group_edit_general' );

				$meta = $bp->groups->current_group->args['extras'];

				$meta['display_page']        = sanitize_key( $_POST['group-extras-display'] ?? 'public' );
				$meta['display_page_name']   = stripslashes( wp_strip_all_tags( wp_unslash( $_POST['group-extras-display-name'] ?? '' ) ) );
				$meta['display_page_layout'] = sanitize_key( $_POST['group-extras-display-layout'] ?? 'plain' );

				$meta['gpage_name']     = stripslashes( wp_strip_all_tags( wp_unslash( $_POST['group-gpages-display-name'] ?? '' ) ) );
				$meta['display_gpages'] = sanitize_key( $_POST['group-gpages-display'] ?? 'public' );

				$meta['home_name'] = stripslashes( wp_strip_all_tags( wp_unslash( $_POST['group-extras-home-name'] ?? '' ) ) );

				// Now save nav order.
				if ( ! empty( $_POST['bpge_group_nav_position'] ) ) {
					// Preparing vars.
					parse_str( sanitize_text_field( wp_unslash( $_POST['bpge_group_nav_position'] ) ), $tab_order );

					array_walk(
						$tab_order['position'],
						static function( &$value ) {
							$value = hex2bin( $value );
						}
					);

					if ( ! in_array( 'home', $tab_order['position'], true ) ) {
						array_unshift( $tab_order['position'], 'home' );
					}

					// Update menu_order for each nav item.
					$order = array_map(
						static function( $position ) {
							++$position;

							return $position * 10;
						},
						array_flip( $tab_order['position'] )
					);

					// Save to DB.
					groups_update_groupmeta( bp_get_current_group_id(), 'bpge_nav_order', $order );
				}

				do_action( 'bpge_save_general', $this, $meta );

				// Save into groupmeta table some general settings.
				groups_update_groupmeta( bp_get_current_group_id(), 'bpge', $meta );

				$this->notices( 'settings_updated' );

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/' );
			}

			// Save new field.
			if ( isset( $_POST['save_fields_add'] ) ) {
				// Check the nonce first.
				check_admin_referer( 'bpge_group_manage_fields' );

				$field                 = [];
				$field['post_title']   = sanitize_text_field( wp_unslash( $_POST['extra-field-title'] ?? '' ) );
				$field['post_excerpt'] = sanitize_key( $_POST['extra-field-type'] ?? '' );
				$field['pinged']       = sanitize_key( $_POST['extra-field-required'] ?? '' );
				$field['post_status']  = sanitize_key( $_POST['extra-field-display'] ?? '' );
				$field['post_parent']  = absint( $_POST['group-id'] ?? 0 );
				$field['post_type']    = BPGE_GFIELDS;

				$field = apply_filters( 'bpge_new_field', $field );

				$options = [];

				if ( ! empty( $_POST['options'] ) ) {
					foreach ( wp_unslash( $_POST['options'] ) as $option ) {
						$options[] = htmlspecialchars( wp_strip_all_tags( $option ) );
					}
				}

				do_action( 'bpge_save_new_field', $this, $field );

				// Save Field.
				$field_id = wp_insert_post( $field );

				if ( is_int( $field_id ) ) {
					// Save field options.
					update_post_meta( $field_id, 'bpge_field_options', $options );

					$field_desc = apply_filters( 'bpge_new_field_desc', sanitize_textarea_field( wp_unslash( $_POST['extra-field-desc'] ?? '' ) ) );

					update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

					$this->notices( 'added_field' );
				}

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/' );
			}

			// Save new page.
			if ( isset( $_POST['save_pages_add'] ) ) {
				// Check the nonce first.
				check_ajax_referer( 'bpge_group_manage_pages' );

				global $current_blog;
				if ( empty( $current_blog ) || ! isset( $current_blog->blog_id ) ) {
					$current_blog          = new stdClass();
					$current_blog->blog_id = 1;
				}

				$admin = get_user_by( 'email', get_blog_option( $current_blog->blog_id, 'admin_email' ) );

				// Save as a post_type.
				$page = [
					'comment_status' => 'open',
					'ping_status'    => 'open',
					'post_author'    => $admin->ID,
					'post_title'     => sanitize_text_field( wp_unslash( $_POST['extra-page-title'] ?? '' ) ),
					'post_name'      => sanitize_text_field( wp_unslash( $_POST['extra-page-slug'] ?? '' ) ),
					'post_content'   => wp_kses_post( wp_unslash( $_POST['extra-page-content'] ?? '' ) ),
					'post_parent'    => $bp->groups->current_group->args['extras']['gpage_id'],
					'post_status'    => sanitize_key( $_POST['extra-page-status'] ?? '' ),
					'menu_order'     => count( $this->get_all_gpages() ) + 1,
					'post_type'      => $this->page_slug,
				];

				$page = apply_filters( 'bpge_new_page', $page );

				do_action( 'bpge_save_new_page_before', $this, $page );

				$page_id = wp_insert_post( $page );

				update_post_meta( $page_id, 'group_id', bp_get_current_group_id() );
				do_action( 'bpge_save_new_page_after', $this, $page, $page_id );

				$this->notices( 'added_page' );

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/pages/' );
			}

			// Edit existing field.
			if ( isset( $_POST['save_fields_edit'] ) ) {
				// Check the nonce first.
				check_ajax_referer( 'bpge_group_manage_fields' );

				$new                 = [];
				$new['ID']           = absint( $_POST['extra-field-id'] ?? 0 );
				$new['post_title']   = sanitize_text_field( wp_unslash( $_POST['extra-field-title'] ?? '' ) );
				$new['post_excerpt'] = sanitize_textarea_field( wp_unslash( $_POST['extra-field-type'] ?? '' ) );
				$new['pinged']       = sanitize_key( $_POST['extra-field-required'] ?? '' );
				$new['post_status']  = sanitize_key( $_POST['extra-field-display'] ?? '' );
				$new['post_parent']  = absint( $_POST['group-id'] ?? 0 );
				$new['post_type']    = BPGE_GFIELDS;

				$new = apply_filters( 'bpge_update_field', $new );

				// Save to DB.
				$field_id = wp_update_post( $new );

				do_action( 'bpge_update_field', $this, $new );

				if ( is_int( $field_id ) ) {
					$field_desc = apply_filters( 'bpge_update_field_desc', htmlspecialchars( wp_strip_all_tags( wp_unslash( $_POST['extra-field-desc'] ?? '' ) ) ) );

					update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

					$this->notices( 'edited_field' );
				}

				bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/' );
			}

			// Edit existing page.
			if ( isset( $_POST['save_pages_edit'] ) ) {
				// Check the nonce first.
				check_ajax_referer( 'bpge_group_manage_pages' );

				$page                 = [];
				$page['ID']           = absint( $_POST['extra-page-id'] ?? 0 );
				$page['post_title']   = sanitize_text_field( apply_filters( 'bpge_updated_page_title', wp_unslash( $_POST['extra-page-title'] ?? '' ) ) );
				$page['post_name']    = sanitize_text_field( apply_filters( 'bpge_updated_page_slug', wp_unslash( $_POST['extra-page-slug'] ) ?? '' ) );
				$page['post_content'] = wp_kses_post( apply_filters( 'bpge_updated_page_content', wp_unslash( $_POST['extra-page-content'] ?? '' ) ) );
				$page['post_status']  = sanitize_key( apply_filters( 'bpge_updated_page_status', $_POST['extra-page-status'] ?? '' ) );

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
	 * Display Header and Extra-Nav.
	 *
	 * @param string $cur
	 */
	public function edit_screen_head( $cur = 'general' ) {

		$bp         = buddypress();
		$group_link = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug;

		bpge_view(
			'front/extras_top_menu',
			[
				'group_link' => $group_link,
				'cur'        => $cur,
			]
		);

		do_action( 'bpge_extra_menus', $cur );
	}

	/************************************************************************/

	/**
	 * Getting all extra items (fields or pages) for defined group.
	 *
	 * @param string  $type
	 * @param integer $id
	 *
	 * @return mixed|void
	 */
	public function get_all_items( $type, $id ) {

		// Get all fields.
		$items = [];

		if ( $type === 'fields' ) {
			$items = groups_get_groupmeta( $id, 'bpge_fields' );
		} elseif ( $type === 'pages' ) {
			$items = groups_get_groupmeta( $id, 'bpge_pages' );
		}

		if ( empty( $items ) ) {
			$items = false;
		} else {
			$items = json_decode( $items, false );
		}

		return apply_filters( 'bpge_get_all_items', $items );
	}

	/**
	 * Retrieve groups pages.
	 *
	 * @param string $post_status
	 *
	 * @return array
	 */
	public function get_all_gpages( $post_status = 'any' ) {

		$bp = buddypress();

		switch_to_blog( bpge_get_main_site_id() );

		$args = [
			'post_parent' => $bp->groups->current_group->args['extras']['gpage_id'],
			'post_type'   => $this->page_slug,
			'orderby'     => 'menu_order',
			'order'       => 'ASC',
			'numberposts' => 999,
			'post_status' => $post_status,
		];

		$data = get_posts( $args );

		restore_current_blog();

		return $data;
	}

	/**
	 * Get item (field or page) by slug - reusable.
	 *
	 * @param $type
	 * @param $slug
	 *
	 * @return mixed|void
	 */
	public function get_item_by_slug( $type, $slug ) {

		$bp = buddypress();
		// just in case...
		if ( ! is_string( $type ) || ! is_string( $slug ) ) {
			return false;
		}

		$items    = [];
		$searched = [];

		$type = apply_filters( 'bpge_items_by_slug_type', $type );
		$slug = apply_filters( 'bpge_items_by_slug_slug', $slug );

		if ( $type === 'field' ) {
			$items = $this->get_all_items( 'fields', bp_get_current_group_id() );
		} elseif ( $type === 'page' ) {
			$items = $this->get_all_items( 'pages', bp_get_current_group_id() );
		}

		foreach ( $items as $item ) {
			if ( $slug === $item->slug ) {
				$searched = $item;
			}
		}

		return apply_filters( 'bpge_items_by_slug', $searched );
	}

	/**
	 * Import set of fields.
	 */
	public function import_set_fields() {

		global $wpdb;
		$bp = buddypress();

		switch_to_blog( bpge_get_main_site_id() );

		$redirect      = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $this->slug . '/fields/';
		$post_group_id = absint( $_POST['group-id'] ?? 0 );

		if ( $post_group_id !== bp_get_current_group_id() ) {
			restore_current_blog();
			wp_safe_redirect( $redirect );
			die;
		}

		$group_id = bp_get_current_group_id();

		$import_def_set_fields = absint( $_POST['import_def_set_fields'] ?? 0 );

		// Get all fields from a set with appropriate options from a db.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$fields = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ID, post_title, post_content, post_excerpt
            	FROM {$wpdb->posts}
            	WHERE post_parent = %d
                	AND post_type = %s
            	ORDER BY ID",
	            $import_def_set_fields,
                BPGE_FIELDS
            )
        );

		// Save everything to a group.
		foreach ( $fields as $field ) {
			$field->options = [];
			// Get options if needed.
			if ( in_array( $field->post_excerpt, [ 'select', 'radio', 'checkbox' ], true ) ) {
				$field->options = get_post_meta( $field->ID, 'bpge_field_options', true );
			}

			// Save option.
			$field_display = get_post_meta( $field->ID, 'bpge_field_display', true );

			if ( empty( $field_display ) ) {
				$field_display = 'no';
			}

			// Save the field.
			$new                 = [];
			$new['post_type']    = BPGE_GFIELDS;
			$new['post_parent']  = $group_id;
			$new['post_title']   = $field->post_title;
			$new['post_excerpt'] = $field->post_excerpt;
			$new['post_status']  = $field_display === 'yes' ? 'publish' : 'draft';

			$field_id = wp_insert_post( $new );

			if ( is_int( $field_id ) ) {
				update_post_meta( $field_id, 'bpge_field_desc', sanitize_textarea_field( wp_strip_all_tags( $field->post_content ) ) );

				// ... and options.
				if ( ! empty( $field->options ) ) {
					update_post_meta( $field_id, 'bpge_field_options', $field->options );
				}
			}
		}

		restore_current_blog();

		wp_safe_redirect( $redirect );
		die;
	}

	/**
	 * Notices about user actions.
	 *
	 * @param string $type
	 */
	public function notices( $type ) {

		switch ( $type ) {
			case 'settings_updated':
				bp_core_add_message( esc_html__( 'Group Extras settings were successfully updated.', 'buddypress-groups-extras' ) );
				break;

			case 'added_field':
				bp_core_add_message( esc_html__( 'New field was successfully added.', 'buddypress-groups-extras' ) );
				break;

			case 'edited_field':
				bp_core_add_message( esc_html__( 'The field was successfully updated.', 'buddypress-groups-extras' ) );
				break;

			case 'added_page':
				bp_core_add_message( esc_html__( 'New page was successfully added.', 'buddypress-groups-extras' ) );
				break;

			case 'edited_page':
				bp_core_add_message( esc_html__( 'The page was successfully updated.', 'buddypress-groups-extras' ) );
				break;

			case 'no_fields':
				echo '<div class="" id="message"><p>' . esc_html__( 'Please create at least 1 extra field to show it in a list.', 'buddypress-groups-extras' ) . '</p></div>';
				break;

			case 'no_pages':
				echo '<div class="" id="message"><p>' . esc_html__( 'Please create at least 1 extra page to show it in a list.', 'buddypress-groups-extras' ) . '</p></div>';
				break;
		}

		do_action( 'bpge_notices', $type );
	}

	/**
	 * Create a storage for groups pages.
	 *
	 * @param string $what
	 * @param bool   $input
	 *
	 * @return int|null|WP_Post
	 */
	public function get_gpage_by( $what, $input = false ) {

		$bp = buddypress();

		$data = null;

		switch_to_blog( bpge_get_main_site_id() );

		switch ( $what ) {
			case 'group_id':
				global $current_blog;
				if ( empty( $current_blog ) || ! isset( $current_blog->blog_id ) ) {
					$current_blog          = new stdClass();
					$current_blog->blog_id = 1;
				}
				$admin    = get_user_by( 'email', get_blog_option( $current_blog->blog_id, 'admin_email' ) );
				$old_data = (array) groups_get_groupmeta( bp_get_current_group_id(), 'bpge' );

				// create a gpage...
				$old_data['gpage_id'] = wp_insert_post(
					[
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_author'    => $admin->ID,
						'post_content'   => $bp->groups->current_group->description,
						'post_name'      => $bp->groups->current_group->slug,
						'post_status'    => 'publish',
						'post_title'     => $bp->groups->current_group->name,
						'post_type'      => $this->page_slug,
					]
				);
				// ...and save it to reuse later
				groups_update_groupmeta( bp_get_current_group_id(), 'bpge', $old_data );

				$data = $old_data['gpage_id'];
				break;

			case 'id':
				$data = get_post( $input );
				break;
		}

		restore_current_blog();

		return $data;
	}

	/**
	 * Modify form action in group admin area on front-end, so admin won't be redirected to the General settings.
	 *
	 * @param $link
	 * @param $group
	 *
	 * @return string
	 */
	public function edit_group_admin_form_action( $link, $group ) {

		$bp = buddypress();

		$post_fix = '';
		if ( $bp->current_action === 'admin' && ! empty( $bp->action_variables[1] ) ) {
			$post_fix = '/' . sanitize_key( $bp->action_variables[1] );
		}

		return $link . $post_fix;
	}

	/************************************************************************/

	/**
	 * Creation step - enter the data.
	 *
	 * @param null $group_id
	 */
	public function create_screen( $group_id = null ) {

		do_action( 'bpge_create_screen', $this );
	}

	/**
	 * Creation step - save the data.
	 *
	 * @param int|null $group_id
	 */
	public function create_screen_save( $group_id = null ) {

		do_action( 'bpge_create_save', $this );
	}

	/**
	 * Load if was not already loaded.
	 *
	 * @return BPGE
	 */
	public static function getInstance() {

		if ( ! self::$instance ) {
			self::$instance = new BPGE();
		}

		return self::$instance;
	}
}

bp_register_group_extension( 'BPGE' );
