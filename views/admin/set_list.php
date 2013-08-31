<li id="set-<?php echo $set->ID ?>">
    <span class="name"><?php echo stripslashes($set->post_title) ?></span>
    <span class="desc"><?php echo stripslashes($set->post_content) ?></span>
    <span class="actions">
        <?php $applied = (isset($set->options['applied']) && $set->options['applied'] == 'true') ? 'applied' : false; ?>
        <a class="button display_fields" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Show Fields', 'bpge'); ?> (<?php echo count($fields);?>)</a>
        <a class="button set_apply <?php echo $applied; ?>" <?php echo ($applied?'disabled':'');?> data-set_id="<?php echo $set->ID;?>" href="#" title="<?php echo (!$applied ? __('Apply to all groups this set of fields', 'bpge') : __('Already applied', 'bpge')); ?>">
            <?php echo (!$applied ? __('Apply', 'bpge') : __('Applied', 'bpge')); ?>
        </a>
        <a class="button field_edit" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Edit','bpge'); ?></a>
        <a class="button field_delete" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Delete','bpge'); ?></a>
    </span>

    <ul class="fields" id="fields_<?php echo $set->ID ?>" class="fields">
        <?php
        if(!empty($fields)){
            foreach($fields as $field){
                echo '<li>'.$field->post_title.' &rarr; <em class="description">'.$field->post_excerpt.'</em> &rarr; '.$field->post_content.'</li>';
            }
        }else{
            echo '<li><strong>'.__('Fields not yet created','bpde').'</strong></li>';
        } ?>
        <li><a class="button add_field" data-set_id="<?php echo $set->ID;?>" href="#"><?php _e('Add field','bpge');?></a></li>
    </ul>

</li>