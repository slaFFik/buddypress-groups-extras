<ul id="fields-sortable">
	<?php
	foreach ( (array) $fields as $field ) { ?>
		<li id="position_<?php echo $field->ID; ?>" class="default">
			<strong title="<?php echo htmlspecialchars( strip_tags( $field->post_content ) ); ?>"><?php echo stripslashes( $field->post_title ); ?></strong>
			&rarr; <?php echo stripslashes( $field->post_excerpt ); ?>
			&rarr; <?php ( ( $field->post_status == 'publish' ) ? _e( 'displayed', BPGE_I18N ) : _e( '<u>not</u> displayed', BPGE_I18N ) ); ?>
			&rarr; <?php ( ( $field->pinged == 'req' ) ? _e( 'required', BPGE_I18N ) : _e( '<u>not</u> required', BPGE_I18N ) ); ?>
			<span class="items-link">
                <a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ); ?>admin/<?php echo $slug; ?>/fields-manage/?edit=<?php echo $field->ID; ?>"
                   class="button" title="<?php _e( 'Change its title, description etc', BPGE_I18N ); ?>"><?php _e( 'Edit field', BPGE_I18N ); ?></a>&nbsp;
                <a href="#" class="button delete_field"
                   title="<?php _e( 'Delete this item and all its content', BPGE_I18N ); ?>"><?php _e( 'Delete', BPGE_I18N ); ?></a>
            </span>
		</li>
	<?php } ?>
</ul>