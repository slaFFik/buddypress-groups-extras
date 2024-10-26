<?php

if ( ! class_exists( 'BPGE_ADMIN_POLL' ) ) {
	/**
	 * Class BPGE_ADMIN_POLL.
	 */
	class BPGE_ADMIN_POLL extends BPGE_ADMIN_TAB {

		/**
		 * Position is used to define where exactly this tab will appear.
		 *
		 * @var int
		 */
		public $position = 90;

		/**
		 * Slug that is used in url to access this tab.
		 *
		 * @var string
		 */
		public $slug = 'poll';

		/**
		 * Title is used as a tab name.
		 *
		 * @var string
		 */
		public $title = '';

		/**
		 * BPGE_ADMIN_POLL constructor.
		 */
		public function __construct() {

			$this->title = esc_html__( 'Poll', 'buddypress-groups-extras' );

			parent::__construct();
		}

		/**
		 * Render the content of the tab.
		 */
		public function display() {

			?>

			<p>
				<?php esc_html_e( 'Please answer the question below - this will help me to prioritize my development work.', 'buddypress-groups-extras' ); ?>
			</p>

			<style>.submit{display:none}</style>

			<!--suppress JSUnresolvedLibraryURL -->
			<?php // phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript ?>
			<script type="text/javascript" charset="utf-8" referrerpolicy="no-referrer" src="https://static.polldaddy.com/p/7122239.js"></script>

			<?php
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_POLL();
	}
}
