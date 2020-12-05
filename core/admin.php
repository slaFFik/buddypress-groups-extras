<?php

/**
 * Main class for WordPress admin area.
 */
class BPGE_ADMIN {

	// Page slug, used on URL.
	public $slug = BPGE_ADMIN_SLUG;
	// Where all options are stored.
	public $bpge = false;
	// Where to save in options table.
	public $bpge_options_key = 'bpge';
	// Default tab that will be opened if nothing specified.
	// Will be redefined after all tabs are loaded.
	public $default_tab = null;
	// The list of tabs in admin area, will be extended by child classes.
	public $bpge_tabs = array();
	// Path the folder where all tabs are situated.
	public $tabs_path = null;

	/**
	 * Do some important initial routine.
	 */
	public function __construct() {

		global $bpge;
		$this->bpge      = $bpge;
		$this->tabs_path = __DIR__ . DS . 'admin_tabs';

		// Own columns in gpages list.
		add_filter( 'manage_' . BPGE_GPAGES . '_posts_columns', array( $this, 'manage_columns' ) );
		add_action( 'manage_' . BPGE_GPAGES . '_posts_custom_column', array( $this, 'manage_columns_content' ), 10, 2 );
		add_filter( 'page_row_actions', array( $this, 'manage_columns_actions' ), 10, 2 );
		add_filter( 'bulk_actions-edit-' . BPGE_GPAGES, array( $this, 'manage_columns_remove_bulk' ) );

		// Create tabs.
		$this->get_tabs();
	}

	/**
	 * Improve the table of Groups pages in wp-admin area.
	 *
	 * @return array
	 */
	public function manage_columns() {

		return array(
			'cb'         => '<input type="checkbox" />',
			'title'      => __( 'Title', 'buddypress-groups-extras' ),
			'group_link' => __( 'Group Links', 'buddypress-groups-extras' ),
			'page_link'  => __( 'Page Links', 'buddypress-groups-extras' ),
			'date'       => __( 'Date', 'buddypress-groups-extras' ),
		);
	}

	public function manage_columns_content( $column, $post_id ) {

		$group_id = get_post_meta( $post_id, 'group_id', true );
		$post     = get_post( $post_id );

		if ( empty( $group_id ) ) {
			$groups_class  = new BP_Groups_Group;
			$groups_handle = $groups_class::get(
				array(
					'search_terms'    => $post->post_title,
					'search_columns'  => array( 'name' ),
					'populate_extras' => false,
					'show_hidden'     => true,
				)
			);

			if ( ! empty( $groups_handle['groups'][0] ) ) {
				$group = $groups_handle['groups'][0];
			}
		} else {
			$group = groups_get_group( array( 'group_id' => $group_id ) );
		}

		if ( empty( $group ) ) {
			return;
		}

		$group_link       = bp_get_group_permalink( $group );
		$group_admin_link = bp_get_group_admin_permalink( $group );

		switch ( $column ) {
			case 'group_link' :
				if ( $post->post_parent !== 0 ) {
					break;
				}

				echo '<a href="' . esc_url( $group_link ) . '" target="_blank">' . __( 'Visit', 'buddypress-groups-extras' ) . '</a>';
				echo ' | ';
				echo '<a href="' . esc_url( $group_admin_link ) . '" target="_blank">' . __( 'Edit', 'buddypress-groups-extras' ) . '</a>';

				break;

			case 'page_link' :
				if ( $post->post_parent === 0 ) {
					break;
				}

				echo '<a href="' . esc_url( $group_link . BPGE_GPAGES . '/' . $post->post_name ) . '/" target="_blank">' . __( 'Visit', 'buddypress-groups-extras' ) . '</a>';
				echo ' | ';
				echo '<a href="' .esc_url( $group_admin_link . 'extras/pages-manage/?edit=' . $post_id ) . '" target="_blank">' . __( 'Edit', 'buddypress-groups-extras' ) . '</a>';
				break;
		}
	}

	public function manage_columns_actions( $actions, $post ) {

		if ( $post->post_type !== BPGE_GPAGES ) {
			return $actions;
		}

		unset( $actions['view'], $actions['trash'], $actions['inline hide-if-no-js'] );

		return $actions;
	}

	/**
	 * Remove all default actions, as we are currently not using them.
	 *
	 * @return array
	 */
	public function manage_columns_remove_bulk() {

		return array();
	}

	/**
	 * Get all tabs from individual files (include).
	 */
	public function get_tabs() {

		if ( $handle = opendir( $this->tabs_path ) ) {
			while ( false !== ( $file = readdir( $handle ) ) ) {
				if ( $file === "." || $file === ".." ) {
					continue;
				}

				/** @noinspection PhpIncludeInspection */
				$tab = include( $this->tabs_path . DS . $file );
				if ( $tab ) {
					$this->bpge_tabs[] = $tab;
				}
			}
			closedir( $handle );
		}

		$this->bpge_tabs = apply_filters( 'bpge_admin_tabs', $this->bpge_tabs );

		$this->reorder_tabs();
	}

	/**
	 * Used for sorting tabs according to their position.
	 */
	public function reorder_tabs() {

		if ( empty( $this->bpge_tabs ) || ! is_array( $this->bpge_tabs ) ) {
			return;
		}

		$tmp = array();

		foreach ( $this->bpge_tabs as $tab ) {
			$tmp[ $tab->position ] = $tab;
		}

		// Make smaller position at the top of an array.
		ksort( $tmp );
		$this->bpge_tabs = $tmp;
		unset( $tmp );

		// Set the first tab as default.
		if ( empty( $this->default_tab ) ) {
			$first             = reset( $this->bpge_tabs );
			$this->default_tab = $first->slug;
		}
	}

	/**
	 * Load all required styles and scripts.
	 */
	public function load_assets() {

		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );
		add_action( 'admin_footer', array( $this, 'load_pointers' ) );

		// All other admin area js.
		add_action( 'admin_enqueue_scripts', array( $this, 'load_js' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_css' ) );
	}

	public function load_pointers() {

		$page = is_multisite() ? 'network/settings.php' : 'options-general.php';

		$vote_content = '<h3>' . __( 'Vote for Features', 'buddypress-groups-extras' ) . '</h3>';
		$vote_content .= '<p>' . __( 'Based on voting results I will implement features in new versions (either in core or as modules to extend the initial functionality).', 'buddypress-groups-extras' ) . '</p>';

		// Get all pointer that we dismissed.
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		// Check whether my pointer has been dismissed.
		if ( in_array( 'bpge_vote', $dismissed, true ) ) {
			$vote_content = '';
		}

		if ( ! empty( $vote_content ) ) { ?>
			<!--suppress JSUnresolvedVariable -->
			<script type="text/javascript">// <![CDATA[
				jQuery( document ).ready( function() {
					jQuery( '#bpge_tab_poll' ).pointer( {
						content: '<?php echo $vote_content; ?>',
						position: {
							edge: 'top',
							align: 'left',
						},
						close: function() {
							jQuery.post( ajaxurl, {
								action: 'dismiss-wp-pointer',
								pointer: 'bpge_vote',
							} );
						},
					} ).pointer( 'open' );
				} );
				// ]]></script>
			<?php
		}
	}

	public function load_js( $hook ) {

		if ( $hook !== 'settings_page_bpge-admin' ) {
			return;
		}

		wp_enqueue_script( 'bpge_admin_js_popup', BPGE_URL . '/libs/messi.js', array( 'jquery' ), BPGE_VERSION );

		wp_enqueue_script( 'bpge-admin', BPGE_URL . '/js/admin-scripts.js', array( 'wp-pointer' ), BPGE_VERSION );
		wp_localize_script( 'bpge-admin', 'bpge', bpge_get_localized_data() );
	}

	public function load_css( $hook ) {

		global $post_type;

		if (
			( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'gpages' ) ||
			( isset( $post_type ) && $post_type === 'gpages' ) ||
			$hook === 'settings_page_bpge-admin'
		) {
			wp_enqueue_style( 'bpge_admin_css', BPGE_URL . '/css/admin-styles.css' );
		}

		if ( $hook === 'settings_page_bpge-admin' ) {
			wp_enqueue_style( 'bpge_admin_css_messi', BPGE_URL . '/libs/messi.css' );
		}
	}

	/**
	 * Actual html of a page (its core).
	 */
	public function admin_page() {

		// Define some data that can be given to each metabox during rendering.
		$tab = $this->get_cur_tab(); ?>

		<div id="bpge-admin" class="wrap">
			<?php $this->header(); ?>

			<?php
			$page     = is_multisite() ? 'network/settings.php' : 'options-general.php';
			$page_url = admin_url( '/' . $page . '?page=' . BPGE_ADMIN_SLUG );
			if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
				$page_url .= '&tab=' . sanitize_key( $_GET['tab'] );
			}
			?>

			<form action="<?php echo esc_url( $page_url ); ?>" class="tab_<?php echo esc_attr( $tab ); ?>" id="bpge-form" method="post">
				<?php
				wp_nonce_field( 'bpge-options' );
				settings_fields( $tab );
				do_settings_sections( $tab );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * We need to know the current tab at any time.
	 * If not specified - get the default one.
	 */
	public function get_cur_tab() {

		if ( isset( $_GET['tab'] ) && ! empty( $_GET['tab'] ) ) {
			return $_GET['tab'];
		}

		return $this->default_tab;
	}

	/**
	 * Content part with header section.
	 */
	public function header() {

		$current_tab = $this->get_cur_tab();

		echo '<h2>';
		_e( 'BuddyPress Groups Extras', 'buddypress-groups-extras' );
		do_action( 'bpge_admin_header_title_pro' );
		echo '&rarr; ';
		_e( 'Extend Your Groups', 'buddypress-groups-extras' );
		do_action( 'bpge_admin_header_title' );
		echo '</h2>'; ?>

		<?php
		if ( isset( $_GET['saved'] ) ) {
			echo '<div style="clear:both"></div>';
			echo '<div id="message" class="updated fade"><p>' . __( 'All changes were saved. Go and check results!', 'buddypress-groups-extras' ) . '</p></div>';
		}

		if ( ! isset( $this->bpge['reviewed'] ) || $this->bpge['reviewed'] === 'no' ) {
			echo '<div style="clear:both"></div>';
			echo '<div id="message" class="updated fade"><p>' .
			     sprintf( /* translators: %s - URL to WordPress.org page where use can review the plugin. */
				     __( 'If you like the plugin please review it on its <a href="%s">WordPress Repository page</a>. Thanks in advance!', 'buddypress-groups-extras' ),
				     'https://wordpress.org/support/view/plugin-reviews/buddypress-groups-extras'
			     ) .
			     '<span style="float:right"><a href="#" class="bpge_review_dismiss" style="color:red">' . __( 'Dismiss', 'buddypress-groups-extras' ) . '</span>' .
			     '</p></div>';
		}

		echo '<div style="clear:both"></div>';
		echo '<h3 class="nav-tab-wrapper">';
		foreach ( $this->bpge_tabs as $tab ) {
			$active = $current_tab === $tab->slug ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . esc_attr( $active ) . '" id="bpge_tab_' . esc_attr( $tab->slug ) . '" href="?page=' . esc_attr( $this->slug ) . '&tab=' . esc_attr( $tab->slug ) . '">' . esc_html( $tab->title ) . '</a>';
		}
		do_action( 'bpge_admin_tabs_links' );
		echo '</h3>';
	}
}

/*************************************************************************/

/**
 * Class that will be a skeleton for all other pages.
 */
class BPGE_ADMIN_TAB {

	// All theme options
	public $bpge = null;

	// All these vars are required and should be overwritten.
	public $position = 0;
	public $title    = null;
	public $slug     = null;

	// Used by some pro extensions.
	public $extras_to_header = array();

	/**
	 * Create the actual page object.
	 */
	public function __construct() {

		if ( ! ( isset( $_GET['page'] ) && $_GET['page'] === BPGE_ADMIN_SLUG ) ) {
			return;
		}

		global $bpge;
		$this->bpge = $bpge;

		register_setting( $this->slug, $this->slug );
		add_settings_section(
			$this->slug . '_settings', // section id
			'', // title
			array( &$this, 'display' ), // method handler
			$this->slug // slug
		);

		$this->register_sections();

		if ( $test = $this->header_title_attach() ) {
			$this->extras_to_header[] = $test;
		}

		add_action( 'bpge_admin_header_title', array( $this, 'apply_header_extras' ), 10 );

		$tab = 'general';
		if ( isset( $_GET['tab'] ) ) {
			$tab = $_GET['tab'];
		}

		// Process save process.
		if (
			! empty( $_POST )
			&& is_admin()
			&& isset( $_GET['page'] ) && $_GET['page'] === BPGE_ADMIN_SLUG
			&& $this->slug === $tab
		) {
			$this->save();
			// Now redirect to the same page to clear POST.
			wp_redirect( str_replace( '&saved', '', $_POST['_wp_http_referer'] ) . '&saved' );
		}
	}

	/**
	 * Here we need to register all settings if needed.
	 * Those sections will be used to display fields/options.
	 *
	 * @override
	 */
	public function register_sections() {
	}

	/**
	 * In case we need to add some strings to the admin page header.
	 *
	 * @override
	 */
	public function header_title_attach() {

		return '';
	}

	public function apply_header_extras() {

		if ( $data = array_filter( $this->extras_to_header ) ) {
			echo ' [' . implode( ', ', $data ) . ']';
		}
	}

	/**
	 * HTML should be here.
	 *
	 * @override
	 */
	public function display() {
	}

	/**
	 * All security and data checks should be here.
	 *
	 * @override
	 */
	public function save() {
	}
}
