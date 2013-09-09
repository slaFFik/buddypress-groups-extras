<?php

/**
 * WP Admin area
 * Remove set of fields on appropriate tabs
 */
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
    // check_ajax_referer();

    $method = isset($_REQUEST['method']) ? $_REQUEST['method'] : '';

    do_action('bpge_ajax', $method);

    switch($method){
        case 'reorder_fields':
            global $wpdb;
            parse_str($_REQUEST['field_order'], $field_order );
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
            if(wp_delete_post(intval($_REQUEST['field']), true))
                echo 'deleted';
            die;
            break;

        case 'reorder_pages':
            parse_str($_REQUEST['page_order'], $page_order );
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
            if($deleted = wp_delete_post(intval($_REQUEST['page']), true) ){
                die('deleted');
            }else{
                die('error');
            }
            break;

        case 'apply_set':
            $set_id = intval($_REQUEST['set_id']);
            if ($set_id < 1)
                die('error');

            // get all fields for that set
            $fields = new WP_Query(array(
                                        'post_parent' => $set_id,
                                        'post_type'   => BPGE_FIELDS
                                    ));
            $to_import = array();
            foreach ($fields->posts as $field) {
                $field->desc    = $field->post_content;
                $field->options = get_post_meta($field->ID, 'bpge_field_options', true);
                $field->display = get_post_meta($field->ID, 'bpge_field_display', true);
                unset(
                    $field->post_date,
                    $field->post_date_gmt,
                    $field->post_modified,
                    $field->post_modified_gmt,
                    $field->ID,
                    $field->post_content,
                    $field->post_parent,
                    $field->guid,
                    $field->filter,
                    $field->post_type
                );
                $to_import[] = $field;
            }

            // get all groups ids
            $groups = groups_get_groups(array(
                        'show_hidden'     => true,
                        'populate_extras' => false,
                        'per_page'        => 999
                    ));

            // insert in the loop all the fields where parent_id = group_id
            $mega = array();
            foreach($groups['groups'] as $group){
                foreach($to_import as $field){
                    $data = array();
                    $data = (array) $field;
                    $data['post_parent'] = $group->id;
                    $data['post_type']   = BPGE_GFIELDS;
                    $data['post_status'] = $field->display == 'yes' ? 'publish' : 'draft';
                    $field_id = wp_insert_post($data);

                    if(is_integer($field_id)){
                        // save field description
                        update_post_meta($field_id, 'bpge_field_desc', $data['desc']);

                        // now save options
                        if(!empty($data['options'])){
                            $options = array();
                            foreach($data['options'] as $option){
                                $options[] = htmlspecialchars(strip_tags($option));
                            }
                            update_post_meta( $field_id, 'bpge_field_options', $options );
                        }
                    }
                }
            }

            update_post_meta( $set_id, 'bpge_set_options', array('applied' => 'true') );

            die('success');
            break;

        case 'dismiss_review':
            $bpge = bp_get_option('bpge');
            $bpge['reviewed'] = 'yes';
            bp_update_option('bpge', $bpge);
            die('ok');
            break;
    }

    die('error');
}