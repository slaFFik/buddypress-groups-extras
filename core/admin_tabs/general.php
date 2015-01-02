<?php
if ( ! class_exists( 'BPGE_ADMIN_GENERAL' ) ) {

	/**
	 * General BPGE options, nothing too special
	 */
	class BPGE_ADMIN_GENERAL extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		var $position = 10;
		// slug that is used in url to access this tab
		var $slug = 'general';
		// title is used as a tab name
		var $title = null;

		function __construct() {
			$this->title = __( 'General Options', 'bpge' );

			parent::__construct();
		}

		/**
		 * Create sections of options
		 */
		function register_sections() {
			do_action( 'bpge_admin_general_section_before', $this );

			add_settings_field( 're_pages',
			                    __( 'Rich Editor for Pages', 'bpge' ),
			                    array( $this, 'display_re_pages' ),
			                    $this->slug,
			                    $this->slug . '_settings' );
			add_settings_field( 're_fields',
			                    __( 'Rich Editor for Fields', 'bpge' ),
			                    array( $this, 'display_re_fields' ),
			                    $this->slug,
			                    $this->slug . '_settings' );
			add_settings_field( 'access',
			                    __( 'User Access', 'bpge' ),
			                    array( $this, 'display_access' ),
			                    $this->slug,
			                    $this->slug . '_settings' );
			add_settings_field( 'import',
			                    __( 'Data Import', 'bpge' ),
			                    array( $this, 'display_import' ),
			                    $this->slug,
			                    $this->slug . '_settings' );
			add_settings_field( 'uninstall',
			                    __( 'Uninstall Options', 'bpge' ),
			                    array( $this, 'display_uninstall' ),
			                    $this->slug,
			                    $this->slug . '_settings' );

			do_action( 'bpge_admin_general_section_after', $this );
		}

		/**
		 * Display the tab description
		 */
		function display() {
			echo '<p class="description">' . __( 'Here are some general settings.', 'bpge' ) . '</p>';
		}

		/**
		 * Change accessibility of Extras group admin tab
		 */
		function display_access() {
			?>
			<p>
				<?php _e( 'Sometimes we want to change the access level to different parts of a site. Options below will help you to do this.', 'bpge' );?>
			</p>

			<p><?php _e( 'Who can open group admin tab Extras?', 'bpge' ); ?></p>
			<?php
			if ( ! isset( $this->bpge['access_extras'] ) || empty( $this->bpge['access_extras'] ) ) {
				$this->bpge['access_extras'] = 'g_s_admin';
			}
			?>
			<ul>
				<li><label>
						<input name="bpge_access_extras" type="radio"
						       value="s_admin" <?php checked( 's_admin', $this->bpge['access_extras'] ); ?> />&nbsp;
						<?php _e( 'Site admins only', 'bpge' ); ?>
					</label></li>
				<li><label>
						<input name="bpge_access_extras" type="radio"
						       value="g_s_admin" <?php checked( 'g_s_admin', $this->bpge['access_extras'] ); ?> />&nbsp;
						<?php _e( 'Group administrators and site admins', 'bpge' ); ?>
					</label></li>
			</ul>

		<?php
		}

		/**
		 * Data import from versions before BPGE v3.4
		 */
		function display_import() { ?>
			<p>
				<?php _e( 'If you upgraded from any version of BuddyPress Groups Extras, which had the version number less than 3.4, and if you want to preserve all previously generated content (like default and groups fields etc) please do the import using controls below.', 'bpge' );?>
			</p>

			<p class="description"><?php _e( '<strong>Important</strong>: Do not import data twice - as this will create lots of duplicated fields.', 'bpge' ); ?></p>

			<p>
				<input type="submit" name="bpge-import-data" value="<?php _e( 'Import Data', 'bpge' ); ?>"
				       class="button-secondary"/> &nbsp;
				<input type="submit" name="bpge-clear-data" value="<?php _e( 'Clear Data', 'bpge' ); ?>"
				       class="button"/>
			</p>

			<p class="description"><?php _e( 'Note: Clearing data will delete everything except options on this page.', 'bpge' ); ?></p>
		<?php
		}

		/**
		 * Rich Editor for Pages content
		 */
		function display_re_pages() {
			echo '<p>';
			_e( 'Would you like to enable Rich Editor for easy use of html tags for groups custom pages?', 'bpge' );
			echo '</p>';

			echo '<ul>';
			echo '<li><label><input type="radio" name="bpge_re" ' . ( $this->bpge['re'] == 1 ? 'checked="checked"' : '' ) . ' value="1">&nbsp' . __( 'Enable', 'bpge' ) . '</label></li>';
			echo '<li><label><input type="radio" name="bpge_re" ' . ( $this->bpge['re'] != 1 ? 'checked="checked"' : '' ) . ' value="0">&nbsp' . __( 'Disable', 'bpge' ) . '</label></li>';
			echo '</ul>';
		}

		/**
		 * Rich Editor for Fields textareas
		 */
		function display_re_fields() {
			echo '<p>';
			_e( 'Would you like to enable Rich Editor for easy use of html tags for groups custom textarea fields?', 'bpge' );
			echo '</p>';

			if ( ! isset( $this->bpge['re_fields'] ) || empty( $this->bpge['re_fields'] ) ) {
				$this->bpge['re_fields'] = 'no';
			}

			echo '<ul>';
			echo '<li><label><input type="radio" name="bpge_re_fields" ' . checked( $this->bpge['re_fields'], 'yes', false ) . ' value="yes">&nbsp' . __( 'Enable', 'bpge' ) . '</label></li>';
			echo '<li><label><input type="radio" name="bpge_re_fields" ' . checked( $this->bpge['re_fields'], 'no', false ) . ' value="no">&nbsp' . __( 'Disable', 'bpge' ) . '</label></li>';
			echo '</ul>';
		}

		/**
		 * Plugin Deactivation options
		 */
		function display_uninstall() {
			echo '<p>';
			_e( 'On BPGE deactivation you can delete or preserve all its settings and created content (like groups pages and fields). What do you want to do?', 'bpge' );
			echo '</p>';

			if ( ! isset( $this->bpge['uninstall'] ) ) {
				$this->bpge['uninstall'] = 'no';
			}

			echo '<p>';
			echo '<label><input type="radio" name="bpge_uninstall" ' . ( $this->bpge['uninstall'] == 'no' ? 'checked="checked"' : '' ) . ' value="no">&nbsp' . __( 'Preserve all data', 'bpge' ) . '</label><br />';
			echo '<label><input type="radio" name="bpge_uninstall" ' . ( $this->bpge['uninstall'] == 'yes' ? 'checked="checked"' : '' ) . ' value="yes">&nbsp' . __( 'Delete everything', 'bpge' ) . '</label>';
			echo '</p>';
		}

		/**
		 * Validate and save
		 */
		function save() {
			/** @var $wpdb WPDB */
			global $wpdb, $bp;

			switch_to_blog( bpge_get_main_site_id() );

			if ( isset( $_POST['bpge_re'] ) ) {
				$this->bpge['re']            = $_POST['bpge_re'];
				$this->bpge['re_fields']     = $_POST['bpge_re_fields'];
				$this->bpge['uninstall']     = $_POST['bpge_uninstall'];
				$this->bpge['access_extras'] = $_POST['bpge_access_extras'];

				bp_update_option( 'bpge', $this->bpge );
			}

			if ( isset( $_POST['bpge-import-data'] ) ) {
				/**
				 * Default fields
				 */
				// get list of set of fields
				$set_fields = $wpdb->get_results(
					"SELECT option_name AS `slug`, option_value AS `set`
                            FROM {$wpdb->options}
                            WHERE option_name LIKE 'bpge-set-%%'" );

				// reformat data
				if ( ! empty( $set_fields ) ) {
					foreach ( $set_fields as &$set ) {
						$set->set = maybe_unserialize( $set->set );
					}
				}

				// process the import part 1
				foreach ( (array) $set_fields as $set ) {
					// save the set
					$set_id = wp_insert_post( array(
						                          'post_type'    => BPGE_FIELDS_SET,
						                          'post_status'  => 'publish',
						                          'post_title'   => $set->set['name'],
						                          'post_content' => $set->set['desc']
					                          ) );

					// now we need to save fields in that set
					if ( is_integer( $set_id ) ) {
						foreach ( (array) $set->set['fields'] as $field ) {
							$field_id = wp_insert_post( array(
								                            'post_type'    => BPGE_FIELDS,
								                            'post_parent'  => $set_id, // assign to a set of fields
								                            'post_title'   => $field['name'],
								                            'post_content' => $field['desc'],
								                            'post_excerpt' => $field['type'],
								                            'post_status'  => 'publish'
							                            ) );
							// and save options if any
							if ( isset( $field['options'] ) && ! empty( $field['options'] ) ) {
								$options = array();
								foreach ( $field['options'] as $option ) {
									$options[] = htmlspecialchars( strip_tags( $option['name'] ) );
								}
								update_post_meta( $field_id, 'bpge_field_options', $options );
							}
						}
					}
				}

				/**
				 * Groups fields
				 */
				// get list of groups that have gFields from  groupmeta
				$gFields = $wpdb->get_row( $wpdb->prepare(
					"SELECT group_id, meta_value AS `fields`
                            FROM {$bp->table_prefix}bp_groups_groupmeta
                            WHERE meta_key = 'bpge_fields'", __return_false()
				) );

				// reformat data
				if ( ! empty( $gFields ) && ! empty( $gFields->fields ) ) {
					$gFields->fields = json_decode( $gFields->fields );
				}

				$i = 100;
				if ( ! empty( $gFields->fields ) && is_array( $gFields->fields ) ) {
					foreach ( $gFields->fields as $field ) {
						$new_field = array(
							'post_title'   => $field->title,
							'post_excerpt' => $field->type,
							'pinged'       => $field->required,
							'post_status'  => $field->display == '1' ? 'publish' : 'draft',
							'post_parent'  => $gFields->group_id,
							'post_type'    => BPGE_GFIELDS,
							'menu_order'   => $i,
						);

						$new_field = apply_filters( 'bpge_new_field', $new_field );

						$options = array();
						if ( isset( $field->options ) && ! empty( $field->options ) ) {
							foreach ( $field->options as $option ) {
								$options[] = htmlspecialchars( strip_tags( $option ) );
							}
						}

						// Save Field
						$field_id = wp_insert_post( $new_field );

						if ( is_integer( $field_id ) ) {
							// Save field options
							update_post_meta( $field_id, 'bpge_field_options', $options );

							$field_desc = apply_filters( 'bpge_new_field_desc', $field->desc );
							update_post_meta( $field_id, 'bpge_field_desc', $field_desc );
						}
						$i ++;
					}
				}
			}

			restore_current_blog();

			// Remove everything plugin-related except options
			if ( isset( $_POST['bpge-clear-data'] ) ) {
				bpge_clear( false );
			}
		}

	}

	/**
	 * Now we need to init this class
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_GENERAL;
	}

}