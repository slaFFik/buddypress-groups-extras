<?php

if ( ! class_exists( 'BPGE_ADMIN_POLL' ) ) {
	/**
	 * Class BPGE_ADMIN_POLL.
	 */
	class BPGE_ADMIN_POLL extends BPGE_ADMIN_TAB {

		// Position is used to define where exactly this tab will appear.
		public $position = 90;
		// Slug that is used in url to access this tab.
		public $slug = 'poll';
		// Title is used as a tab name.
		public $title = null;

		public function __construct() {

			$this->title = esc_html__( 'Poll', 'buddypress-groups-extras' );

			parent::__construct();
		}

		public function display() {

			echo '<p class="description">';
			esc_html_e( 'Please answer the question below - this will help me to prioritize my development work.', 'buddypress-groups-extras' );
			echo '</p><br />';

			// Hide Submit button.
			echo '<style>.submit{display:none}</style>';

			echo '<script type="text/javascript" charset="utf-8" src="https://static.polldaddy.com/p/7122239.js"></script>';
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_POLL;
	}
}
