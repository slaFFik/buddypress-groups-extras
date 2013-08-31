<?php

add_action('wp_print_scripts', 'bpge_js_all');
function bpge_js_all() {
    global $bp;

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
        remove_it: "'. __('Remove It','bpge') .'",
        apply_set: "'. __('Do you want to apply this set of fields to all groups on your site?','bpge') .'",
        applied: "'. __('Applied','bpge') .'",
        close: "'. __('Close','bpge') .'",
        yes: "'. __('Yes','bpge') .'",
        no: "'. __('No','bpge') .'",
        success: "'. __('Success','bpge') .'",
        success_apply_set: "'. __('This set of fields was successfully applied to all groups on this site.','bpge') .'",
        error: "'. __('Error','bpge') .'",
        error_apply_set: "'. __('Unfortunately, there was an error while applying this set of fields. Please try again a bit later or recreate the set from scratch. Be aware, that re-applying this set will double fields for those groups that were successful.','bpge') .'"
    };
    </script>';
}

add_action('wp_print_styles', 'bpge_css_all');
function bpge_css_all() {
    if ( bp_is_group() ){
        wp_enqueue_style('BPGE_EXTRA_CSS', BPGE_URL . '/extra-styles.css');
    }
}