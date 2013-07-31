<?php

/**
 * Get the template file and output its content
 * @param  string   $view       template file name
 * @param  mixed    $params     variables that should be passed to the view
 * @return string               HTML of a page or view
 */
function bpge_view($view, $params = false){
    global $bp, $bpge;

    do_action('bpge_view_pre', $view, $params);

    $params = apply_filters('bpge_view_params', $params);

    if(!empty($params))
        extract($params);

    $theme_parent_file =   get_template_directory() . DS . BPGE . DS . $view .'.php';
    $theme_child_file  = get_stylesheet_directory() . DS . BPGE . DS . $view .'.php';

    // admin area templates should not be overridable via theme files
    // check that file exists in theme folder
    if(!is_admin() && file_exists($theme_child_file)){
        // from child theme
        include $theme_child_file;
    }elseif(!is_admin() && file_exists($theme_parent_file)){
        // from parent theme if no in child
        include $theme_parent_file;
    }else{
        // from plugin folder
        $plugin_file = BPGE_PATH . 'views'. DS . $view . '.php';
        if(file_exists($plugin_file)){
            include $plugin_file;
        }
    }

    do_action('bpge_view_post', $view, $params);
}

/***************************
 * GPages template functions
 ***************************/

/**
 * Convert text into links where possible
 */
function bpge_filter_link_group_data( $field_value) {
    global $bpge;

    $field_value = stripslashes($field_value);

    if(!isset($bpge['field_2_link']) || $bpge['field_2_link'] == 'no'){
        return $field_value;
    }

    if ( !strpos( $field_value, ',' ) && ( count( explode( ' ', $field_value ) ) > 5 ) )
        return $field_value;

    $values = explode( ',', $field_value );

    if ( !empty( $values ) ) {
        foreach ( (array) $values as $value ) {
            $value = trim( $value );

            // If the value is a URL, skip it and just make it clickable.
            if ( preg_match( '@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', $value ) ) {
                $new_values[] = make_clickable( $value );

            // Is not clickable
            } else {

                // More than 5 spaces
                if ( count( explode( ' ', $value ) ) > 5 ) {
                    $new_values[] = $value;

                // Less than 5 spaces
                } else {
                    $search_url   = add_query_arg( array( 's' => urlencode( $value ) ), bp_get_groups_directory_permalink() );
                    $new_values[] = '<a href="' . $search_url . '" rel="nofollow">' . $value . '</a>';
                }
            }
        }

        $values = implode( ', ', $new_values );
    }

    return $values;
}

/**
 * Edit page link
 */
function bpge_the_gpage_edit_link($page_id){
    global $bp, $bpge;
    if (bpge_user_can('group_extras_admin')){
        echo '<div class="edit_link">
                <a target="_blank" title="'.__('Edit link for group admins only', 'bpge').'" href="'.bp_get_group_permalink( $bp->groups->current_group ).'admin/extras/pages-manage/?edit='.$page_id.'">'
                    .__('[Edit this page]', 'bpge') .
                '</a>
            </div>';
    }
}