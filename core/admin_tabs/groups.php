<?php

if ( ! class_exists( 'BPGE_ADMIN_GROUPS' ) ) {
	/**
	 * Class BPGE_ADMIN_GROUPS.
	 */
	class BPGE_ADMIN_GROUPS extends BPGE_ADMIN_TAB {

		/**
		 * Position is used to define where exactly this tab will appear.
		 *
		 * @var int
		 */
		public $position = 30;

		/**
		 * Slug that is used in url to access this tab.
		 *
		 * @var string
		 */
		public $slug = 'groups';

		/**
		 * Title is used as a tab name.
		 *
		 * @var string
		 */
		public $title = '';

		/**
		 * BPGE_ADMIN_GROUPS constructor.
		 */
		public function __construct() {

			$this->title = esc_html__( 'Allowed Groups', 'buddypress-groups-extras' );

			parent::__construct();
		}

		/**
		 * Render the content of the tab.
		 */
		public function display() {

			$arg['type']     = 'alphabetical';
			$arg['per_page'] = '1000';
			?>

			<p>
				<?php esc_html_e( 'Specify which groups will have access to the custom fields and custom pages functionality.', 'buddypress-groups-extras' ); ?>
			</p>

			<?php
			bpge_view(
				'admin/groups_list',
				[
					'arg' => $arg,
				]
			);

			wp_nonce_field( 'bpge_manage_allowed_groups_nonce', 'bpge_manage_allowed_groups_nonce' );
		}

		/**
		 * Save the content of the tab.
		 */
		public function save() {

			check_admin_referer( 'bpge_manage_allowed_groups_nonce', 'bpge_manage_allowed_groups_nonce' );

			$this->bpge['groups'] = isset( $_POST['bpge_groups'] ) && is_array( $_POST['bpge_groups'] ) ? array_map( 'intval', $_POST['bpge_groups'] ) : 'all';

			bp_update_option( 'bpge', $this->bpge );
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_GROUPS();
	}
}
