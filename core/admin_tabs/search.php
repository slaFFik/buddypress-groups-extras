<?php
if ( defined( 'BPGE_PRO_SEARCH' ) ) {
	return false;
}

if ( ! class_exists( 'BPGE_ADMIN_SEARCH' ) ) {

	/**
	 *
	 */
	class BPGE_ADMIN_SEARCH extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		var $position = 35;
		// slug that is used in url to access this tab
		var $slug = 'search';
		// title is used as a tab name
		var $title = null;

		function __construct() {
			$this->title = __( 'Search', 'bpge' );

			parent::__construct();
		}

		function display() {
			echo '<p class="">';
			_e( 'Ability to search in groups fields and pages was finally implemented!', 'bpge' );
			echo '</p>';

			echo '<p class="">';
			echo sprintf( __( 'Search extension is a <a href="%s">PRO feature</a>, and costs just $12.', 'bpge' ), 'http://ovirium.com/downloads/bp-groups-extras-pro-search/' );
			echo '</p>';

			echo '<p class="">';
			echo sprintf( __( 'All buyes will get the latest versions among the first AND extra support for their plugin PRO features on a dedicated forum on <a href="%1$s" target="_blank">%2$s</a>', 'bpge' ), 'http://ovirium.com', 'Ovirium.com' );
			echo '</p>';

			echo '<a href="http://ovirium.com/downloads/bp-groups-extras-pro-search/" target="_blank" class="button-primary buy_bpge">' . __( 'Buy Now for just $12<sup>.00</sup>', 'bpge' ) . '</a>';

			echo '<p>Below is the screenshot of what you will get after purchasing the Search extension.</p>';

			echo '<p><img style="padding:5px;border:1px solid #ccc" src="https://dl.dropboxusercontent.com/u/6771917/bpge/bpge_search_promo.jpg" width="931" height="540" title="BP Groups Extras Pro - Search" alt="BP Groups Extras Pro - Search" /></p>';

			echo '<p>And here is how the search results look like.</p>';

			echo '<p><img src="http://content.screencast.com/users/slaFFik/folders/Jing/media/b4fd1e6f-e76d-4cde-8a68-541398de598c/2013-08-24_0157.png" /></p>';

			// hide Submit button
			echo '<style>.submit{display:none}</style>';
		}
	}

	/**
	 * Now we need to init this class
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_SEARCH;
	}

}