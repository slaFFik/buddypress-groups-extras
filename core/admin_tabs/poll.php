<?php
if ( ! class_exists( 'BPGE_ADMIN_POLL' ) ) {

	/**
	 *
	 */
	class BPGE_ADMIN_POLL extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		public $position = 90;
		// slug that is used in url to access this tab
		public $slug = 'poll';
		// title is used as a tab name
		public $title = null;

		public function __construct() {
			$this->title = __( 'Poll', 'buddypress-groups-extras' );

			parent::__construct();
		}

		public function display() {
			echo '<p class="description">';
			_e( 'Please answer the question below - this will help me to prioritize my development work.', 'buddypress-groups-extras' );
			echo '</p><br />';

			// hide Submit button
			echo '<style>.submit{display:none}</style>';

			echo '<script type="text/javascript" charset="utf-8" src="http://static.polldaddy.com/p/7122239.js"></script>';
		}
	}

	/**
	 * Now we need to init this class
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_POLL;
	}

}
