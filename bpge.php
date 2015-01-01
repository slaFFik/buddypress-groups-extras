<?php
/*
Plugin Name: BuddyPress Groups Extras
Plugin URI: http://ovirium.com/portfolio/bp-groups-extras/
Description: Adding extra fields and pages, menu sorting and other missing functionality to groups
Version: 3.6.5
Author: slaFFik
Author URI: http://ovirium.com/
*/
define( 'BPGE_VERSION', '3.6.5' );
define( 'BPGE', 'bpge' );
define( 'BPGE_ADMIN_SLUG', 'bpge-admin' );
define( 'BPGE_URL', plugins_url( '_inc', __FILE__ ) ); // link to all assets, with /
define( 'BPGE_PATH', dirname( __FILE__ ) . '/' ); // with /
// post types
define( 'BPGE_FIELDS', 'bpge_fields' );
define( 'BPGE_FIELDS_SET', 'bpge_fields_set' );
define( 'BPGE_GFIELDS', 'bpge_gfields' );
define( 'BPGE_GPAGES', 'gpages' );

if ( ! defined( 'DS' ) ) {
	define( 'DS', DIRECTORY_SEPARATOR );
}

/**
 * What to do on activation
 */
register_activation_hook( __FILE__, 'bpge_activation' );
function bpge_activation() {
	// some defaults
	$bpge['groups']        = 'all';
	$bpge['uninstall']     = 'no';
	$bpge['re']            = '1';
	$bpge['re_fields']     = 'no';
	$bpge['access_extras'] = 'g_s_admin';
	$bpge['field_2_link']  = 'no';
	$bpge['reviewed']      = 'no';

	add_option( 'bpge', $bpge, '', 'yes' );
}

/**
 * What to do on deactivation
 */
register_deactivation_hook( __FILE__, 'bpge_deactivation' );
function bpge_deactivation() {
	$bpge = bp_get_option( 'bpge' );

	if ( $bpge['uninstall'] == 'yes' ) {
		bpge_clear( 'all' );
	}
}

/**
 * Remove all plugin data
 *
 * @param string $type
 */
function bpge_clear( $type = 'all' ) {
	/** @var $wpdb WPDB */
	global $wpdb, $bp;

	$post_types = "'" . implode( "','", array( BPGE_FIELDS, BPGE_GPAGES, BPGE_GFIELDS, BPGE_FIELDS_SET ) ) . "'";
	$group_meta = $bp->table_prefix . 'bp_groups_groupmeta';

	if ( $type === 'all' ) {
		delete_option( 'bpge' );
	}

	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE 'bpge_%%'" );

	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE `post_type` IN ({$post_types})" );

	$wpdb->query( "DELETE FROM {$group_meta} WHERE `meta_key` LIKE 'bpge%%'" );
}

/**
 * i18n: Load languages
 */
function bpge_load_textdomain() {
	$locale = apply_filters( 'buddypress_locale', get_locale() );
	$mofile = dirname( __File__ ) . "/langs/bpge-$locale.mo";

	if ( file_exists( $mofile ) ) {
		load_textdomain( 'bpge', $mofile );
	}
}

add_action( 'plugins_loaded', 'bpge_load_textdomain', 7 );

/**
 * Load admin menu
 */
function bpge_admin_init() {
	include( BPGE_PATH . '/core/admin.php' );

	$admin    = new BPGE_ADMIN();
	$pagehook = add_submenu_page(
		is_multisite() ? 'settings.php' : 'options-general.php',
		__( 'BP Groups Extras', 'bpge' ),
		__( 'BP Groups Extras', 'bpge' ),
		'manage_options',
		BPGE_ADMIN_SLUG,
		array( $admin, 'admin_page' ) );
	$admin->load_assets( $pagehook );

	do_action( 'bpge_admin_load' );
}

if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'bpge_admin_init' );
} else {
	add_action( 'admin_menu', 'bpge_admin_init' );
}

/**
 * The main loader - BPGE Engine
 */
function bpge_pre_load() {
	global $bpge;

	if ( ! defined( 'BP_VERSION' ) ) {
		return;
	}

	$bpge = bp_get_option( 'bpge' );

	// scripts and styles
	require( BPGE_PATH . '/core/cssjs.php' );
	require( BPGE_PATH . '/core/ajax.php' );
	require( BPGE_PATH . '/core/templates.php' );

	// load pro features
	bpge_include_pro_files( dirname( __FILE__ ) . '/_pro' );

	// gpages
	bpge_register_groups_pages();
	// bpge_gfields
	bpge_register_fields();
	// bpge_fields_set
	bpge_register_set();
	// bpge_fields
	bpge_register_set_fields();
}

add_action( 'init', 'bpge_pre_load' );

function bpge_load() {
	global $bp, $bpge;

	if ( bp_is_group() && ! defined( 'DOING_AJAX' ) ) {
		if (
			( is_string( $bpge['groups'] ) && $bpge['groups'] == 'all' ) ||
			( is_array( $bpge['groups'] ) && in_array( $bp->groups->current_group->id, $bpge['groups'] ) )
		) {
			require( BPGE_PATH . '/core/loader.php' );
		}

		do_action( 'bpge_group_load' );
	}
}

add_action( 'bp_init', 'bpge_load' );

/**
 * Parse and include all BPGE extensions
 *
 * @param string $dir
 */
function bpge_include_pro_files( $dir ) {
	if ( ! is_dir( $dir ) ) {
		return;
	}

	if ( $handle = opendir( $dir ) ) {
		while ( false !== ( $file = readdir( $handle ) ) ) {
			if ( $file == "." || $file == ".." ) {
				continue;
			}

			if ( is_dir( $dir . DS . $file ) ) {
				bpge_include_pro_files( $dir . DS . $file );
			} else {
				/** @noinspection PhpIncludeInspection */
				include( $dir . DS . $file );
			}
		}
		closedir( $handle );
	}
}

/**
 * Reorder group nav links
 *
 * @return array
 */
function bpge_nav_order() {
	global $bp, $bpge;

	if ( ! $bpge ) {
		$bpge = bp_get_option( 'bpge' );
	}

	if ( bp_is_group() && bp_is_single_item() ) {
		$order = groups_get_groupmeta( $bp->groups->current_group->id, 'bpge_nav_order' );

		if ( ! empty( $order ) && is_array( $order ) ) {
			foreach ( $order as $slug => $position ) {
				if ( isset( $bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ] ) ) {
					$bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ]['position'] = $position;
				}
			}
		}

		do_action( 'bpge_nav_order' );
	}

	return $bp->bp_options_nav[ $bp->groups->current_group->slug ];
}

add_action( 'bp_head', 'bpge_nav_order', 100 );

/**
 * Groups navigation reordering
 *
 * @param $old_slug
 *
 * @return string
 */
function bpge_landing_page( $old_slug ) {
	global $bp, $bpge;

	$new_slug = $old_slug;

	if ( bp_is_group() && bp_is_single_item() &&
	     (
		     ( is_array( $bpge['groups'] ) && in_array( $bp->groups->current_group->id, $bpge['groups'] ) )
		     ||
		     ( is_string( $bpge['groups'] ) && $bpge['groups'] == 'all' )
	     )
	) {
		// get all pages - take the first
		$order = groups_get_groupmeta( $bp->groups->current_group->id, 'bpge_nav_order' );

		if ( is_array( $order ) && ! empty( $order ) ) {
			$flipped  = array_flip( $order );
			$new_slug = reset( $flipped );
		}
	}

	return apply_filters( 'bpge_landing_page', $new_slug );
}

add_filter( 'bp_groups_default_extension', 'bpge_landing_page' );

/************************************
 * Several hooks to fix some places *
 ************************************/

/**
 * Register group fields
 */
function bpge_register_fields() {
	$labels = array(
		'name' => __( 'Groups Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'comments' )
	);

	register_post_type( BPGE_GFIELDS, $args );
}

/**
 * Register set of fields post types
 */
function bpge_register_set() {
	$labels = array(
		'name' => __( 'Sets of Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes' )
	);

	register_post_type( BPGE_FIELDS_SET, $args );
}

/**
 * Register groups fields post type, where all their content will be stored
 */
function bpge_register_set_fields() {
	$labels = array(
		'name' => __( 'Groups Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes' )
	);

	register_post_type( BPGE_FIELDS, $args );
}

/**
 * Register groups pages post type, where all their content will be stored
 */
function bpge_register_groups_pages() {
	$labels = array(
		'name'          => __( 'Groups Pages', 'bpge' ),
		'singular_name' => __( 'Groups Page', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => true,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'comments' )
	);
	register_post_type( BPGE_GPAGES, $args );
}

/**
 * Delete associated gpage on group delete
 *
 * @param object $group_obj
 * @param array $user_ids
 */
function bpge_delete_group(
	$group_obj, /** @noinspection PhpUnusedParameterInspection */
	$user_ids
) {
	/** @var $wpdb WPDB */
	global $bp, $wpdb;
	$to_delete = false;
	$pages     = $fields = array();

	if ( isset( $bp->groups->current_group->extras['gpage_id'] ) && ! empty( $bp->groups->current_group->extras['gpage_id'] ) ) {
		$to_delete = $bp->groups->current_group->extras['gpage_id'];
	} else {
		$bpge = groups_get_groupmeta( $group_obj->id, 'bpge' );
		if ( $bpge && isset( $bpge['gpage_id'] ) && ! empty( $bpge['gpage_id'] ) ) {
			$to_delete = $bpge['gpage_id'];
		}
	}

	if ( ! empty( $to_delete ) ) {
		// remove all group pages
		$pages = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE `post_parent` = %d",
			$to_delete
		) );
	}
	// remove all group fields
	$fields = $wpdb->get_col( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE `post_type` = '%s' AND  `post_parent` = %d",
		BPGE_GFIELDS, $group_obj->id
	) );
	$data   = array_merge( $pages, $fields );
	if ( ! empty( $data ) ) {
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->posts} WHERE `ID` IN (%s)",
			implode( ',', $data )
		) );
	}

	if ( ! empty( $to_delete ) ) {
		wp_delete_post( $to_delete, true );
	}
}

add_action( 'bp_groups_delete_group', 'bpge_delete_group', 10, 2 );

/**
 * Рide add new menu and redirect from it to the whole list - do not allow admin to add manually
 */
function bpge_gpages_hide_add_new() {
	global $submenu;

	unset(
		$submenu['edit.php?post_type=gpages'][10],
		$submenu[ 'edit.php?post_type=' . BPGE_FIELDS ][10],
		$submenu[ 'edit.php?post_type=' . BPGE_FIELDS_SET ][10]
	);
}

add_action( 'admin_menu', 'bpge_gpages_hide_add_new' );

/**
 * Do not allow create group pages in wp-admin area - redirect to the list
 */
function bpge_gpages_redirect_to_all() {
	$result = stripos( $_SERVER['REQUEST_URI'], 'post-new.php?post_type=gpages' );
	if ( $result !== false ) {
		wp_redirect( get_option( 'siteurl' ) . '/wp-admin/edit.php?post_type=gpages' );
	}
}

add_action( 'admin_menu', 'bpge_gpages_redirect_to_all' );

/*******************
 * Several Helpers *
 *******************/

/**
 * Check access level: true or false as return
 */
if ( ! function_exists( 'bpge_user_can' ) ) {
	function bpge_user_can( $item, $user_id = false ) {
		global $bpge;

		if ( empty( $user_id ) || ! is_int( $user_id ) || $user_id < 1 ) {
			$user_id = bp_loggedin_user_id();
		}

		if ( is_super_admin( $user_id ) ) {
			return true;
		}

		$current_group = groups_get_current_group();

		switch ( $item ) {
			case 'group_extras_admin':
				if ( $bpge['access_extras'] == 'g_s_admin' && groups_is_user_admin( $user_id, $current_group->id ) ) {
					return true;
				}
				break;
		}

		return false;
	}
}

/**
 * Helper for generating some titles
 *
 * @param string $name
 *
 * @return string
 */
function bpge_names( $name = 'name' ) {
	$text = '';

	switch ( $name ) {
		case 'title_general':
			$text = __( 'Group Extras &rarr; General Settings', 'bpge' );
			break;
		case 'title_fields':
			$text = __( 'Group Extras &rarr; Fields Management', 'bpge' );
			break;
		case 'title_pages':
			$text = __( 'Group Extras &rarr; Pages Management', 'bpge' );
			break;
		case 'title_fields_add':
			$text = __( 'Group Extras &rarr; Add Field', 'bpge' );
			break;
		case 'title_fields_edit':
			$text = __( 'Group Extras &rarr; Edit Field', 'bpge' );
			break;
		case 'title_pages_add':
			$text = __( 'Group Extras &rarr; Add Page', 'bpge' );
			break;
		case 'title_pages_edit':
			$text = __( 'Group Extras &rarr; Edit Page', 'bpge' );
			break;
		case 'name':
			$text = __( 'Description', 'bpge' );
			break;
		case 'nav':
			$text = __( 'Extras', 'bpge' );
			break;
		case 'gpages':
			$text = __( 'Pages', 'bpge' );
			break;
	}

	return $text;
}

/**
 * Empty defaults
 *
 * @return Stdclass
 */
function bpge_get_field_defaults() {
	$field = new Stdclass;

	$field->ID           = '';
	$field->desc         = '';
	$field->post_title   = '';
	$field->post_content = '';
	$field->post_excerpt = ''; // field type - radio, select, checkbox, text, textarea, datebox
	$field->post_status  = '';
	$field->pinged       = ''; // required or not
	$field->post_type    = BPGE_GFIELDS;

	return $field;
}

/**
 * Get all group fields
 *
 * @param string $status
 * @param bool $group_id
 *
 * @return array
 */
function bpge_get_group_fields( $status = 'publish', $group_id = false ) {
	global $bp;

	if ( empty( $group_id ) ) {
		$group_id = $bp->groups->current_group->id;
	}

	// possible statuses: draft | publish | any

	$fields = get_posts( array(
		                     'posts_per_page' => 99,
		                     'numberposts'    => 99,
		                     'order'          => 'ASC',
		                     'orderby'        => 'menu_order',
		                     'post_status'    => $status,
		                     'post_parent'    => $group_id,
		                     'post_type'      => BPGE_GFIELDS,
	                     ) );

	return $fields;
}

/**
 * Display PRO label or not
 */
function bpge_admin_header_title_pro() {
	if ( defined( 'BPGE_PRO' ) ) {
		echo ' [PRO] ';
	}
}

add_action( 'bpge_admin_header_title_pro', 'bpge_admin_header_title_pro', 1 );