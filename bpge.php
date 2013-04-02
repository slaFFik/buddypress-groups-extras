<?php
/*
Plugin Name: BuddyPress Groups Extras
Plugin URI: http://ovirium.com/
Description: Adding extra fields and pages, menu sorting and other missing functionality to groups
Version: 3.4
Author: slaFFik
Author URI: http://cosydale.com/
*/
define('BPGE_VERSION',    '3.4');
define('BPGE_FIELDS',     'bpge_fields');
define('BPGE_FIELDS_SET', 'bpge_fields_set');
define('BPGE_GFIELDS',    'bpge_gfields');
define('BPGE_GPAGES',     'gpages');
define('BPGE_URL',        plugins_url('_inc', __FILE__ )); // link to all assets, with /
define('BPGE_PATH',       plugin_dir_path(__FILE__)); // with /

/**
 * What to do on activation
 */
register_activation_hook( __FILE__, 'bpge_activation');
function bpge_activation() {
    // some activation defaults
    $bpge['groups']    = 'all';
    $bpge['uninstall'] = 'no';
    $bpge['re']        = '1';

    add_option('bpge', $bpge, '', 'yes');
}

/**
 * What to do on deactivation
 */
register_deactivation_hook( __FILE__, 'bpge_deactivation');
function bpge_deactivation() {
    $bpge = bp_get_option('bpge');

    if($bpge['uninstall'] == 'yes'){
        delete_option('bpge');
        global $wpdb, $bp;
        $wpdb->query("DELETE FROM {$wpdb->options}
                        WHERE `option_name` LIKE 'bpge%%'");
        $post_types = "'" . implode("','", array(BPGE_FIELDS, BPGE_GPAGES)) . "'";
        $wpdb->query("DELETE FROM {$wpdb->posts}
                        WHERE `post_type` IN ({$post_types})");
        $group_meta = $bp->table_prefix . 'bp_groups_groupmeta';
        $wpdb->query("DELETE FROM {$group_meta}
                        WHERE `meta_key` IN ('bpge_fields', 'bpge_pages', 'bpge_nav_order')");
    }
}

/**
 * i18n: Load languages
 */
add_action ('plugins_loaded', 'bpge_load_textdomain', 7 );
function bpge_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/bpge-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('bpge', $mofile);
}

/**
 * The main loader - BPGE Engine
 */
// dirty hack #1 to work around BP bug: http://buddypress.trac.wordpress.org/ticket/4072
add_action('init','bpge_pre_load');
function bpge_pre_load(){
    global $bpge;

    if (defined('BP_VERSION'))
        $bpge = bp_get_option('bpge');

    // gpages
    bpge_register_groups_pages();
    // bpge_gfields
    bpge_register_fields();
    // bpge_fields_set
    bpge_register_set();
    // bpge_fields
    bpge_register_set_fields();

    return;
}

add_action( 'bp_init', 'bpge_load' );
function bpge_load(){
    global $bp, $bpge;

    // dirty hack #2
    if (!$bpge)
        return;

    // scripts and styles
    require ( BPGE_PATH . '/core/cssjs.php');
    require ( BPGE_PATH . '/core/ajax.php');

    // admin interface
    if ( is_admin() ){
        require ( BPGE_PATH . '/core/admin.php');
    }else{
        // the core
        if ( bp_is_group() ) {
            if(
                (is_string($bpge['groups']) && $bpge['groups'] == 'all' ) ||
                (is_array($bpge['groups']) && in_array($bp->groups->current_group->id, $bpge['groups']) )
            ){
                require ( BPGE_PATH . '/core/loader.php');
            }else{
                $bp->no_extras = true;
            }
        }
    }

    do_action('bpge_load');
}

// reorder group nav links
add_action('bp_init', 'bpge_nav_order');
function bpge_nav_order(){
    global $bp, $bpge;

    if (!$bpge)
        $bpge = bp_get_option('bpge');

    if ( bp_is_group() && bp_is_single_item()){
        $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');

        if (!empty($order) && is_array($order)){
            foreach($order as $slug => $position){
                $bp->bp_options_nav[$bp->groups->current_group->slug][$slug]['position'] = $position;
            }
        }

        do_action('bpge_nav_order');
    }
}

add_filter('bp_groups_default_extension','bpge_landing_page');
function bpge_landing_page($old_slug){
    global $bp, $bpge;
        $new_slug = $old_slug;

    if ( bp_is_group() && bp_is_single_item() &&
         in_array($bp->groups->current_group->id, (array)$bpge['groups'])
    ){
        // get all pages - take the first
        $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');
        if(is_array($order) && !empty($order))
            $new_slug = reset(array_flip($order));
    }

    return apply_filters('bpge_landing_page', $new_slug);
}


/**
 * Several hooks to fix some places
 */
// Register group fields
function bpge_register_fields(){
    $labels = array(
        'name'               => __('Groups Fields', 'bpge'),
        'singular_name'      => __('Groups Field', 'bpge'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Groups Fields', 'bpge')
    );
    $args = array(
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
        'supports'            => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'comments')
    );
    register_post_type(BPGE_GFIELDS, $args);
}
// Register set of fields post types
function bpge_register_set(){
    $labels = array(
        'name'               => __('Sets of Fields', 'bpge'),
        'singular_name'      => __('Set of Fields', 'bpge'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Sets of Fields', 'bpge')
    );
    $args = array(
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
        'supports'            => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'comments')
    );
    register_post_type(BPGE_FIELDS_SET, $args);
}
// Register groups fields post type, where all their content will be stored
function bpge_register_set_fields(){
    $labels = array(
        'name'               => __('Groups Fields', 'bpge'),
        'singular_name'      => __('Groups Field', 'bpge'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Groups Fields', 'bpge')
    );
    $args = array(
        'labels'              => $labels,
        'description'         => __('Displaying fields that were created in all community groups', 'bpge'),
        'public'              => true,
        'show_in_menu'        => false,
        'exclude_from_search' => true,
        'show_in_nav_menus'   => false,
        'menu_position'       => 100,
        'hierarchical'        => true,
        'query_var'           => true,
        'rewrite'             => false,
        'capability_type'     => 'page',
        'supports'            => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'comments')
    );
    register_post_type(BPGE_FIELDS, $args);
}

// Register groups pages post type, where all their content will be stored
function bpge_register_groups_pages(){
    $labels = array(
        'name'               => __('Groups Pages', 'bpge'),
        'singular_name'      => __('Groups Page', 'bpge'),
        'add_new'            => __('Add New', 'bpge'),
        'add_new_item'       => __('Add New Page', 'bpge'),
        'edit_item'          => __('Edit Page', 'bpge'),
        'new_item'           => __('New Page', 'bpge'),
        'view_item'          => __('View Page', 'bpge'),
        'search_items'       => __('Search Groups Pages', 'bpge'),
        'not_found'          => __('No groups pages found', 'bpge'),
        'not_found_in_trash' => __('No groups pages found in Trash', 'bpge'),
        'parent_item_colon'  => '',
        'menu_name'          => __('Groups Pages', 'bpge')
    );
    $args = array(
        'labels'              => $labels,
        'description'         => __('Displaying pages that were created in all community groups', 'bpge'),
        'public'              => true,
        'show_in_menu'        => true,
        'exclude_from_search' => true,
        'show_in_nav_menus'   => false,
        'menu_position'       => 100,
        'hierarchical'        => true,
        'query_var'           => true,
        'rewrite'             => false,
        'capability_type'     => 'page',
        'supports'            => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'comments')
    );
    register_post_type(BPGE_GPAGES, $args);
}

// hide add new menu and redirect from it to the whole list - do not allow admin to add manually
add_action('admin_menu', 'bpge_gpages_hide_add_new');
function bpge_gpages_hide_add_new() {
    global $submenu;
    unset($submenu['edit.php?post_type=gpages'][10]);
    unset($submenu['edit.php?post_type='.BPGE_FIELDS][10]);
    unset($submenu['edit.php?post_type='.BPGE_FIELDS_SET][10]);
}
add_action('admin_menu','bpge_gpages_redirect_to_all');
function bpge_gpages_redirect_to_all() {
    $result = stripos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=gpages');
    if ($result !== false) {
        wp_redirect(get_option('siteurl') . '/wp-admin/edit.php?post_type=gpages');
    }
}

/**
 * Several Helpers
 */
// Display view
function bpge_view($view, $params = false){
    global $bp, $bpge;

    do_action('bpge_view_pre', $view, $params);

    $params = apply_filters('bpge_view_params', $params);

    if(!empty($params))
        extract($params);

    $path = BPGE_PATH . 'views/'. $view . '.php';
    if(file_exists($path))
        include $path;

    do_action('bpge_view_post', $view, $params);
}

// Helper for generating some titles
function bpge_names($name = 'name'){
    switch ($name){
        case 'title_general':
            return __('Group Extras &rarr; General Settings', 'bpge');
            break;
        case 'title_fields':
            return __('Group Extras &rarr; Fields Management', 'bpge');
            break;
        case 'title_pages':
            return __('Group Extras &rarr; Pages Management', 'bpge');
            break;
        case 'title_fields_add':
            return __('Group Extras &rarr; Add Field', 'bpge');
            break;
        case 'title_fields_edit':
            return __('Group Extras &rarr; Edit Field', 'bpge');
            break;
        case 'title_pages_add':
            return __('Group Extras &rarr; Add Page', 'bpge');
            break;
        case 'title_pages_edit':
            return __('Group Extras &rarr; Edit Page', 'bpge');
            break;
        case 'name':
            return __('Description', 'bpge');
            break;
        case 'nav':
            return __('Extras', 'bpge');
            break;
        case 'gpages':
            return __('Pages', 'bpge');
            break;
    }
}

// Empty defaults
function bpge_get_field_defaults(){
    $field = new Stdclass;

    $field->ID           = '';
    $field->post_title   = '';
    $field->post_content = '';
    $field->post_excerpt = '';
    $field->post_status  = '';
    $field->to_ping      = ''; // required or not
    $field->post_type    = BPGE_GFIELDS;

    return $field;
}

add_action('wp_ajax_bpge', 'bpge_ajax');
function bpge_ajax(){
    require ( BPGE_PATH . 'core/loader.php');
    $load = BPGE::getInstance();
    $load->ajax();
}
