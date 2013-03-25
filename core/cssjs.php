<?php

add_action('wp_print_scripts', 'bpge_js_all');
function bpge_js_all() {
    global $bp;

    if (is_admin() && isset($_GET['page']) && $_GET['page'] == 'bpge-admin')
        wp_enqueue_script('BPGE_ADMIN_JS', BPGE_URL . '/admin-scripts.js', array('jquery') );

    if ( bp_is_group() && 'admin' == $bp->current_action && $bp->action_variables[0] == 'extras' ){
        wp_enqueue_script('BPGE_EXTRA_JS', BPGE_URL . '/extra-scripts.js', array('jquery') );
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
    if ( bp_is_group() ){
        if (file_exists(BPGE_PATH . '/_inc/extra-styles.css')){
            wp_enqueue_style('BPGE_EXTRA_CSS', BPGE_URL . '/extra-styles.css');
        }else{
            wp_enqueue_style('BPGE_EXTRA_CSS', BPGE_URL . '/extra-styles-dev.css');
        }
    }
}

add_action('admin_head', 'bpge_css_admin');
function bpge_css_admin(){
    global $post_type;

    if ( ( isset($_GET['post_type']) && $_GET['post_type'] == 'gpages')
        || $post_type == 'gpages'
        || (isset($_GET['page']) && $_GET['page'] == 'bpge-admin')
    ) {
        echo "<link type='text/css' rel='stylesheet' href='" . BPGE_URL . '/admin-styles.css' . "' />";
    }
}
