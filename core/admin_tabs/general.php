<?php

if ( ! class_exists( 'BPGE_ADMIN_GENERAL' ) ) {
	/**
	 * General BPGE options, nothing too special.
	 */
	class BPGE_ADMIN_GENERAL extends BPGE_ADMIN_TAB {

		// Position is used to define where exactly this tab will appear.
		public $position = 10;
		// Slug that is used in url to access this tab.
		public $slug = 'general';
		// Title is used as a tab name.
		public $title = null;

		public function __construct() {

			$this->title = esc_html__( 'General Options', 'buddypress-groups-extras' );

			parent::__construct();
		}

		/**
		 * Create sections of options.
		 */
		public function register_sections() {

			do_action( 'bpge_admin_general_section_before', $this );

			add_settings_field(
				're_pages',
				esc_html__( 'Rich Editor for Pages', 'buddypress-groups-extras' ),
				array( $this, 'display_re_pages' ),
				$this->slug,
				$this->slug . '_settings'
			);
			add_settings_field(
				're_fields',
				esc_html__( 'Rich Editor for Fields', 'buddypress-groups-extras' ),
				array( $this, 'display_re_fields' ),
				$this->slug,
				$this->slug . '_settings'
			);
			add_settings_field(
				'access',
				esc_html__( 'User Access', 'buddypress-groups-extras' ),
				array( $this, 'display_access' ),
				$this->slug,
				$this->slug . '_settings'
			);
			add_settings_field(
				'import',
				esc_html__( 'Data Import', 'buddypress-groups-extras' ),
				array( $this, 'display_import' ),
				$this->slug,
				$this->slug . '_settings'
			);
			add_settings_field(
				'uninstall',
				esc_html__( 'Uninstall Options', 'buddypress-groups-extras' ),
				array( $this, 'display_uninstall' ),
				$this->slug,
				$this->slug . '_settings'
			);

			do_action( 'bpge_admin_general_section_after', $this );
		}

		/**
		 * Display the tab description.
		 */
		public function display() {

			echo '<p class="description">' . esc_html__( 'Here are some general settings.', 'buddypress-groups-extras' ) . '</p>';
		}

		/**
		 * Change accessibility of Extras group admin tab.
		 */
		public function display_access() {

			?>
			<p>
				<?php esc_html_e( 'Sometimes we want to change the access level to different parts of a site. Options below will help you to do this.', 'buddypress-groups-extras' ); ?>
			</p>

			<p><?php esc_html_e( 'Who can open group admin tab Extras?', 'buddypress-groups-extras' ); ?></p>
			<?php
			if ( ! isset( $this->bpge['access_extras'] ) || empty( $this->bpge['access_extras'] ) ) {
				$this->bpge['access_extras'] = 'g_s_admin';
			}
			?>
			<ul>
				<li>
					<label>
						<input name="bpge_access_extras" type="radio" value="s_admin" <?php checked( 's_admin', $this->bpge['access_extras'] ); ?> />&nbsp;
						<?php esc_html_e( 'Site admins only', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input name="bpge_access_extras" type="radio" value="g_s_admin" <?php checked( 'g_s_admin', $this->bpge['access_extras'] ); ?> />&nbsp;
						<?php esc_html_e( 'Group administrators and site admins', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}

		/**
		 * Data import from versions before BPGE v3.4.
		 */
		public function display_import() { ?>
			<p>
				<?php esc_html_e( 'If you upgraded from any version of BuddyPress Groups Extras, which had the version number less than 3.4, and if you want to preserve all previously generated content (like default and groups fields etc) please do the import using controls below.', 'buddypress-groups-extras' ); ?>
			</p>

			<p class="description"><?php esc_html_e( 'IMPORTANT: Do not import data twice - as this will create lots of duplicated fields.', 'buddypress-groups-extras' ); ?></p>

			<p>
				<input type="submit" name="bpge-import-data" value="<?php esc_html_e( 'Import Data', 'buddypress-groups-extras' ); ?>"
					class="button-secondary" /> &nbsp;
				<input type="submit" name="bpge-clear-data" value="<?php esc_html_e( 'Clear Data', 'buddypress-groups-extras' ); ?>"
					class="button" />
			</p>

			<p class="description"><?php esc_html_e( 'Note: Clearing data will delete everything except options on this page.', 'buddypress-groups-extras' ); ?></p>
			<?php
		}

		/**
		 * Rich Editor for Pages content.
		 */
		public function display_re_pages() {

			echo '<p>';
			esc_html_e( 'Would you like to enable Rich Editor for easy use of html tags for groups custom pages?', 'buddypress-groups-extras' );
			echo '</p>';

			echo '<ul>';
			echo '<li><label><input type="radio" name="bpge_re" ' . ( (int) $this->bpge['re'] === 1 ? 'checked="checked"' : '' ) . ' value="1">&nbsp' . esc_html__( 'Enable', 'buddypress-groups-extras' ) . '</label></li>';
			echo '<li><label><input type="radio" name="bpge_re" ' . ( (int) $this->bpge['re'] !== 1 ? 'checked="checked"' : '' ) . ' value="0">&nbsp' . esc_html__( 'Disable', 'buddypress-groups-extras' ) . '</label></li>';
			echo '</ul>';
		}

		/**
		 * Rich Editor for Fields textareas.
		 */
		public function display_re_fields() {

			echo '<p>';
			_e( 'Would you like to enable Rich Editor for easy use of html tags for groups custom textarea fields?', 'buddypress-groups-extras' );
			echo '</p>';

			if ( ! isset( $this->bpge['re_fields'] ) || empty( $this->bpge['re_fields'] ) ) {
				$this->bpge['re_fields'] = 'no';
			}

			echo '<ul>';
			echo '<li><label><input type="radio" name="bpge_re_fields" ' . checked( $this->bpge['re_fields'], 'yes', false ) . ' value="yes">&nbsp' . esc_html__( 'Enable', 'buddypress-groups-extras' ) . '</label></li>';
			echo '<li><label><input type="radio" name="bpge_re_fields" ' . checked( $this->bpge['re_fields'], 'no', false ) . ' value="no">&nbsp' . esc_html__( 'Disable', 'buddypress-groups-extras' ) . '</label></li>';
			echo '</ul>';
		}

		/**
		 * Plugin Deactivation options.
		 */
		public function display_uninstall() {

			echo '<p>';
			esc_html_e( 'On BPGE deactivation you can delete or preserve all its settings and created content (like groups pages and fields). What do you want to do?', 'buddypress-groups-extras' );
			echo '</p>';

			if ( ! isset( $this->bpge['uninstall'] ) ) {
				$this->bpge['uninstall'] = 'no';
			}

			echo '<p>';
			echo '<label><input type="radio" name="bpge_uninstall" ' . ( $this->bpge['uninstall'] === 'no' ? 'checked="checked"' : '' ) . ' value="no">&nbsp' . esc_html__( 'Preserve all data', 'buddypress-groups-extras' ) . '</label><br />';
			echo '<label><input type="radio" name="bpge_uninstall" ' . ( $this->bpge['uninstall'] === 'yes' ? 'checked="checked"' : '' ) . ' value="yes">&nbsp' . esc_html__( 'Delete everything', 'buddypress-groups-extras' ) . '</label>';
			echo '</p>';
		}

		/**
		 * Validate and save.
		 */
		public function save() {

			global $wpdb;
			$bp = buddypress();

			switch_to_blog( bpge_get_main_site_id() );

			if ( isset( $_POST['bpge_re'] ) ) {
				$this->bpge['re']            = (int) $_POST['bpge_re'];
				$this->bpge['re_fields']     = (int) $_POST['bpge_re_fields'];
				$this->bpge['uninstall']     = sanitize_key( $_POST['bpge_uninstall'] );
				$this->bpge['access_extras'] = sanitize_key( $_POST['bpge_access_extras'] );

				bp_update_option( 'bpge', $this->bpge );
			}

			if ( isset( $_POST['bpge-import-data'] ) ) {
				/**
				 * Default fields.
				 */
				// Get list of set of fields.
				$set_fields = $wpdb->get_results(
					"SELECT option_name AS `slug`, option_value AS `set`
                            FROM {$wpdb->options}
                            WHERE option_name LIKE 'bpge-set-%%'" );

				// Reformat data.
				if ( ! empty( $set_fields ) ) {
					foreach ( $set_fields as &$set ) {
						$set->set = maybe_unserialize( $set->set );
					}
				}

				// Process the import part 1.
				foreach ( (array) $set_fields as $set ) {
					// Save the set.
					$set_id = wp_insert_post(
						array(
							'post_type'    => BPGE_FIELDS_SET,
							'post_status'  => 'publish',
							'post_title'   => $set->set['name'],
							'post_content' => $set->set['desc'],
						)
					);

					// Now we need to save fields in that set.
					if ( is_int( $set_id ) ) {
						foreach ( (array) $set->set['fields'] as $field ) {
							$field_id = wp_insert_post(
								array(
									'post_type'    => BPGE_FIELDS,
									'post_parent'  => $set_id, // assign to a set of fields.
									'post_title'   => $field['name'],
									'post_content' => $field['desc'],
									'post_excerpt' => $field['type'],
									'post_status'  => 'publish',
								)
							);
							// And save options if any.
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
				 * Groups fields.
				 */
				// get list of groups that have gFields from  groupmeta.
				$gFields = $wpdb->get_row( $wpdb->prepare(
					"SELECT group_id, meta_value AS `fields`
                            FROM {$bp->table_prefix}bp_groups_groupmeta
                            WHERE meta_key = 'bpge_fields'",
					__return_false()
				) );

				// Reformat data.
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
							'post_status'  => (bool) $field->display ? 'publish' : 'draft',
							'post_parent'  => $gFields->group_id,
							'post_type'    => BPGE_GFIELDS,
							'menu_order'   => $i,
						);

						$new_field = apply_filters( 'bpge_new_field', $new_field );

						$options = array();
						if ( isset( $field->options ) && ! empty( $field->options ) ) {
							foreach ( $field->options as $option ) {
								$options[] = htmlspecialchars( wp_strip_all_tags( $option ) );
							}
						}

						// Save Field.
						$field_id = wp_insert_post( $new_field );

						if ( is_int( $field_id ) ) {
							// Save field options.
							update_post_meta( $field_id, 'bpge_field_options', $options );

							$field_desc = apply_filters( 'bpge_new_field_desc', $field->desc );
							update_post_meta( $field_id, 'bpge_field_desc', $field_desc );
						}
						$i ++;
					}
				}
			}

			restore_current_blog();

			// Remove everything plugin-related except options.
			if ( isset( $_POST['bpge-clear-data'] ) ) {
				bpge_clear( false );
			}
		}

	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_GENERAL;
	}
}
