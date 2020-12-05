<?php

if ( ! class_exists( 'BPGE_ADMIN_GROUPS' ) ) {
	/**
	 * Class BPGE_ADMIN_GROUPS.
	 */
	class BPGE_ADMIN_GROUPS extends BPGE_ADMIN_TAB {

		// Position is used to define where exactly this tab will appear.
		public $position = 30;
		// Slug that is used in url to access this tab.
		public $slug = 'groups';
		// Title is used as a tab name.
		public $title = null;

		public function __construct() {

			$this->title = esc_html__( 'Allowed Groups', 'buddypress-groups-extras' );

			parent::__construct();
		}

		public function display() {

			$arg['type']     = 'alphabetical';
			$arg['per_page'] = '1000';
			bpge_view(
				'admin/groups_list',
				array(
					'arg'  => $arg,
				)
			);
		}

		public function save() {

			$this->bpge['groups'] = isset( $_POST['bpge_groups'] ) && is_array( $_POST['bpge_groups'] ) ? array_map( 'intval', $_POST['bpge_groups'] ) : 'all';
			bp_update_option( 'bpge', $this->bpge );
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_GROUPS;
	}
}
