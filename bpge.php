<?php
/*
Plugin Name: BuddyPress Groups Extras
Plugin URI: http://ovirium.com/portfolio/bp-groups-extras/
Description: Adding extra fields and pages, menu sorting and other missing functionality to groups
Version: 3.6.7.2
Author: slaFFik
Author URI: http://ovirium.com/
*/
define( 'BPGE_VERSION', '3.6.7.2' );
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
	$bpge = array(
		'groups'        => 'all',
		'uninstall'     => 'no',
		're'            => '1',
		're_fields'     => 'no',
		'access_extras' => 'g_s_admin',
		'field_2_link'  => 'no',
		'reviewed'      => 'no',
	);

	add_blog_option( bpge_get_main_site_id(), 'bpge', $bpge );
}

/**
 * What to do on deactivation
 */
register_deactivation_hook( __FILE__, 'bpge_deactivation' );
function bpge_deactivation() {
	$bpge = bpge_get_options();

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

	switch_to_blog( bpge_get_main_site_id() );

	$post_types = "'" . implode( "','", array( BPGE_FIELDS, BPGE_GPAGES, BPGE_GFIELDS, BPGE_FIELDS_SET ) ) . "'";
	$group_meta = $bp->table_prefix . 'bp_groups_groupmeta';

	if ( $type === 'all' ) {
		delete_option( 'bpge' );
	}

	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE 'bpge_%%'" );

	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE `post_type` IN ({$post_types})" );

	$wpdb->query( "DELETE FROM {$group_meta} WHERE `meta_key` LIKE 'bpge%%'" );

	restore_current_blog();
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
	add_submenu_page(
		is_multisite() ? 'settings.php' : 'options-general.php',
		__( 'BP Groups Extras', 'bpge' ),
		'<span id="bpge-admin-menu" class="dashicons dashicons-groups"></span>&nbsp;' . __( 'BP Groups Extras', 'bpge' ),
		'manage_options',
		BPGE_ADMIN_SLUG,
		array( $admin, 'admin_page' ) );

	$admin->load_assets();

	do_action( 'bpge_admin_load' );
}

if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'bpge_admin_init' );
} else {
	add_action( 'admin_menu', 'bpge_admin_init' );
}

/**
 * Get BPGE plugin options
 * They are always stored on the main site.
 *
 * @return array
 */
function bpge_get_options() {
	return get_blog_option( bpge_get_main_site_id(), 'bpge' );
}

/**
 * Get the main site id, usually <code>1</code>
 *
 * @return int
 */
function bpge_get_main_site_id() {
	return apply_filters( 'bpge_get_main_site_id', 1 );
}

/**
 * The main loader - BPGE Engine
 */
function bpge_pre_load() {
	global $bpge;

	if ( ! defined( 'BP_VERSION' ) ) {
		return;
	}

	$bpge = bpge_get_options();

	// scripts and styles
	require( BPGE_PATH . '/core/cssjs.php' );
	require( BPGE_PATH . '/core/ajax.php' );
	require( BPGE_PATH . '/core/templates.php' );
	require( BPGE_PATH . '/core/pro.php' );
	require( BPGE_PATH . '/core/cpt.php' );
	require( BPGE_PATH . '/core/helpers.php' );

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
 * Reorder group nav links
 *
 * @return bool|array
 */
function bpge_get_nav_order() {
	global $bp, $bpge;

	if ( empty( $bpge ) ) {
		$bpge = bpge_get_options();
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

		do_action( 'bpge_get_nav_order' );

		return $bp->bp_options_nav[ $bp->groups->current_group->slug ];
	}

	return false;
}

add_action( 'bp_head', 'bpge_get_nav_order', 100 );

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