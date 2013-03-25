<?php

add_action('wp_ajax_set_fields_delete','set_fields_delete');
function set_fields_delete(){
    $sets_fields = get_option('bpge_def_fields');
    unset($sets_fields[$_POST['slug_set_fields']]);
    //print_var($sets_fields);
    if(!empty($sets_fields)){
        update_option('bpge_def_fields',$sets_fields);
    }else{
        delete_option('bpge_def_fields');
    }
    delete_option($_POST['slug_set_fields']);
    exit;
}