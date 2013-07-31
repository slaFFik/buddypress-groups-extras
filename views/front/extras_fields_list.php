<ul id="fields-sortable">
    <?php
    foreach((array)$fields as $field){ ?>
        <li id="position_<?php echo $field->ID; ?>" class="default">
            <strong title="<?php echo  htmlspecialchars(strip_tags($field->post_content));?>"><?php echo stripslashes($field->post_title);?></strong>
            &rarr; <?php echo stripslashes($field->post_excerpt);?>
            &rarr; <?php (($field->post_status == 'publish')?_e('displayed','bpge'):_e('<u>not</u> displayed','bpge'));?>
            &rarr; <?php (($field->pinged == 'req')?_e('required','bpge'):_e('<u>not</u> required','bpge'));?>
            <span class="items-link">
                <a href="<?php echo bp_get_group_permalink( $bp->groups->current_group );?>admin/<?php echo $slug; ?>/fields-manage/?edit=<?php echo $field->ID;?>" class="button" title="<?php _e('Change its title, description etc','bpge');?>"><?php _e('Edit field', 'bpge');?></a>&nbsp;
                <a href="#" class="button delete_field" title="<?php _e('Delete this item and all its content', 'bpge');?>"><?php _e('Delete', 'bpge');?></a>
            </span>
        </li>
    <?php } ?>
</ul>