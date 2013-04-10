<?php

add_action('wp_ajax_fields_set_delete','bpge_fields_set_delete');
function bpge_fields_set_delete(){
    global $wpdb;

    $set_id = intval($_POST['id']);
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->posts}` WHERE `ID` = %d", $set_id));
    $wpdb->query($wpdb->prepare("DELETE FROM `{$wpdb->posts}` WHERE `post_parent` = %d", $set_id));

    die('deleted');
}

/**
 * All main non-admin ajax requests
 */
add_action('wp_ajax_bpge', 'bpge_ajax');
function bpge_ajax(){
    global $bp;
    $method = isset($_POST['method']) ? $_POST['method'] : '';

    do_action('bpge_ajax', $method);

    switch($method){
        case 'reorder_fields':
            global $wpdb;
            parse_str($_POST['field_order'], $field_order );
            $fields = bpge_get_group_fields('any');

            // reorder all fields accordig to new positions
            $i = 1;
            foreach($field_order['position'] as $field_id){
                $wpdb->update(
                        $wpdb->posts,
                        array('menu_order' => $i),
                        array('ID' => $field_id),
                        array('%d'),
                        array('%d')
                    );
                $i++;
            }
            die('saved');
            break;

        case 'delete_field':
            if(wp_delete_post(intval($_POST['field']), true))
                echo 'deleted';
            die;
            break;

        case 'reorder_pages':
            parse_str($_POST['page_order'], $page_order );
            // update menu_order for each gpage
            foreach($page_order['position'] as $index => $page_id){
                wp_update_post(array(
                    'ID'         => $page_id,
                    'menu_order' => $index
                ));
            }
            die('saved');
            break;

        case 'delete_page':
            if($deleted = wp_delete_post($_POST['page'], true) ){
                die('deleted');
            }else{
                die('error');
            }
            break;
    }
    die('error');
}
