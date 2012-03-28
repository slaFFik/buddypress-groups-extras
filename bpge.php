<?php
/*
Plugin Name: BuddyPress Groups Extras
Plugin URI: http://ovirium.com/
Description: Adding extra fields and pages, menu sorting and other missing functionality to groups
Version: 3.2.2
Author: slaFFik
Author URI: http://cosydale.com/
*/
define ('BPGE_VERSION', '3.2.2');

register_activation_hook( __FILE__, 'bpge_activation');
//register_deactivation_hook( __FILE__, 'bpge_deactivation');
function bpge_activation() {
    // some activation defaults
    $bpge['groups'] = 'all';
    $bpge['re']     = '1';
    
    add_option('bpge', $bpge, '', 'yes');
}
function bpge_deactivation() { delete_option('bpge'); }

/* LOAD LANGUAGES */
add_action ('plugins_loaded', 'bpge_load_textdomain', 7 );
function bpge_load_textdomain() {
    $locale = apply_filters('buddypress_locale', get_locale() );
    $mofile = dirname( __File__ )   . "/langs/bpge-$locale.mo";

    if ( file_exists( $mofile ) )
        load_textdomain('bpge', $mofile);
}

/*
 * The main loader - BPGE Engine
 */
// dirty hack #1 to work around BP bug: http://buddypress.trac.wordpress.org/ticket/4072
add_action('init','bpge_pre_load');
function bpge_pre_load(){
    global $bpge;
    
    if (defined('BP_VERSION'))
        $bpge = bp_get_option('bpge');
    
    return;
}

add_action( 'bp_init', 'bpge_load' );
function bpge_load(){
    global $bp, $bpge;

    // dirty hack #2
    if (!$bpge)
        return;
    
    // scripts and styles
    require ( dirname(__File__) . '/bpge-cssjs.php');
    
    // admin interface
    if ( is_admin() ){
        require ( dirname(__File__) . '/bpge-admin.php');
    }else{    
        // the core
        if ( ( is_string($bpge['groups']) && $bpge['groups'] == 'all' ) || 
             ( is_array($bpge['groups']) && in_array($bp->groups->current_group->id, $bpge['groups']) )
           ){
            require ( dirname(__File__) . '/bpge-loader.php');
        }else{
            $bp->no_extras = true;
        }
    }
    // gpages - custom post type
    bpge_register_groups_pages();
    
    do_action('bpge_load');
}

// reorder group nav links
add_action('bp_init', 'bpge_nav_order');
function bpge_nav_order(){
    global $bp, $bpge;

    if (!$bpge)
        return;
    
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item){
        $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');
      
        if (!empty($order) && is_array($order)){
            foreach($order as $slug => $position){
                $bp->bp_options_nav[$bp->groups->current_group->slug][$slug]['position'] = $position;
            }
        }

        do_action('bpge_nav_order');
    }
}

add_filter('bp_default_component_subnav','bpge_landing_page', 10, 2);
function bpge_landing_page($default_subnav_slug, $r){
    global $bp, $bpge;

    // dirty hack #3 - the most important
    if (!$bpge)
        return $default_subnav_slug;
    
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item && in_array($bp->groups->current_group->id, $bpge['groups'])){
        // get all pages - take the first
        $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');
        if(!empty($order)){
            $default_subnav_slug = reset(array_flip($order));
        }
        $bp->current_action = $default_subnav_slug;
    }

    return apply_filters('bpge_landing_page', $default_subnav_slug);
}

// Register groups pages post type, where all their content will be stored
function bpge_register_groups_pages(){
    $labels = array(
        'name'                  => __('Groups Pages', 'bpge'),
        'singular_name'         => __('Groups Page', 'bpge'),
        'add_new'               => __('Add New', 'bpge'),
        'add_new_item'          => __('Add New Page', 'bpge'),
        'edit_item'             => __('Edit Page', 'bpge'),
        'new_item'              => __('New Page', 'bpge'),
        'view_item'             => __('View Page', 'bpge'),
        'search_items'          => __('Search Groups Pages', 'bpge'),
        'not_found'             =>  __('No groups pages found', 'bpge'),
        'not_found_in_trash'    => __('No groups pages found in Trash', 'bpge'), 
        'parent_item_colon'     => '',
        'menu_name'             => __('Groups Pages', 'bpge')
    );
    $args = array(
        'labels'                => $labels,
        'description'           => __('Displaying pages that were created in all community groups', 'bpge'),
        'public'                => true,
        'show_in_menu'          => true, 
        'exclude_from_search'   => true, 
        'show_in_nav_menus'     => false, 
        'menu_position'         => 100,
        'hierarchical'          => true,
        'query_var'             => true,
        'rewrite'               => false,
        'capability_type'       => 'page',
        'supports'              => array('title', 'editor', 'custom-fields', 'page-attributes', 'thumbnail', 'comments')
    ); 
    register_post_type('gpages',$args);
}
// hide add new menu and redirect from it to the whole list - do not allow admin to add manually
add_action('admin_menu', 'bpge_gpages_hide_add_new');
function bpge_gpages_hide_add_new() {
    global $submenu;
    unset($submenu['edit.php?post_type=gpages'][10]);
}
add_action('admin_menu','bpge_gpages_redirect_to_all');
function bpge_gpages_redirect_to_all() {
    $result = stripos($_SERVER['REQUEST_URI'], 'post-new.php?post_type=gpages');
    if ($result !== false) {
        wp_redirect(get_option('siteurl') . '/wp-admin/edit.php?post_type=gpages');
    }
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
    }
}

/*
 * Personal debug functions
 */
//add_action('bp_adminbar_menus', 'bpge_queries',99);
function bpge_queries(){
    echo '<li class="no-arrow"><a>'.get_num_queries() . ' queries | ';
    echo round(memory_get_usage() / 1024 / 1024, 2) . 'Mb</a></li>';
}

if(!function_exists('print_var')){
    function print_var($var, $die = false){
        echo '<pre>';
        if (empty($var))
            var_dump($var);
        else
            print_r($var);
        echo '</pre>';
        if ($die)
            die;
    }
}
