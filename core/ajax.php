<?php

add_action('wp_ajax_fields_set_delete','bpge_fields_set_delete');
function bpge_fields_set_delete(){
    global $wpdb;

    $set_id = intval($_POST['id']);
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->posts}` WHERE `ID` = %d", $set_id));
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->posts}` WHERE `post_parent` = %d", $set_id));

    die('deleted');
}