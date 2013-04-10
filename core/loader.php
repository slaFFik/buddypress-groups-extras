<?php

class BPGE extends BP_Group_Extension {
    var $bpge = false;

    var $slug                 = 'extras';
    var $page_slug            = 'gpages';
    var $name                 = false;
    var $nav_item_name        = false;
    var $gpages_item_name     = false;

    var $home_name            = false;

    var $gpage_id             = false;

    /* By default - Is it visible to non-members of a group? Options: public/private */
    var $visibility           = false;

    var $create_step_position = 5;
    var $nav_gpages_position  = 12;
    var $nav_item_position    = 14;

    var $enable_create_step   = false; // will set to true in future version
    var $enable_nav_item      = false;
    var $enable_gpages_item   = false;
    var $enable_edit_item     = true;

    var $display_hook         = 'groups_extras_group_boxes';
    var $template_file        = 'groups/single/plugins';

    var $bpge_glob            = false;

    function __construct(){
        global $bp;

        $this->bpge_glob = bp_get_option('bpge');

        // we are on a single group page
        if (!empty($bp->groups->current_group)){
            // populate extras data in global var
            $bpge = groups_get_groupmeta($bp->groups->current_group->id, 'bpge');
            if(!empty($bpge)){
                $bp->groups->current_group->extras = $bpge;
            }
        }

        if(!empty($bp->groups->current_group) && !empty($bp->groups->current_group->extras['gpage_id'])){
            $this->gpage_id = $bp->groups->current_group->extras['gpage_id'];
        }elseif(!empty($bp->groups->current_group)){
            $this->gpage_id = $this->get_gpage_by('group_id');
        }

        // Display or Hide top menu from group non-members
        $this->visibility      = isset($bp->groups->current_group->extras['display_page']) ? $bp->groups->current_group->extras['display_page'] : 'public';
        $this->enable_nav_item = (isset($bp->groups->current_group->extras['display_page']) && $bp->groups->current_group->extras['display_page'] == 'public') ? true : false;

        if (bp_is_single_item() && !empty($bp->groups->current_group) && empty($bp->groups->current_group->extras['display_page_layout'])){
            $bp->groups->current_group->extras['display_page_layout'] = 'profile';
        }

        // gPages Page
        $this->gpages_item_name   = isset($bp->groups->current_group->extras['gpage_name']) ? $bp->groups->current_group->extras['gpage_name'] : bpge_names('gpages');
        $this->enable_gpages_item = (isset($bp->groups->current_group->extras['display_gpages']) && $bp->groups->current_group->extras['display_gpages'] == 'public') ? true : false;

        // In Admin
        $this->name = bpge_names('nav');
        // Public page
        $this->nav_item_name = isset($bp->groups->current_group->extras['display_page_name']) ? $bp->groups->current_group->extras['display_page_name'] : bpge_names('nav');
        // Home page
        if( !empty($bp->groups->current_group->extras['home_name']) ){
            $this->home_name = $bp->groups->current_group->extras['home_name'];
            $bp->bp_options_nav[$bp->groups->current_group->slug]['home']['name'] = $this->home_name;
        }

        add_action('groups_custom_group_fields_editable', array($this, 'edit_group_fields'));
        add_action('groups_group_details_edited', array($this, 'edit_group_fields_save'));

        if ( $this->enable_gpages_item ) {
            if ( bp_is_group() && bp_is_single_item() ) {
                $order = groups_get_groupmeta($bp->groups->current_group->id, 'bpge_nav_order');
                if(empty($order[$this->page_slug])){
                    $order[$this->page_slug] = 99;
                }
                if(!empty($order['extras'])){
                    $this->nav_item_position = $order['extras'];
                }
                bp_core_new_subnav_item( array(
                        'name'            => $this->gpages_item_name,
                        'slug'            => $this->page_slug,
                        'parent_slug'     => $bp->groups->current_group->slug,
                        'parent_url'      => bp_get_group_permalink( $bp->groups->current_group ),
                        'position'        => $order[$this->page_slug],
                        'item_css_id'     => $this->page_slug,
                        'screen_function' => array(&$this, 'gpages')
                ) );
            }
        }
    }

    // Public page with already saved content
    function display() {
        global $bp;
        // get all to display
        $fields = bpge_get_group_fields('publish');

        if (empty($fields))
            return false;

        if ( isset($bp->groups->current_group->extras['display_page_layout']) &&
                   $bp->groups->current_group->extras['display_page_layout'] == 'plain' ){
            echo '<div class="extra-data">';
            foreach($fields as $field){
                $field->desc    = get_post_meta($field->ID, 'bpge_field_desc', true);
                $field->options = json_decode($field->post_content);

                echo '<h4 title="' . ( ! empty($field->desc)  ? esc_attr($field->desc) : '')  .'">' . $field->post_title .'</h4>';

                if ( is_array($field->options) )
                    $data = implode(', ', $field->options);
                else
                    $data = $field->post_content;
                echo '<p>' . $data . '</p>';
            }
            echo '</div>';
        }else{
            echo '<table class="profile-fields zebra">';
                foreach($fields as $field){
                    $field->desc    = get_post_meta($field->ID, 'bpge_field_desc', true);
                    $field->options = json_decode($field->post_content);

                    echo '<tr><td class="label" title="' . ( ! empty($field->desc)  ? esc_attr($field->desc) : '')  .'">' . $field->post_title .'</td>';

                    if ( is_array($field->options) )
                        $data = implode(', ', $field->options);
                    else
                        $data = $field->post_content;
                    echo '<td class="data">' . $data . '</td></tr>';
                }
            echo '</table>';
        }
    }

    /************************************************************************/

    // Publis gPages screen function
    function gpages(){
        add_action( 'bp_before_group_body', array( &$this, 'gpages_screen_nav' ) );
        add_action( 'bp_template_content', array( &$this, 'gpages_screen_content' ) );
        do_action( 'bpge_gpages', $this );
        bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'groups/single/plugins' ) );
    }

    function gpages_screen_nav(){
        global $bp;

        $pages = $this->get_all_gpages('publish');

        if (empty($bp->action_variables)){
            global $wpdb;
            $sql = "SELECT `post_name` FROM {$wpdb->prefix}posts
                        WHERE `post_parent` = {$bp->groups->current_group->extras['gpage_id']}
                            AND `post_type` = '{$this->page_slug}'
                        ORDER BY `menu_order` ASC
                        LIMIT 1";
            $bp->action_variables[0] = $wpdb->get_var($sql);
        }

        // display list of pages if there are more than of 1 of them
        if ( count($pages) > 1 ) {
            echo '<div role="navigation" id="subnav" class="item-list-tabs no-ajax">
                <ul>';
                        foreach($pages as $page){
                            echo '<li '. (($bp->action_variables[0] == $page->post_name) ? 'class="current"' : '') .'>
                                <a href="'.bp_get_group_permalink( $bp->groups->current_group ) . $this->page_slug .'/'. $page->post_name.'">'.$page->post_title.'</a>
                            </li>';
                        }
                    do_action('bpge_gpages_nav_in', $this, $pages);
                echo '</ul>
            </div>';
        }
        do_action('bpge_gpages_nav_after', $this, $pages);
    }

    function gpages_screen_content(){
        global $bp, $wpdb;
        $gpage_id = $bp->groups->current_group->extras['gpage_id'];

        $sql  = "SELECT * FROM {$wpdb->prefix}posts
                    WHERE `post_name` = %s
                        AND `post_type` = %s
                        AND `post_parent` = %d";
        $page = $wpdb->get_row($wpdb->prepare($sql, $bp->action_variables[0], $this->page_slug, $gpage_id));

        if(!empty($page)){
            do_action('bpge_gpages_content_display_before', $this, $page);

            setup_postdata($page);
            echo '<div class="gpage">';
                apply_filters('bpge_gpages_content', the_content());

                if (bp_group_is_admin()){
                    echo '<div class="edit_link"><a target="_blank" title="'.__('Edit link for group admins only', 'bpge').'" href="'.bp_get_group_permalink( $bp->groups->current_group ).'admin/extras/pages-manage/?edit='.$page->ID.'">[Edit this page]</a></div>';
                }
            echo '</div>';
        }

        do_action('bpge_gpages_content_display_after', $this, $page);
    }

    /************************************************************************/

    // Display exra fields on edit group details page
    function edit_group_fields(){
        $fields = bpge_get_group_fields('publish');

        if (empty($fields))
            return false;

        foreach( $fields as $field ){
            $field->desc    = get_post_meta($field->ID, 'bpge_field_desc', true);
            $field->options = get_post_meta($field->ID, 'bpge_field_options', true);

            $req_text = $req_class = false;
            if ( $field->pinged == 'req' ) {
                $req_text  = '* ';
                $req_class = 'required';
            }

            echo '<label class="'.$req_class.'" for="bpge-' . $field->ID . '">' . $req_text . $field->post_title . '</label>';

            switch($field->post_excerpt){
                case 'text':
                    echo '<input id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '" type="text" value="' . $field->post_content . '" />';
                    break;
                case 'textarea':
                    echo '<textarea id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '">' . $field->post_content . '</textarea>';
                    break;
                case 'select':
                    echo '<select id="bpge-' . $field->ID . '" name="bpge-' . $field->ID . '">';
                        echo '<option value="">-------</option>';
                        foreach($field->options as $option){
                            echo '<option ' . ($field->post_content == $option ? 'selected="selected"' : '') .' value="' . $option . '">' . $option . '</option>';
                        }
                    echo '</select>';
                    break;
                case 'checkbox':
                    echo '<span id="bpge-' . $field->ID . '">';
                        $content = json_decode($field->post_content);
                        foreach($field->options as $option){
                            echo '<input ' . ( in_array($option, (array)$content) ? 'checked="checked"' : '') .' type="checkbox" name="bpge-' . $field->ID . '[]" value="' . $option . '"> ' . $option . '<br />';
                        }
                    echo '</span>';
                    break;
                case 'radio':
                    echo '<span id="bpge-' . $field->ID . '">';
                        foreach($field->options as $option){
                            echo '<input ' . ($field->post_content == $option ? 'checked="checked"' : '') .' type="radio" name="bpge-' . $field->ID . '" value="' . $option . '"> ' . $option . '<br />';
                        }
                    echo '</span>';
                    if ($req_text)
                        echo '<a class="clear-value" href="javascript:clear( \'bpge-' . $field->ID . '\' );">'. __( 'Clear', 'bpge' ) .'</a>';
                    break;
                case 'datebox':
                    echo '<input id="bpge-' . $field->ID . '" class="datebox" name="bpge-' . $field->ID . '" type="text" value="' . $field->post_content . '" />';
                    break;
            }
            if ( ! empty($field->desc) ) echo '<p class="description">' . $field->desc . '</p>';
        }
        do_action('bpge_group_fields_edit', $this, $fields);
    }

    // Save extra fields in DB
    function edit_group_fields_save($group_id){
        global $wpdb, $bp;

        // If the edit form has been submitted, save the edited details
        if (
            (bp_is_group() && 'edit-details' == $bp->action_variables[0])
         && ($bp->is_item_admin || $bp->is_item_mod)
         && isset( $_POST['save'] )
        ) {
            /* Check the nonce first. */
            if ( !check_admin_referer( 'groups_edit_group_details' ) )
                return false;

            $to_save = array();

            foreach($_POST as $data => $value){
                if ( substr($data, 0, 5) === 'bpge-' )
                    $to_save[$data] = $value;
            }

            foreach($to_save as $key => $value){
                $key = substr($key, 5);
                if ( !is_array($value) ) {
                    $data = wp_kses_data($value);
                    $data = force_balance_tags($value);
                }else{
                    $value = array_map("wp_kses_data", $value);
                    $value = array_map("force_balance_tags", $value);
                    $data = json_encode($value);
                }

                $wpdb->update(
                    $wpdb->posts,
                    array(
                        'post_content' => $data,  // string
                    ),
                    array( 'ID' => $key ),
                    array( '%s' ), // data format
                    array( '%d' ) // where format
                );

            }

            do_action('bpge_group_fields_save', $this, $to_save);
        }
    }

    function widget_display() {
        do_action('bpge_widget_display', $this);
    }

    /************************************************************************/

    // Admin area - Main
    function edit_screen() {
        global $bp;

        if ( 'admin' == $bp->current_action && isset($bp->action_variables[1]) && $bp->action_variables[1] == 'fields' ) {
            $this->edit_screen_fields($bp);
        }elseif ( 'admin' == $bp->current_action && isset($bp->action_variables[1]) && $bp->action_variables[1] == 'pages' ) {
            $this->edit_screen_pages($bp);
        }elseif ( 'admin' == $bp->current_action && isset($bp->action_variables[1]) && $bp->action_variables[1] == 'fields-manage' ) {
            $this->edit_screen_fields_manage($bp);
        }elseif ( 'admin' == $bp->current_action && isset($bp->action_variables[1]) && $bp->action_variables[1] == 'pages-manage' ) {
            $this->edit_screen_pages_manage($bp);
        }else{
            $this->edit_screen_general($bp);
        }
    }

    // Admin area - General Settings
    function edit_screen_general($bp){
        if(!isset($bp->groups->current_group->extras['display_page']))
            $bp->groups->current_group->extras['display_page'] = 'public';
        if(!isset($bp->groups->current_group->extras['display_gpages']))
            $bp->groups->current_group->extras['display_gpages'] = 'public';
        if(!isset($bp->groups->current_group->extras['display_page_layout']))
            $bp->groups->current_group->extras['display_page_layout'] = 'plain';

        $this->edit_screen_head('general');

        echo '<p>';
            echo '<label for="group_extras_display_name">'.__('Please specify the page name, where all fields will be displayed','bpge').'</label>';
            echo '<input type="text" value="'.$this->nav_item_name.'" name="group-extras-display-name">';
        echo '</p>';

        echo '<p>';
            echo '<label for="group_extras_display">'.sprintf(__('Do you want to make <strong>"%s"</strong> page public? Everyone will see this page.','bpge'), $this->nav_item_name).'</label>';
            echo '<input type="radio" value="public" '.checked($bp->groups->current_group->extras['display_page'], 'public', false).' name="group-extras-display"> '.__('Show it', 'bpge').'<br />';
            echo '<input type="radio" value="private" '.checked($bp->groups->current_group->extras['display_page'], 'private', false).' name="group-extras-display"> '. __('Hide it', 'bpge');
        echo '</p>';

        echo '<p>';
            echo '<label for="group_extras_display_layout">'.sprintf(__('Please choose the layout for <strong>"%s"</strong> page','bpge'), $this->nav_item_name).'</label>';
            echo '<input type="radio" value="plain" '.checked($bp->groups->current_group->extras['display_page_layout'], 'plain', false).' name="group-extras-display-layout"> '.__('Plain (field title and its data below)', 'bpge').'<br />';
            echo '<input type="radio" value="profile" '.checked($bp->groups->current_group->extras['display_page_layout'], 'profile', false).' name="group-extras-display-layout"> '. __('Profile style (in a table)', 'bpge');
        echo '</p>';

        echo '<hr />';

        echo '<p>';
            echo '<label for="group_extras_display_name">'.__('Please specify the page name, where all custom pages will be displayed','bpge').'</label>';
            echo '<input type="text" value="'.$this->gpages_item_name.'" name="group-gpages-display-name">';
        echo '</p>';

        echo '<p>';
            echo '<label for="group_extras_display">'.sprintf(__('Do you want to make <strong>"%s"</strong> page public (extra group pages will be displayed there)?','bpge'), $this->gpages_item_name).'</label>';
            echo '<input type="radio" value="public" '.checked($bp->groups->current_group->extras['display_gpages'], 'public', false).' name="group-gpages-display"> '.__('Show it', 'bpge').'<br />';
            echo '<input type="radio" value="private" '.checked($bp->groups->current_group->extras['display_gpages'], 'private', false).' name="group-gpages-display"> '. __('Hide it', 'bpge');
        echo '</p>';

        echo '<hr />';

        echo '<label>'.__('You can reorder here all navigation links in this group. The first item will become a landing page for this group. Save changes after reordering.<br />Please do NOT make Admin pages on first place - that will cause display problems.', 'bpge') .'</label>';
        $group_nav = $bp->bp_options_nav[$bp->groups->current_group->slug];
        echo '<ul id="nav-sortable">';
            foreach($group_nav as $nav){
                if(isset($nav['slug']) && $nav['slug'] == 'home'){
                    $home_name = $nav['name'];
                }
                if(empty($nav['position'])){
                    $nav['position'] = 99;
                }
                if(isset($nav['name'])){
                    echo '<li id="position_'.$nav['position'].'" class="default">
                            <strong>' . $nav['name'] .'</strong>
                        </li>';
                }
            }
            echo '<input type="hidden" name="bpge_group_nav_position" value="" />';
        echo '</ul>';

        echo '<p>';
            echo '<label for="group_extras_home_name">'.__('Rename the Home group page - Activity (for example) is far better.','bpge').'</label>';
            echo '<input type="text" value="'.($this->home_name?$this->home_name:$home_name).'" name="group-extras-home-name">';
        echo '</p>';

        echo '<div class="clear">&nbsp;</div>';

        echo '<p><input type="submit" name="save_general" id="save" value="'.__('Save Changes','bpge').'"></p>';
        wp_nonce_field('groups_edit_group_extras');
    }

    // Admin area - All Fields
    function edit_screen_fields($bp){
        $this->edit_screen_head('fields');

        // get all groups fields
        $fields = bpge_get_group_fields('any');

        // get set of fields
        $def_set_fields = get_posts(array(
                            'posts_per_page' => 50,
                            'numberposts'    => 50,
                            'order'          => 'ASC',
                            'orderby'        => 'ID',
                            'post_type'      => BPGE_FIELDS_SET
                        ));

        if(empty($fields)){
            $this->notices('no_fields');
        }

        if(!empty($def_set_fields)){
            bpge_view('front_fields_import', array('def_set_fields' => $def_set_fields));
        }

        bpge_view('front_fields_list', array('fields' => $fields, 'slug' => $this->slug));
    }

    function get_all_gpages($post_status = 'any'){
        global $bp;
        $args = array(
                    'post_parent'   => $bp->groups->current_group->extras['gpage_id'],
                    'post_type'     => $this->page_slug,
                    'orderby'       => 'menu_order',
                    'order'         => 'ASC',
                    'numberposts'   => 999,
                    'post_status'   => $post_status
                );
        return get_posts( $args );
    }

    // Admin area - All Pages
    function edit_screen_pages($bp){
        $this->edit_screen_head('pages');

        $pages = $this->get_all_gpages();

        if(empty($pages)){
            $this->notices('no_pages');
            return false;
        }

        echo '<ul id="pages-sortable">';
            foreach($pages as $page){
                echo '<li id="position_'.$page->ID.'" class="default">
                        <strong>' . $page->post_title .'</strong> &rarr; ' . (($page->post_status == 'publish')?__('displayed','bpge'):__('<u>not</u> displayed','bpge')) . '
                        <span class="items-link">
                            <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . $this->page_slug . '/' . $page->post_name . '" class="button" target="_blank" title="'.__('View this page live','bpge').'">'.__('View', 'bpge').'</a>&nbsp;
                            <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug . '/pages-manage/?edit=' . $page->ID . '" class="button" title="'.__('Change its title, content etc','bpge').'">'.__('Edit', 'bpge').'</a>&nbsp;
                            <a href="#" class="button delete_page" title="'.__('Delete this item and all its content', 'bpge').'">'.__('Delete', 'bpge').'</a>
                        </span>
                    </li>';
            }
        echo '</ul>';

    }

    // Add / Edit 1 field form
    function edit_screen_fields_manage($bp){
        // if Editing page - get data
        if (isset($_GET['edit']) && !empty($_GET['edit'])){
            $field = get_post(intval($_GET['edit']));
            $field->desc = get_post_meta($field->ID, 'bpge_field_desc', true);
        }else{
            // get empty values for Adding page
            $field = bpge_get_field_defaults();
        }

        $this->edit_screen_head('fields-manage');

        bpge_view('front_fields_add_edit', array('field' => $field, 'nav_item_name' => $this->nav_item_name));
    }

    // Add / Edit pages form
    function edit_screen_pages_manage($bp){
        $edit = false;
        $page = new Stdclass;
        $page->ID = $page->post_title = $page->post_name = $page->post_content = $page->post_status = '';
        if (isset($_GET['edit']) && !empty($_GET['edit']) && is_numeric($_GET['edit'])){
            $edit = true;
            $page = $this->get_gpage_by('id', $_GET['edit']);
        }

        $this->edit_screen_head('pages-manage');

        echo '<div class="box_page">';
        echo '<p>';
            echo '<label>' . __('Page Title', 'bpge') . '</label>';
            echo '<input type="text" value="'.$page->post_title.'" name="extra-page-title">';
        echo '</p>';

        if ($edit){
            echo '<p>';
                echo '<label>' . __('Page Slug', 'bpge') . '</label>';
                echo '<input type="text" value="'.$page->post_name.'" name="extra-page-slug">';
            echo '</p>';
        }

        echo '<p style="margin:0;">';
            echo '<label>' . __('Page Content', 'bpge') . '</label>';
            if (function_exists('wp_editor') && isset($this->bpge_glob['re']) && $this->bpge_glob['re'] == '1' ) {
                wp_editor(
                        $page->post_content, // initial content
                        'post_content', // ID attribute value for the textarea
                        array(
                            'media_buttons' => false,
                            'textarea_name' => 'extra-page-content',
                        )
                );
            } else {
                echo '<textarea name="extra-page-content" id="post_content">'.$page->post_content.'</textarea>';
            }
        echo '</p>';

        echo '<p>';
            echo '<label for="extra-page-display">' . __('Should this page be displayed for public in group navigation?','bpge') . '</label>';
                $checked = 'checked="checked"';
                echo '<input type="radio" value="publish" '.($page->post_status == 'publish'?$checked:'').' name="extra-page-status"> '.__('Display it', 'bpge').'<br />';
                echo '<input type="radio" value="draft" '.($page->post_status == 'publish'?'':$checked).' name="extra-page-status"> '. __('Do NOT display it', 'bpge');
        echo '</p>';

        do_action('bpge_page_manage', $this, $page);

        if (!$edit){
            echo '<p><input type="submit" name="save_pages_add" id="save" value="'.__('Create New &rarr;','bpge').'" /></p>';
        }else{
            echo '<input type="hidden" name="extra-page-id" value="' . $page->ID . '">';
            echo '<p><input type="submit" name="save_pages_edit" id="save" value="'.__('Save Changes &rarr;','bpge').'" /></p>';
        }
        echo '</div>';
        wp_nonce_field('groups_edit_group_extras');
    }

    // Save all changes into DB
    function edit_screen_save() {
        global $bp;

        if ( bp_is_group() && 'extras' == $bp->action_variables[0] ) {
            if ( !$bp->is_item_admin )
                return false;

            $group_link = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug;

            // Import set of fields
            if(isset($_POST['approve_import']) && $_POST['approve_import'] == true && !empty($_POST['import_def_set_fields'])){
                $this->import_set_fields();
                die;
            }

            // Save general settings
            if ( isset($_POST['save_general'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                $meta = $bp->groups->current_group->extras;

                $meta['display_page'] = $_POST['group-extras-display'];
                $meta['display_page_name'] = stripslashes(strip_tags($_POST['group-extras-display-name']));
                $meta['display_page_layout'] = $_POST['group-extras-display-layout'];

                $meta['gpage_name'] = stripslashes(strip_tags($_POST['group-gpages-display-name']));
                $meta['display_gpages'] = $_POST['group-gpages-display'];

                $meta['home_name'] = stripslashes(strip_tags($_POST['group-extras-home-name']));

                // now save nav order
                if(!empty($_POST['bpge_group_nav_position'])){
                    // preparing vars
                    parse_str($_POST['bpge_group_nav_position'], $tab_order );
                    $nav_old = $bp->bp_options_nav[$bp->groups->current_group->slug];
                    $order = array();
                    $pos = 1;

                    // update menu_order for each nav item
                    foreach($tab_order['position'] as $index => $old_position){
                        foreach($nav_old as $nav){
                            if ($nav['position'] == $old_position){
                                $order[$nav['slug']] = $pos;
                            }
                            $pos++;
                        }
                    }

                    // save to DB
                    groups_update_groupmeta($bp->groups->current_group->id, 'bpge_nav_order', $order);
                }

                do_action('bpge_save_general', $this, $meta);

                // Save into groupmeta table some general settings
                groups_update_groupmeta( $bp->groups->current_group->id, 'bpge', $meta );

                $this->notices('settings_updated');

                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/' );
            }

            // Save new field
            if ( isset($_POST['save_fields_add'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                $new               = new Stdclass;
                $new->post_title   = apply_filters('bpge_new_field_title',    $_POST['extra-field-title']);
                $new->post_excerpt = apply_filters('bpge_new_field_type',     $_POST['extra-field-type']);
                $new->pinged       = apply_filters('bpge_new_field_required', $_POST['extra-field-required']);
                $new->post_status  = apply_filters('bpge_new_field_display',  $_POST['extra-field-display']);
                $new->post_parent  = apply_filters('bpge_new_field_group',    $_POST['group-id']);
                $new->post_type    = apply_filters('bpge_new_field_type',     BPGE_GFIELDS);

                if(!empty($_POST['options'])){
                    $options = array();
                    foreach($_POST['options'] as $option){
                        $options[] = htmlspecialchars(strip_tags($option));
                    }
                }

                do_action('bpge_save_new_field', $this, $new);

                // Save Field
                $field_id = wp_insert_post($new);


                if(is_integer($field_id)){
                    // Save field options
                    update_post_meta($field_id, 'bpge_field_options', $options );

                    $field_desc = apply_filters('bpge_new_field_desc', $_POST['extra-field-desc']);
                    update_post_meta($field_id, 'bpge_field_desc', $field_desc);

                    $this->notices('added_field');
                }

                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );
            }

            // Save new page
            if ( isset($_POST['save_pages_add'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                global $current_blog;
                $thisblog = $current_blog->blog_id;
                $admin = get_user_by( 'email', get_blog_option($thisblog, 'admin_email'));

                // Save as a post_type
                $page = array(
                    'comment_status' => 'open',
                    'ping_status'    => 'open',
                    'post_author'    => $admin->ID,
                    'post_title'     => apply_filters('bpge_new_page_title', $_POST['extra-page-title']),
                    'post_name'      => apply_filters('bpge_new_page_slug', isset($_POST['extra-page-slug'])?$_POST['extra-page-slug']:''),
                    'post_content'   => apply_filters('bpge_new_page_content', $_POST['extra-page-content']),
                    'post_parent'    => apply_filters('bpge_new_page_parent', $bp->groups->current_group->extras['gpage_id']),
                    'post_status'    => apply_filters('bpge_new_page_status', $_POST['extra-page-status']),
                    'menu_order'     => count($this->get_all_gpages()) + 1,
                    'post_type'      => $this->page_slug
                );

                do_action('bpge_save_new_page_before', $this, $page);
                $page_id = wp_insert_post($page);
                do_action('bpge_save_new_page_after', $this, $page_id);

                $this->notices('added_page');

                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/pages/' );
            }

            // Edit existing field
            if ( isset($_POST['save_fields_edit'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                $new               = new Stdclass;
                $new->ID           = apply_filters('bpge_update_field_id',       $_POST['extra-field-id']);
                $new->post_title   = apply_filters('bpge_update_field_title',    $_POST['extra-field-title']);
                $new->post_excerpt = apply_filters('bpge_update_field_type',     $_POST['extra-field-type']);
                $new->pinged       = apply_filters('bpge_update_field_required', $_POST['extra-field-required']);
                $new->post_status  = apply_filters('bpge_update_field_display',  $_POST['extra-field-display']);
                $new->post_parent  = apply_filters('bpge_update_field_group',    $_POST['group-id']);
                $new->post_type    = apply_filters('bpge_update_field_type',     BPGE_GFIELDS);

                if(!empty($_POST['options'])){
                    $options = array();
                    foreach($_POST['options'] as $option){
                        $options[] = htmlspecialchars(strip_tags($option));
                    }
                }

                // Save into DB
                $field_id = wp_update_post($new);

                do_action('bpge_update_field', $this, $new);

                if(is_integer($field_id)){
                    $field_desc = apply_filters('bpge_update_field_desc', htmlspecialchars(strip_tags($_POST['extra-field-desc'])));
                    update_post_meta($field_id, 'bpge_field_desc', $field_desc);

                    $this->notices('edited_field');
                }

                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/' );

            }

            // Edit existing page
            if ( isset($_POST['save_pages_edit'])){
                /* Check the nonce first. */
                if ( !check_admin_referer( 'groups_edit_group_extras' ) )
                    return false;

                $page['ID']           = $_POST['extra-page-id'];
                $page['post_title']   = apply_filters('bpge_updated_page_title', $_POST['extra-page-title']);
                $page['post_name']    = apply_filters('bpge_updated_page_slug', isset($_POST['extra-page-slug'])?$_POST['extra-page-slug']:'');
                $page['post_content'] = apply_filters('bpge_updated_page_content', $_POST['extra-page-content']);
                $page['post_status']  = apply_filters('bpge_updated_page_status', $_POST['extra-page-status']);

                do_action('bpge_save_updated_page_before', $this, $page);
                $updated = wp_update_post($page);
                do_action('bpge_save_updated_page_after', $this, $updated);

                $this->notices('edited_page');

                bp_core_redirect( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/pages/' );
            }
        }
    }

    // Display Header and Extra-Nav
    function edit_screen_head($cur = 'general'){
        global $bp;
        $group_link = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug;

        switch($cur){
            case 'general':
                echo '<span class="extra-title">'.bpge_names('title_general').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button active">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;

            case 'fields':
                echo '<span class="extra-title">'.bpge_names('title_fields').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button active">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;

            case 'fields-manage':
                if ( isset($_GET['edit']) && !empty($_GET['edit']) ){
                    echo '<span class="extra-title">'.bpge_names('title_fields_edit').'</span>';
                    $active = '';
                }else{
                    echo '<span class="extra-title">'.bpge_names('title_fields_add').'</span>';
                    $active = 'active';
                }
                echo '<span class="extra-subnav">
                            <a href="'. $group_link . '/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link . '/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields-manage/" class="button ' . $active . '">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link . '/pages-manage/" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;

            case 'pages':
                echo '<span class="extra-title">'.bpge_names('title_pages').'</span>';
                echo '<span class="extra-subnav">
                            <a href="'. $group_link .'/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages/" class="button active">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link .'/fields-manage/" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/" class="button">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;

            case 'pages-manage':
                if ( isset($_GET['edit']) && !empty($_GET['edit']) ){
                    echo '<span class="extra-title">'.bpge_names('title_pages_edit').'</span>';
                    $active = '';
                }else{
                    echo '<span class="extra-title">'.bpge_names('title_pages_add').'</span>';
                    $active = 'active';
                }
                echo '<span class="extra-subnav">
                            <a href="'. $group_link . '/" class="button">'. __('General', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields/" class="button">'. __('All Fields', 'bpge') .'</a>
                            <a href="'. $group_link . '/pages/" class="button">'. __('All Pages', 'bpge') .'</a>
                            <a href="'. $group_link . '/fields-manage/" class="button">'. __('Add Field', 'bpge') .'</a>
                            <a href="'. $group_link .'/pages-manage/" class="button ' . $active . '">'. __('Add Page', 'bpge') .'</a>';
                            do_action('bpge_group_admin_head_nav', $cur, $group_link, $this);
                        echo '</span>';
                break;
        }

        echo '<div class="clear">&nbsp;</div>';

        do_action('bpge_extra_menus', $cur);
    }

    /************************************************************************/

    // Getting all extra items (fields or pages) for defined group
    function get_all_items($type, $id){
        // get all fields
        $items = array();

        if ( $type == 'fields' ){
            $items = groups_get_groupmeta($id, 'bpge_fields');
        }elseif ( $type == 'pages' ){
            $items = groups_get_groupmeta($id, 'bpge_pages');
        }

        if (empty($items)) {
            $items = false;
        }else{
            $items = json_decode($items);
        }

        return apply_filters('bpge_get_all_items', $items);
    }

    // Get item (field or page) by slug - reusable
    function get_item_by_slug($type, $slug){
        global $bp;
        // just in case...
        if (!is_string($type) || !is_string($slug))
            return false;

        $items = array();
        $searched = array();

        $type = apply_filters('bpge_items_by_slug_type', $type);
        $slug = apply_filters('bpge_items_by_slug_slug', $slug);

        if ( $type == 'field'){
            $items = $this->get_all_items('fields', $bp->groups->current_group->id);
        }elseif ( $type == 'page'){
            $items = $this->get_all_items('pages', $bp->groups->current_group->id);
        }

        foreach( $items as $item ){
            if ( $slug == $item->slug )
                $searched = $item;
        }

        return apply_filters('bpge_items_by_slug', $searched);
    }

    // Import set of fields
    function import_set_fields(){
        global $wpdb, $bp;

        $redirect = bp_get_group_permalink( $bp->groups->current_group ) . 'admin/'.$this->slug .'/fields/';

        if($_POST['group-id'] != $bp->groups->current_group->id){
            wp_redirect($redirect);
            die;
        }

        $group_id = $bp->groups->current_group->id;

        // get all fields from a set with appropriate options from a db
        $fields = $wpdb->get_results($wpdb->prepare(
                            "SELECT ID, post_title, post_content, post_excerpt
                                FROM {$wpdb->posts}
                                WHERE post_parent = %d
                                    AND post_type = %s
                                ORDER BY ID ASC",
                            intval($_POST['import_def_set_fields']),
                            BPGE_FIELDS
                        ));

        // save everything to a group
        foreach ($fields as $field) {
            $field->options = array();
            // get options if needed
            if(in_array($field->post_excerpt, array('select', 'radio', 'checkbox'))){
                $field->options = get_post_meta($field->ID, 'bpge_field_options', true);
            }

            // save the field
            $new = new Stdclass;
            $new->post_type    = BPGE_GFIELDS;
            $new->post_parent  = $group_id;
            $new->post_title   = $field->post_title;
            $new->post_excerpt = $field->post_excerpt;
            $new->post_status  = 'draft'; // not public

            $field_id = wp_insert_post($new);

            if (is_int($field_id)){
                $field_desc = strip_tags($field->post_content);
                update_post_meta( $field_id, 'bpge_field_desc', $field_desc );

                // ... and options
                if(!empty($field->options)){
                    update_post_meta( $field_id, 'bpge_field_options', $field->options );
                }
            }
        }

        wp_redirect($redirect);
        die;
    }

    // Notices about user actions
    function notices($type){
        switch($type){
            case 'settings_updated';
                bp_core_add_message(__('Group Extras settings were succefully updated.','bpge'));
                break;
            case 'added_field';
                bp_core_add_message(__('New field was successfully added.','bpge'));
                break;
            case 'edited_field';
                bp_core_add_message(__('The field was successfully updated.','bpge'));
                break;
            case 'added_page';
                bp_core_add_message(__('New page was successfully added.','bpge'));
                break;
            case 'edited_page';
                bp_core_add_message(__('The page was successfully updated.','bpge'));
                break;
            case 'no_fields':
                echo '<div class="" id="message"><p>' . __('Please create at least 1 extra field to show it in a list.', 'bpge') . '</p></div>';
                break;
            case 'no_pages':
                echo '<div class="" id="message"><p>' . __('Please create at least 1 extra page to show it in a list.', 'bpge') . '</p></div>';
                break;
        }
        do_action('bpge_notices', $type);
    }

    // create a storage for groups pages
    function get_gpage_by($what, $input = false){
        global $bp;

        switch($what){
            case 'group_id':
                global $current_blog;
                $admin    = get_user_by( 'email', get_blog_option($current_blog->blog_id, 'admin_email'));
                $old_data = groups_get_groupmeta($bp->groups->current_group->id, 'bpge');
                // create a gpage...
                $old_data['gpage_id'] = wp_insert_post(array(
                                            'comment_status' => 'closed',
                                            'ping_status'    => 'closed',
                                            'post_author'    => $admin->ID,
                                            'post_content'   => $bp->groups->current_group->description,
                                            'post_name'      => $bp->groups->current_group->slug,
                                            'post_status'    => 'publish',
                                            'post_title'     => $bp->groups->current_group->name,
                                            'post_type'      => $this->page_slug
                                        ));
                // ...and save it to reuse later
                groups_update_groupmeta($bp->groups->current_group->id, 'bpge', $old_data);
                return $old_data['gpage_id'];
                break;

            case 'id':
                return get_post($input);
                break;
        }

    }

    /************************************************************************/

    // Creation step - enter the data
    function create_screen() {
        do_action('bpge_create_screen', $this);
    }

    // Creation step - save the data
    function create_screen_save() {
        do_action('bpge_create_save', $this);
    }

    // Load if was not already loaded
    private static $instance = false;
    static function getInstance(){
        if(!self::$instance)
            self::$instance = new BPGE;

        return self::$instance;
    }
}

bp_register_group_extension('BPGE');
