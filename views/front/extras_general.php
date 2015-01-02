<p>
    <label for="group_extras_display_name"><?php _e('Please specify the page name, where all fields will be displayed','bpge'); ?></label>
    <input type="text" value="<?php echo $nav_item_name; ?>" name="group-extras-display-name">
</p>

<p>
    <label for="group_extras_display"><?php echo sprintf(__('Do you want to make <strong>"%s"</strong> page public? Everyone will see this page.','bpge'), $nav_item_name); ?></label>
    <input type="radio" value="public" <?php echo checked($bp->groups->current_group->extras['display_page'], 'public', false); ?> name="group-extras-display"> <?php _e('Show it', 'bpge'); ?><br />
    <input type="radio" value="private" <?php echo checked($bp->groups->current_group->extras['display_page'], 'private', false); ?> name="group-extras-display"> <?php _e('Hide it', 'bpge'); ?>
</p>

<p>
    <label for="group_extras_display_layout"><?php echo sprintf(__('Please choose the layout for <strong>"%s"</strong> page','bpge'), $nav_item_name); ?></label>
    <input type="radio" value="plain" <?php echo checked($bp->groups->current_group->extras['display_page_layout'], 'plain', false); ?> name="group-extras-display-layout"> <?php _e('Plain (field title and its data below)', 'bpge'); ?><br />
    <input type="radio" value="profile" <?php echo checked($bp->groups->current_group->extras['display_page_layout'], 'profile', false); ?> name="group-extras-display-layout"> <?php _e('Profile style (in a table)', 'bpge'); ?>
</p>

<hr />

<p>
    <label for="group_extras_display_name"><?php _e('Please specify the page name, where all custom pages will be displayed','bpge'); ?></label>
    <input type="text" value="<?php echo $gpages_item_name; ?>" name="group-gpages-display-name">
</p>

<p>
    <label for="group_extras_display"><?php echo sprintf(__('Do you want to make <strong>"%s"</strong> page public (extra group pages will be displayed there)?','bpge'), $gpages_item_name); ?></label>
    <input type="radio" value="public" <?php echo checked($bp->groups->current_group->extras['display_gpages'], 'public', false); ?> name="group-gpages-display"> <?php _e('Show it', 'bpge'); ?><br />
    <input type="radio" value="private" <?php echo checked($bp->groups->current_group->extras['display_gpages'], 'private', false); ?> name="group-gpages-display"> <?php _e('Hide it', 'bpge'); ?>
</p>

<hr />

<label><?php _e('You can reorder here all navigation links in this group. The first item will become a landing page for this group. Save changes after reordering.<br />Please do NOT make Admin/Manage pages on first place - that will cause display problems.', 'bpge'); ?></label>
<ul id="nav-sortable">
    <?php
    foreach($group_nav as $nav){
        if(isset($nav['slug']) && $nav['slug'] == 'home'){
            $home_name = $nav['name'];
        }
        if(empty($nav['position'])){
            $nav['position'] = 99;
        }
        if(isset($nav['name'])){
            echo '<li id="position_'.$nav['position'].'" class="default">
                    <strong>' . stripslashes($nav['name']) .'</strong>
                </li>';
        }
    } ?>
    <input type="hidden" name="bpge_group_nav_position" value="" />
</ul>

<p>
    <label for="group_extras_home_name"><?php _e('Rename the Home group page - Activity (for example) is far better.','bpge'); ?></label>
    <input type="text" value="<?php echo $home_name; ?>" name="group-extras-home-name">
</p>

<div class="clear">&nbsp;</div>

<p>
    <input type="submit" name="save_general" id="save" value="<?php _e('Save Changes','bpge'); ?>">
</p>