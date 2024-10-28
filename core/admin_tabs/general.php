<?php

if ( ! class_exists( 'BPGE_ADMIN_GENERAL' ) ) {
	/**
	 * General BPGE options, nothing too special.
	 */
	class BPGE_ADMIN_GENERAL extends BPGE_ADMIN_TAB {

		/**
		 * Position is used to define where exactly this tab will appear.
		 *
		 * @var int
		 */
		public $position = 10;

		/**
		 * Slug that is used in url to access this tab.
		 *
		 * @var string
		 */
		public $slug = 'general';

		/**
		 * Title is used as a tab name.
		 *
		 * @var string
		 */
		public $title = '';

		/**
		 * BPGE_ADMIN_GENERAL constructor.
		 */
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
				[ $this, 'display_re_pages' ],
				$this->slug,
				$this->slug . '_settings'
			);

			add_settings_field(
				're_fields',
				esc_html__( 'Rich Editor for Fields', 'buddypress-groups-extras' ),
				[ $this, 'display_re_fields' ],
				$this->slug,
				$this->slug . '_settings'
			);

			add_settings_field(
				'access',
				esc_html__( 'Groups "Extras" Access', 'buddypress-groups-extras' ),
				[ $this, 'display_access' ],
				$this->slug,
				$this->slug . '_settings'
			);

			add_settings_field(
				'groups_nav_reorder',
				esc_html__( 'Groups Nav Reorder', 'buddypress-groups-extras' ),
				[ $this, 'display_groups_nav_reorder' ],
				$this->slug,
				$this->slug . '_settings'
			);

			add_settings_field(
				'uninstall',
				esc_html__( 'Plugin Deactivation', 'buddypress-groups-extras' ),
				[ $this, 'display_uninstall' ],
				$this->slug,
				$this->slug . '_settings'
			);

			do_action( 'bpge_admin_general_section_after', $this );
		}

		/**
		 * Display the tab description.
		 */
		public function display() {

			echo '<p>' . esc_html__( 'Plugin-wide settings can be modified on this page.', 'buddypress-groups-extras' ) . '</p>';

			wp_nonce_field( 'bpge_manage_general_options_nonce', 'bpge_manage_general_options_nonce' );
		}

		/**
		 * Rich Editor for Pages content.
		 */
		public function display_re_pages() {

			?>

			<p>
				<?php esc_html_e( 'Would you like to enable Rich Editor for easy use of HTML tags for groups custom pages?', 'buddypress-groups-extras' ); ?>
			</p>

			<ul>
				<li>
					<label>
						<input type="radio" name="bpge_re" <?php checked( $this->bpge['re'], 1 ); ?> value="1">
						<?php esc_html_e( 'Enable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="bpge_re" <?php checked( $this->bpge['re'], 0 ); ?> value="0">
						<?php esc_html_e( 'Disable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}

		/**
		 * Rich Editor for Fields textareas.
		 */
		public function display_re_fields() {

			?>

			<p>
				<?php esc_html_e( 'Would you like to enable Rich Editor for easy use of HTML tags for groups custom textarea fields?', 'buddypress-groups-extras' ); ?>
			</p>

			<?php
			if ( ! isset( $this->bpge['re_fields'] ) || empty( $this->bpge['re_fields'] ) ) {
				$this->bpge['re_fields'] = 'no';
			}
			?>

			<ul>
				<li>
					<label>
						<input type="radio" name="bpge_re_fields" <?php checked( $this->bpge['re_fields'], 'yes' ); ?> value="yes">
						<?php esc_html_e( 'Enable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="bpge_re_fields" <?php checked( $this->bpge['re_fields'], 'no' ); ?> value="no">
						<?php esc_html_e( 'Disable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}


		/**
		 * Change accessibility of Extras group admin tab.
		 */
		public function display_access() {

			?>

			<p>
				<?php esc_html_e( 'Who can view the group admin tab "Extras" and manage group-level fields and pages?', 'buddypress-groups-extras' ); ?>
			</p>

			<?php
			if ( ! isset( $this->bpge['access_extras'] ) || empty( $this->bpge['access_extras'] ) ) {
				$this->bpge['access_extras'] = 'g_s_admin';
			}
			?>

			<ul>
				<li>
					<label>
						<input name="bpge_access_extras" type="radio" value="s_admin" <?php checked( 's_admin', $this->bpge['access_extras'] ); ?> />
						<?php esc_html_e( 'Site admins only', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input name="bpge_access_extras" type="radio" value="g_s_admin" <?php checked( 'g_s_admin', $this->bpge['access_extras'] ); ?> />
						<?php esc_html_e( 'Group administrators and site admins', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}

		/**
		 * Control whether group admins can reorder group tabs.
		 */
		public function display_groups_nav_reorder() {

			?>

			<p>
				<?php esc_html_e( 'Should group admins be able to reorder group navigation items?', 'buddypress-groups-extras' ); ?>
			</p>

			<?php
			if ( empty( $this->bpge['access_nav_reorder'] ) ) {
				$this->bpge['access_nav_reorder'] = 'yes';
			}
			?>

			<ul>
				<li>
					<label>
						<input name="bpge_access_nav_reorder" type="radio" value="yes" <?php checked( 'yes', $this->bpge['access_nav_reorder'] ); ?> />
						<?php esc_html_e( 'Enable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input name="bpge_access_nav_reorder" type="radio" value="no" <?php checked( 'no', $this->bpge['access_nav_reorder'] ); ?> />
						<?php esc_html_e( 'Disable', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}

		/**
		 * Plugin Deactivation options.
		 */
		public function display_uninstall() {

			?>

			<p>
				<?php esc_html_e( 'On BPGE deactivation you can delete or preserve all its settings and created content (like groups pages and fields). What do you want to do?', 'buddypress-groups-extras' ); ?>
			</p>

			<?php
			if ( ! isset( $this->bpge['uninstall'] ) ) {
				$this->bpge['uninstall'] = 'no';
			}
			?>

			<ul>
				<li>
					<label>
						<input type="radio" name="bpge_uninstall" <?php checked( $this->bpge['uninstall'], 'no' ); ?> value="no">
						<?php esc_html_e( 'Preserve all data', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
				<li>
					<label>
						<input type="radio" name="bpge_uninstall" <?php checked( $this->bpge['uninstall'], 'yes' ); ?> value="yes">
						<?php esc_html_e( 'Delete everything', 'buddypress-groups-extras' ); ?>
					</label>
				</li>
			</ul>

			<?php
		}

		/**
		 * Validate and save.
		 */
		public function save() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

			check_admin_referer( 'bpge_manage_general_options_nonce', 'bpge_manage_general_options_nonce' );

			switch_to_blog( bpge_get_main_site_id() );

			if ( isset( $_POST['bpge_re'] ) ) {
				$this->bpge['re']                 = absint( $_POST['bpge_re'] ?? 0 );
				$this->bpge['re_fields']          = absint( $_POST['bpge_re_fields'] ?? 0 );
				$this->bpge['access_extras']      = sanitize_key( $_POST['bpge_access_extras'] ?? 'g_s_admin' );
				$this->bpge['access_nav_reorder'] = sanitize_key( $_POST['bpge_access_nav_reorder'] ?? 'yes' );
				$this->bpge['uninstall']          = sanitize_key( $_POST['bpge_uninstall'] ?? 'no' );

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
		return new BPGE_ADMIN_GENERAL();
	}
}
