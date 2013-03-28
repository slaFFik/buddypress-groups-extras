<li id="set-<?php echo $set->ID ?>">
    <span class="name"><?php echo $set->post_title ?></span>
    <span class="desc"><?php echo stripslashes($set->post_content) ?></span>
    <span class="actions">
        <a class="button display_fields" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Show Fields', 'bpge'); ?> (<?php echo count($fields);?>)</a>
        <a class="button field_edit" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Edit','bpge'); ?></a>
        <a class="button field_delete" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Delete','bpge'); ?></a>
    </span>

    <ul class="fields" id="fields_<?php echo $set->ID ?>" class="fields">
        <?php
        if(!empty($fields)){
            foreach($fields as $field){
                echo '<li>'.$field->post_title.' &rarr; '.$field->post_excerpt.' &rarr; '.$field->post_content.'</li>';
            }
        }else{
            echo '<li><strong>'.__('Fields not yet created','bpde').'</strong></li>';
        } ?>
        <li><a class="button add_field" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Add field','bpge');?></a></li>
    </ul>
</li>