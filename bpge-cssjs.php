<?php
add_action('wp_print_scripts', 'bpge_js_all');
function bpge_js_all() {
    global $bp;
    /*
    $bp->action_variable[0] = extras
    $bp->action_variable[1] = fields | pages | fields-manage | pages-manage
    */
    if ($_GET['page'] == 'bpge-admin')
        wp_enqueue_script('BPGE_ADMIN_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-scripts.js', array('jquery') );
        
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
        wp_enqueue_script('BPGE_EXTRA_JS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-scripts.js', array('jquery') );
        // localize js string
        add_action('wp_head', 'bpge_js_localize', 5);
        wp_enqueue_script('jquery-ui-sortable');
    }
    
}

function bpge_js_localize(){
    echo '<script type="text/javascript">
    var bpge = {
        enter_options: "'. __('Please enter options for this Field','bpge') .'",
        option_text: "'. __('Option','bpge') .'",
        remove_it: "'. __('Remove It','bpge') .'"
    };
    </script>';
}

add_action('wp_print_styles', 'bpge_css_all');
function bpge_css_all() {
    global $bp;
        
    if ( $bp->current_component == bp_get_groups_root_slug() && $bp->is_single_item ){
        if (file_exists(WP_PLUGIN_DIR.'/buddypress-groups-extras/_inc/extra-styles.css')){
            wp_enqueue_style('BPGE_EXTRA_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-styles.css');
        }else{
            wp_enqueue_style('BPGE_EXTRA_CSS', WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/extra-styles-dev.css');
        }
    }
}

add_action('admin_head', 'bpge_css_admin');
function bpge_css_admin(){
    global $post_type; 
    
    if ($_GET['post_type'] == 'gpages' || $post_type == 'gpages' || $_GET['page'] == 'bpge-admin') {
        echo "<link type='text/css' rel='stylesheet' href='" . WP_PLUGIN_URL.'/buddypress-groups-extras/_inc/admin-styles.css' . "' />";
    }
}
