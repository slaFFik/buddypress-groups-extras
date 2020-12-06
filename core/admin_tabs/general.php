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
