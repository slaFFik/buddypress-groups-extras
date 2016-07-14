<ul id="fields-sortable">
	<?php
	foreach ( (array) $fields as $field ) { ?>
		<li id="position_<?php echo (int) $field->ID; ?>" class="default">
			<strong title="<?php echo esc_attr( strip_tags( $field->post_content ) ); ?>"><?php echo stripslashes( $field->post_title ); ?></strong>
			&rarr; <?php echo stripslashes( $field->post_excerpt ); ?>
			&rarr; <?php ( ( $field->post_status == 'publish' ) ? _e( 'displayed', 'buddypress-groups-extras' ) : _e( '<u>not</u> displayed', 'buddypress-groups-extras' ) ); ?>
			&rarr; <?php ( ( $field->pinged == 'req' ) ? _e( 'required', 'buddypress-groups-extras' ) : _e( '<u>not</u> required', 'buddypress-groups-extras' ) ); ?>

			<span class="items-link">
                <a href="<?php echo bp_get_group_permalink( $bp->groups->current_group ); ?>admin/<?php echo $slug; ?>/fields-manage/?edit=<?php echo $field->ID; ?>"
                   class="button"
                   title="<?php _e( 'Change its title, description etc', 'buddypress-groups-extras' ); ?>"><?php _e( 'Edit field', 'buddypress-groups-extras' ); ?></a>&nbsp;

				<?php do_action( 'bpge_template_extras_fields_list_actions', $field ); ?>

				<a href="#" class="button delete_field"
				   title="<?php _e( 'Delete this item and all its content', 'buddypress-groups-extras' ); ?>"><?php _e( 'Delete', 'buddypress-groups-extras' ); ?></a>
            </span>
		</li>
	<?php } ?>
</ul>
