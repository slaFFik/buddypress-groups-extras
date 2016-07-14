<?php
if ( ! class_exists( 'BPGE_ADMIN_TUTS' ) ) {

	/**
	 *
	 */
	class BPGE_ADMIN_TUTS extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		public $position = 40;
		// slug that is used in url to access this tab
		public $slug = 'tuts';
		// title is used as a tab name
		public $title = null;

		public function __construct() {
			$this->title = __( 'Tutorials', 'buddypress-groups-extras' );

			parent::__construct();
		}

		public function display() {
			echo '<p class="description">';
			_e( 'Below you will see several tutorials and how to, that will help you to better understand the plugin and how to work with it.', 'buddypress-groups-extras' );
			echo '</p>';

			bpge_view( 'admin/tuts_data' );
		}
	}

	/**
	 * Now we need to init this class
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_TUTS;
	}

}
