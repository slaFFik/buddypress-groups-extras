<?php
/**
 * Plugin Name: BuddyPress Groups Extras
 * Plugin URI: https://ovirium.com/
 * Description: Adding extra fields and pages, menu sorting and other missing functionality to groups.
 * Author: slaFFik
 * Author URI: https://ovirium.com/
 * Version: 3.6.10
 * Text Domain: buddypress-groups-extras
 * Domain Path: /assets/
 * Requires PHP: 7.2
 * Requires at least: 6.0
 * Requires Plugins: buddypress, bp-classic
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

const BPGE_VERSION    = '3.6.10';
const BPGE            = 'bpge';
const BPGE_ADMIN_SLUG = 'bpge-admin';

// phpcs:disable WPForms.Comments.PHPDocDefine.MissPHPDoc
define( 'BPGE_URL', plugins_url( 'assets', __FILE__ ) ); // link to all assets, with "/".
const BPGE_PATH = __DIR__ . '/'; // with "/".
// phpcs:enable WPForms.Comments.PHPDocDefine.MissPHPDoc

// Post types.
const BPGE_FIELDS     = 'bpge_fields';
const BPGE_FIELDS_SET = 'bpge_fields_set';
const BPGE_GFIELDS    = 'bpge_gfields';
const BPGE_GPAGES     = 'gpages';

if ( ! defined( 'DS' ) ) {
	// phpcs:ignore WPForms.Comments.PHPDocDefine.MissPHPDoc
	define( 'DS', DIRECTORY_SEPARATOR );
}

/**
 * Check plugin requirements.
 * We do it on `plugins_loaded` hook. If earlier - core constants still not defined.
 */
function bpge_check_requirements() {

	if ( version_compare( PHP_VERSION, '7.2', '<' ) ) {
		add_action( 'admin_init', 'bpge_deactivate' );
		add_action( 'admin_notices', 'bpge_deactivate_msg_php' );

	} elseif ( ! function_exists( 'bp_is_active' ) ) {
		// Houston, we have a problem.
		add_action( 'admin_init', 'bpge_deactivate' );
		add_action( 'admin_notices', 'bpge_deactivate_msg_bp' );
	}
}

add_action( 'plugins_loaded', 'bpge_check_requirements' );

/**
 * Deactivate plugin.
 */
function bpge_deactivate() {

	deactivate_plugins( plugin_basename( __FILE__ ) );
}

/**
 * Admin notice for minimum PHP version.
 */
function bpge_deactivate_msg_php() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'BuddyPress Groups Extras plugin has been deactivated. Your site is running an outdated version of PHP that is no longer supported and is not compatible with the plugin. Please update PHP to a version supported by WordPress.', 'buddypress-groups-extras' );
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * Admin notice for minimum PHP version.
 */
function bpge_deactivate_msg_bp() {

	echo '<div class="notice notice-error"><p>';
	esc_html_e( 'BuddyPress Groups Extras plugin has been deactivated because it requires an activated BuddyPress plugin with Groups component enabled. Please make sure those two requirements are met in order to use BuddyPress Groups Extras.', 'buddypress-groups-extras' );
	echo '</p></div>';

	// phpcs:disable
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
	// phpcs:enable
}

/**
 * What to do on activation.
 */
register_activation_hook( __FILE__, 'bpge_activation' );

/**
 * Activation hook callback function.
 */
function bpge_activation() {

	// some defaults.
	$bpge = [
		'groups'        => 'all',
		'uninstall'     => 'no',
		're'            => 1,
		're_fields'     => 'no',
		'access_extras' => 'g_s_admin',
		'field_2_link'  => 'no',
		'reviewed'      => 'no',
	];

	if ( ! function_exists( 'add_blog_option' ) ) {
		require_once ABSPATH . 'wp-includes/ms-blogs.php';
	}

	add_blog_option( bpge_get_main_site_id(), 'bpge', $bpge );
}

/**
 * What to do on deactivation.
 */
register_deactivation_hook( __FILE__, 'bpge_deactivation' );

/**
 * Deactivation hook callback function.
 */
function bpge_deactivation() {

	$bpge = bpge_get_options();

	if ( $bpge['uninstall'] === 'yes' ) {
		bpge_clear( 'all' );
	}
}

/**
 * Remove all plugin data.
 *
 * @param string $type Clear plugin data, default set to "all".
 */
function bpge_clear( $type = 'all' ) {

	global $wpdb;

	switch_to_blog( bpge_get_main_site_id() );

	$post_types = "'" . implode( "','", [ BPGE_FIELDS, BPGE_GPAGES, BPGE_GFIELDS, BPGE_FIELDS_SET ] ) . "'";
	$group_meta = bp_core_get_table_prefix() . 'bp_groups_groupmeta';

	if ( $type === 'all' ) {
		delete_option( 'bpge' );
	}

	// phpcs:disable WordPress.DB.DirectDatabaseQuery
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE `option_name` LIKE %s", 'bpge_%%' ) );

	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE `post_type` IN (%s)", $post_types ) );

	$wpdb->query( $wpdb->prepare( 'DELETE FROM %s WHERE `meta_key` LIKE %s', $group_meta, 'bpge%%' ) );
	// phpcs:enable WordPress.DB.DirectDatabaseQuery

	restore_current_blog();
}

/**
 * Load languages.
 */
function bpge_load_textdomain() {

	load_plugin_textdomain( 'buddypress-groups-extras', false, plugin_basename( __DIR__ ) . '/assets' );
}

add_action( 'plugins_loaded', 'bpge_load_textdomain' );

/**
 * Load admin menu.
 */
function bpge_admin_init() {

	include BPGE_PATH . '/core/admin.php';

	$admin = new BPGE_ADMIN();

	add_submenu_page(
		is_multisite() ? 'settings.php' : 'options-general.php',
		esc_html__( 'BP Groups Extras', 'buddypress-groups-extras' ),
		esc_html__( 'BP Groups Extras', 'buddypress-groups-extras' ),
		'manage_options',
		BPGE_ADMIN_SLUG,
		[ $admin, 'admin_page' ]
	);

	$admin->load_assets();

	do_action( 'bpge_admin_load' );
}

if ( is_multisite() ) {
	add_action( 'network_admin_menu', 'bpge_admin_init' );
} else {
	add_action( 'admin_menu', 'bpge_admin_init' );
}

/**
 * Finds the URL of settings page.
 *
 * @return string
 */
function bpge_admin_find_admin_location() {

	if ( ! is_super_admin() ) {
		return false;
	}

	return bp_core_do_network_admin() ? 'settings.php' : 'options-general.php';
}

/**
 * Add settings link on plugin's page.
 *
 * @param array  $links Settings link.
 * @param string $file  File name for comparison.
 *
 * @return array
 */
function bpge_admin_settings_link( $links, $file ) {

	$this_plugin = plugin_basename( plugin_basename( __DIR__ ) ) . '/bpge.php';

	if ( $file === $this_plugin ) {
		$links = array_merge(
			$links,
			[
				'settings' => '<a href="' . esc_url( add_query_arg( [ 'page' => BPGE_ADMIN_SLUG ], bpge_admin_find_admin_location() ) ) . '">' . esc_html__( 'Settings', 'buddypress-groups-extras' ) . '</a>',
			]
		);
	}

	return $links;
}

if ( function_exists( 'bp_is_active' ) ) {
	add_filter( 'plugin_action_links', 'bpge_admin_settings_link', 10, 2 );
	add_filter( 'network_admin_plugin_action_links', 'bpge_admin_settings_link', 10, 2 );
}

/**
 * Get BPGE plugin options.
 * They are always stored on the main site.
 *
 * @return array
 */
function bpge_get_options() {

	if ( ! function_exists( 'get_blog_option' ) ) {
		require_once ABSPATH . 'wp-includes/ms-blogs.php';
	}

	return get_blog_option( bpge_get_main_site_id(), 'bpge' );
}

/**
 * Get the main site id, usually <code>1</code>.
 */
function bpge_get_main_site_id() {

	return (int) apply_filters( 'bpge_get_main_site_id', 1 );
}

/**
 * The main loader - BPGE Engine.
 */
function bpge_pre_load() {

	if ( ! bp_is_active( 'groups' ) ) {
		// Houston, we have a problem.
		add_action( 'admin_init', 'bpge_deactivate' );
		add_action( 'admin_notices', 'bpge_deactivate_msg_bp' );

		return;
	}

	global $bpge;

	if ( ! defined( 'BP_VERSION' ) ) {
		return;
	}

	$bpge = bpge_get_options();

	require BPGE_PATH . '/core/cssjs.php';
	require BPGE_PATH . '/core/ajax.php';
	require BPGE_PATH . '/core/templates.php';
	require BPGE_PATH . '/core/cpt.php';
	require BPGE_PATH . '/core/helpers.php';

	// gpages.
	bpge_register_groups_pages();
	// bpge_gfields.
	bpge_register_fields();
	// bpge_fields_set.
	bpge_register_set();
	// bpge_fields.
	bpge_register_set_fields();
}

add_action( 'bp_init', 'bpge_pre_load' );

/**
 * Display the Group Pages menu item in the BuddyPress Groups admin.
 */
function bpge_admin_add_groups_pages_to_groups() {

	add_submenu_page(
		'bp-groups',
		__( 'Group Pages', 'buddypress-groups-extras' ),
		__( 'Group Pages', 'buddypress-groups-extras' ),
		'bp_moderate',
		'edit.php?post_type=' . BPGE_GPAGES,
		false
	);
}

add_action( 'bp_admin_menu', 'bpge_admin_add_groups_pages_to_groups', 100 );

/**
 * The group component loader.
 */
function bpge_load() {

	global $bpge;

	if ( bp_is_group() && ! wp_doing_ajax() ) {
		if (
			( is_string( $bpge['groups'] ) && $bpge['groups'] === 'all' ) ||
			( is_array( $bpge['groups'] ) && in_array( bp_get_current_group_id(), $bpge['groups'], true ) )
		) {
			require BPGE_PATH . '/core/loader.php';
		}

		do_action( 'bpge_group_load' );
	}
}

add_action( 'bp_init', 'bpge_load' );

/**
 * Reorder group nav links.
 *
 * @return array
 */
function bpge_get_nav_order() { // phpcs:ignore Generic.Metrics

	global $bpge;
	$bp = buddypress();

	if ( empty( $bpge ) ) {
		$bpge = bpge_get_options();
	}

	if ( isset( $bpge['access_nav_reorder'] ) && $bpge['access_nav_reorder'] === 'no' ) {
		return [];
	}

	if ( bp_is_group() && bp_is_single_item() ) {
		$order = groups_get_groupmeta( bp_get_current_group_id(), 'bpge_nav_order' );

		if ( ! empty( $order ) && is_array( $order ) ) {
			foreach ( $order as $slug => $position ) {
				if ( bpge_is_bp_26() ) {
					buddypress()->groups->nav->edit_nav(
						[ 'position' => $position ],
						$slug,
						bp_current_item()
					);
				} else {
					if ( isset( $bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ] ) ) {
						$bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ]['position'] = $position;
					}
				}
			}
		}

		do_action( 'bpge_get_nav_order' );

		return bpge_get_group_nav();
	}

	return [];
}

add_action( 'bp_head', 'bpge_get_nav_order', 100 );

/**
 * Groups navigation reordering.
 *
 * @param string $old_slug Previous nav menu slug.
 *
 * @return string
 */
function bpge_landing_page( $old_slug ) {

	global $bpge;

	$bpge = bpge_get_options();

	if ( isset( $bpge['access_nav_reorder'] ) && $bpge['access_nav_reorder'] === 'no' ) {
		return $old_slug;
	}

	$new_slug = $old_slug;

	if ( bp_is_group() && bp_is_single_item() &&
		(
			( is_array( $bpge['groups'] ) && in_array( bp_get_current_group_id(), $bpge['groups'], true ) )
			||
			( is_string( $bpge['groups'] ) && $bpge['groups'] === 'all' )
		) ) {
		// get all pages - take the first.
		$order = groups_get_groupmeta( bp_get_current_group_id(), 'bpge_nav_order' );

		if ( is_array( $order ) && ! empty( $order ) ) {
			$flipped  = array_flip( $order );
			$new_slug = reset( $flipped );
		}
	}

	return apply_filters( 'bpge_landing_page', $new_slug );
}

add_filter( 'bp_groups_default_extension', 'bpge_landing_page' );

/**
 * Add a link to Adminbar.
 */
function bpge_adminbar_menu_link() {

	global $wp_admin_bar;

	// Only show if viewing a group.
	if ( ! bp_is_group() || bp_is_group_create() ) {
		return;
	}

	// Only show this menu to group admins and super admins.
	if ( ! bp_current_user_can( 'bp_moderate' ) && ! bp_group_is_admin() ) {
		return;
	}

	$wp_admin_bar->add_menu(
		[
			'parent' => buddypress()->group_admin_menu_id,
			'id'     => 'extras',
			'title'  => __( 'Edit Group Extras', 'buddypress-groups-extras' ),
			'href'   => bp_get_groups_action_link( 'admin/extras' ),
		]
	);
}

if ( function_exists( 'bp_is_active' ) ) {
	add_action( 'admin_bar_menu', 'bpge_adminbar_menu_link', 100 );
}
