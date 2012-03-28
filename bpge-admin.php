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

$bpge_admin = new BPGE_ADMIN();

class BPGE_ADMIN{

    function BPGE_ADMIN() {
        add_filter('screen_layout_columns', array( &$this, 'on_screen_layout_columns'), 10, 2 );
        add_action('admin_head', 'bpge_js_localize', 5);
        if (is_multisite()){
            add_action('network_admin_menu', array( &$this, 'on_admin_menu') );
        }else{
            add_action('admin_menu', array( &$this, 'on_admin_menu') );
        }
    }

    function on_screen_layout_columns( $columns, $screen ) {
        if ( $screen == $this->pagehook ) {
            if (is_multisite()){
                $columns[ $this->pagehook ] = 1;
            }else{
                $columns[ $this->pagehook ] = 2;
            }
            //$columns[ $this->pagehook ] = 1;
        }
        return $columns;
    }
    
    function on_admin_menu() {
        $this->pagehook = add_submenu_page('bp-general-settings', __('Groups Extras', 'bpge'), __('Groups Extras', 'bpge'), 'manage_options', 'bpge-admin', array( &$this, 'on_show_page') );
        add_action('load-'.$this->pagehook, array( &$this, 'on_load_page') );
    }
    
    //will be executed if wordpress core detects this page has to be rendered
    function on_load_page() {
        wp_enqueue_script('common');
        wp_enqueue_script('wp-lists');
        wp_enqueue_script('postbox');

        // sidebar
        //add_meta_box('bpge-admin-debug', __('Debug', 'bpge'), array(&$this, 'on_bpge_admin_debug'), $this->pagehook, $position, $priority );
        add_meta_box('bpge-admin-re', __('Rich Editor for Groups Pages', 'bpge'), array(&$this, 'on_bpge_admin_re'), $this->pagehook, 'side', 'low' );
        add_meta_box('bpge-admin-promo', __('Need Help / Custom Work?', 'bpge'), array(&$this, 'on_bpge_admin_promo'), $this->pagehook, 'side', 'low' );
        // main content - normal
        add_meta_box('bpge-admin-groups', __('Groups Management', 'bpge'), array( &$this, 'on_bpge_admin_groups'), $this->pagehook, 'normal', 'core');
        add_meta_box('bpge-admin-fields', __('Default Fields', 'bpge'), array( &$this, 'on_bpge_admin_fields'), $this->pagehook, 'normal', 'core');
    }

    function on_bpge_admin_re($bpge){
        echo '<p>';
            _e('Would you like to enable Rich Editor for easy use of html tags for groups pages?','bpge');
        echo '</p>';

        echo '<p>';
            echo '<input type="radio" name="bpge_re" '.($bpge['re'] == 1?'checked="checked"':'').' value="1">&nbsp'.__('Enable','bpge').'<br />';
            echo '<input type="radio" name="bpge_re" '.($bpge['re'] != 1?'checked="checked"':'').' value="0">&nbsp'.__('Disable','bpge');
        echo '</p>';
    }
    
    function on_bpge_admin_promo($bpge){
        echo '<p>If you:</p>
                <ul style="list-style:disc;margin-left:15px;">
                    <li>have a site/plugin idea and want to implement it</li>
                    <li>want to modify this plugin to your needs and ready to sponsor this</li>
                </ul>
                <p>feel free to contact slaFFik via <a href="skype:slaffik_ua?chat">skype:slaFFik_ua</a></p>';
    }
    
    function on_bpge_admin_fields($bpge){
        
        if(!empty($_POST) && (!empty($_POST['add_set_fields_name']) || !empty($_POST['edit_set_fields_name']) || !empty($_POST['extra-field-title']))){
            if(!empty($_POST['add_set_fields_name'])){
                $field_set_data['name'] = $_POST['add_set_fields_name'];
                if(!empty($_POST['add_set_field_description'])){
                    $field_set_data['desc'] = $_POST['add_set_field_description'];
                }
                $sets_fields = bp_get_option('bpge_def_fields');
                $set_slug = 'bpge-set-' . sanitize_title_with_dashes($_POST['add_set_fields_name']);
                $field_set_data['slug'] = $set_slug;
                $sets_fields[$set_slug]['name'] = $_POST['add_set_fields_name'];
                $sets_fields[$set_slug]['desc'] = $_POST['add_set_field_description'];
                update_option('bpge_def_fields', $sets_fields);
                update_option($set_slug, $field_set_data);
            }else if(!empty($_POST['edit_set_fields_name'])){
                if(!empty($_POST['slug_set_fields'])){
                    $sets_fields = bp_get_option('bpge_def_fields');
                    $set_fields = bp_get_option($_POST['slug_set_fields']);
                    $sets_fields[$_POST['slug_set_fields']]['name'] = $_POST['edit_set_fields_name'];
                    $sets_fields[$_POST['slug_set_fields']]['desc'] = $_POST['edit_set_field_description'];
                    $set_fields['name'] = $_POST['edit_set_fields_name'];
                    $set_fields['desc'] = $_POST['edit_set_field_description'];
                    update_option('bpge_def_fields', $sets_fields);
                    update_option($_POST['slug_set_fields'], $set_fields);
                }
            }else if(!empty($_POST['extra-field-title'])){
                if(!empty($_POST['slug_sf_for_field'])){
                    $set_fields = bp_get_option($_POST['slug_sf_for_field']);
                    $count = count($set_fields['fields']);
                    $set_fields['fields'][$count]['name'] = $_POST['extra-field-title'];
                    $set_fields['fields'][$count]['desc'] = $_POST['extra-field-desc'];
                    $set_fields['fields'][$count]['type'] = $_POST['extra-field-type'];
                    if(!empty($_POST['options'])){
                        $n = 0;
                        foreach($_POST['options'] as $option){
                            $set_fields['fields'][$count]['options'][$n]['name'] = $option;
                            $set_fields['fields'][$count]['options'][$n]['slug'] = sanitize_title_with_dashes($option);
                            $n++;
                        }
                    }
                    update_option($_POST['slug_sf_for_field'], $set_fields);
                }
            }
        }
        
        echo '<p>';
            _e('Please create/edit here fields you want to be available as standard blocks of data.<br />This will be helpful for group admins - no need for them to create lots of fields from scratch.','bpge');
        echo '</p>';
        
        $def_fields = array();
        $def_fields = bp_get_option('bpge_def_fields');
        echo '<ul class="sets">';
        if(!empty($def_fields)){
            foreach($def_fields as $key => $value){
                $data_set = bp_get_option($key);
                echo '<li id="' . $data_set['slug'] . '">';
                echo    '<span class="name">' . $data_set['name'] . '</span>';
                echo    '<span class="desc">' . $data_set['desc'] . '</span>';
                echo    '<span class="actions">
                             <a class="button display_fields" set_fields="' . $data_set['slug'] . '" href="#">'.__('Show Fields', 'bpge').' (' . count($data_set['fields']) . ')</a>
                             <a class="button field_edit" set_fields="' . $data_set['slug'] . '" href="#">'.__('Edit','bpde').'</a>
                             <a class="button field_delete" set_fields="' . $data_set['slug'] . '" href="#">'.__('Delete','bpde').'</a>
                         </span>';
                    echo '<ul class="fields" id="fields_' . $data_set['slug'] . '" class="fields">';
                    if(!empty($data_set['fields'])){
                        foreach($data_set['fields'] as $field){
                            echo '<li>'.$field['name'].' &rarr; '.$field['type'].' &rarr; '.$field['desc'].'</li>';
                        }
                    }else{
                        echo '<li><b>'.__('Fields not yet created','bpde').'</b></li>';
                    }
                    echo '<li><a class="button add_field" set_fields="' . $data_set['slug'] . '" href="#">'.__('Add field','bpge').'</a></li></ul>';
                echo '</li>';
            }
        }else{
            echo '<li>';
                echo '<span class="no_fields">'.__('Currently there are no predefined fields. Groups admins should create all fields by themselves.', 'bpge') . '</span>';
            echo '</li>';
        }
        echo '</ul>';
        
        echo '<div class="clear"></div>';
        
        echo '<div id="box_add_set_fields">
                    <h4>'.__('Add new Set of Fields','bpge').'</h4>
                    <div><label>'.__('Name','bpge').'</label><input type="text" name="add_set_fields_name" /></div>
                    <div><label>'.__('Description','bpge').'</label><textarea name="add_set_field_description" ></textarea></div>
                    <input id="savenewsf" type="submit" class="button-primary" name="savenewsetfields" value="'.__('Save New Set of Fields','bpge').'" />
              </div>';
        
        echo '<div id="box_edit_set_fields">
                    <h4>'.__('Edit Set of Fields','bpge').' &rarr; <span></span></h4>
                    <div><label>'.__('Name','bpge').'</label><input type="text" name="edit_set_fields_name" /></div>
                    <div><label>'.__('Description','bpge').'</label><textarea name="edit_set_field_description" ></textarea></div>
                    <input type="hidden" name="slug_set_fields" value="" />
                    <input id="editsf" type="submit" class="button-primary" name="editsetfields" value="'.__('Edit Set of Fields','bpge').'" />
              </div>';
        
        echo '<div id="box_add_field">';
            echo '<h4>'.__('Add field into','bpge').' &rarr; <span></span></h4>';
            echo '<div><label>' . __('Field Title', 'bpge') . '</label>';
            echo '<input type="text" value="" name="extra-field-title"></div>';
            echo '<div><label>' . __('Field Type', 'bpge') . '</label>';
            echo '<select name="extra-field-type" id="extra-field-type">';
                 echo '<option value="text">' . __('Text Box', 'bpge') . '</option>';
                 echo '<option value="textarea">' . __('Multi-line Text Box', 'bpge') . '</option>';
                 echo '<option value="checkbox">' . __('Checkboxes', 'bpge') . '</option>';
                 echo '<option value="radio">' . __('Radio Buttons', 'bpge') . '</option>';
                 //echo '<option value="datebox">' . __('Date Selector', 'bpge') . '</option>';
                 echo '<option value="select">' . __('Dropdown Select Box', 'bpge') . '</option>';
            echo '</select></div>';
                
            echo '<div id="extra-field-vars" style="display:none;">';
                 echo '<div class="content"></div>';
                 echo '<div class="links">
                               <a class="button" href="#" id="add_new">' . __('Add New', 'bpge') . '</a>
                       </div>';
            echo '</div>';
            echo '<div><label>' . __('Field Description', 'bpge') . '</label>';
                echo '<textarea name="extra-field-desc"></textarea></div>';
            echo '<input type="hidden" name="slug_sf_for_field" value="" />';
            echo '<input id="addnewfield" type="submit" class="button-primary" name="addnewfield" value="'.__('Add New Field','bpge').'" />';
        echo '</div>';
        
        echo '<a class="button add_set_fields" href="#">'.__('Create the Set of Fields','bpge').'</a>';
    }

    function on_bpge_admin_debug($bpge){
        print_var($bpge);
    }
    
    function on_bpge_admin_groups($bpge){
        global $bp;
        ?>
        <table id="bp-gtm-admin-table" class="widefat link-group">
            <thead>
                <tr class="header">
                    <td colspan="2"><p><?php _e('Which groups do you allow to create custom fields and pages?', 'bpge') ?></p></td>
                </tr>
            </thead>
            <tbody id="the-list">
                <tr>
                    <td class="checkbox"><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
                    <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
                </tr>
                <?php
                $arg['type'] = 'alphabetical';
                $arg['per_page'] = '1000';
                if ( bp_has_groups($arg) ){
                    while ( bp_groups() ) : bp_the_group();
                        $description = preg_replace( array('<<p>>', '<</p>>', '<<br />>', '<<br>>'), '', bp_get_group_description_excerpt() );
                        echo '<tr>
                                <td class="checkbox"><p><input name="bpge_groups['.bp_get_group_id().']" class="bpge_groups" type="checkbox" '.( ('all' == $bpge['groups'] || in_array(bp_get_group_id(), $bpge['groups']) ) ? 'checked="checked" ' : '').'value="'.bp_get_group_id().'" /></p></td>
                                <td><p><a href="'.bp_get_group_permalink().'admin/extras/" target="_blank">'. bp_get_group_name() .'</a> &rarr; '.$description.'</p></td>
                            </tr>';
                    endwhile;
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="header">
                    <td><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
                    <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
                </tr>
            </tfoot>
        </table>
    <?php
    }
    
    //executed to show the plugins complete admin page
    function on_show_page() {
        global $bp, $wpdb, $screen_layout_columns;
        
        //define some data that can be given to each metabox during rendering
        $bpge = bp_get_option('bpge');
        ?>
        
        <div id="bpge-admin-general" class="wrap">
            <?php screen_icon('options-general'); ?>
            <style>table.link-group li{margin:0 0 0 25px}</style>
            <h2><?php _e('BuddyPress Groups Extras','bpge') ?> <sup><?php echo 'v' . BPGE_VERSION; ?></sup> &rarr; <?php _e('Extend Your Groups', 'bpge') ?></h2>
        
            <?php 
            if ( isset($_POST['saveData']) ) {
                $bpge['groups'] = $_POST['bpge_groups'] ? $_POST['bpge_groups'] : array();
                $bpge['re'] = $_POST['bpge_re'];

                bp_update_option('bpge', $bpge);

                echo "<div id='message' class='updated fade'><p>" . __('All changes were saved. Go and check results!', 'bpge') . "</p></div>";
            }
            ?>

            <form action="" id="bpge-form" method="post">
                <?php 
                wp_nonce_field('bpge-admin-general');
                wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false );
                wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
                
                <div id="poststuff" class="metabox-holder<?php echo (2 == $screen_layout_columns) ? ' has-right-sidebar' : ''; ?>">
                    <?php if( !is_multisite() ) { ?>
                        <div id="side-info-column" class="inner-sidebar">
                            <p style="text-align:center">
                                <input type="submit" value="<?php _e('Save Changes', 'bpge') ?>" class="button-primary" name="saveData"/>   
                                <a class="button" href="" title="<?php _e('Refresh current page', 'bpge') ?>"><?php _e('Refresh', 'bpge') ?></a>
                            </p>
                            <?php do_meta_boxes($this->pagehook, 'side', $bpge); ?>
                        </div>
                    <?php } ?>
                    <div id="post-body" class="<?php !is_multisite()?' has-sidebar':''; ?>">
                        <div id="post-body-content" class="<?php !is_multisite()?' has-sidebar-content':''; ?>">
                            <?php
                            do_meta_boxes($this->pagehook, 'normal', $bpge);
                            if( is_multisite() ) {
                                do_meta_boxes($this->pagehook, 'side', $bpge);
                            }                            
                            ?>
                            <p>
                                <input type="submit" value="<?php _e('Save Changes', 'bpge') ?>" class="button-primary" name="saveData"/>   
                            </p>
                        </div>
                    </div>
                </div>  
            </form>
        </div>
        <script type="text/javascript">
            jQuery(document).ready( function() {
                jQuery('.if-js-closed').removeClass('if-js-closed').addClass('closed');
                postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
            });
        </script>
        
    <?php
    }
}
