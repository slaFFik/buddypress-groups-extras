<?php
if ( ! class_exists( 'BPGE_ADMIN_POLL' ) ) {

	/**
	 *
	 */
	class BPGE_ADMIN_POLL extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		var $position = 90;
		// slug that is used in url to access this tab
		var $slug = 'poll';
		// title is used as a tab name
		var $title = null;

		function __construct() {
			$this->title = __( 'Poll', 'bpge' );

			parent::__construct();
		}

		function display() {
			echo '<p class="description">';
			_e( 'Please answer the question below - this will help me to prioritize my development work.', 'bpge' );
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