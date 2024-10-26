<ul id="fields-sortable">
	<?php
	foreach ( (array) $fields as $field ) { ?>
		<li id="position_<?php echo (int) $field->ID; ?>" class="default">
			<strong title="<?php echo esc_attr( strip_tags( $field->post_content ) ); ?>"><?php echo esc_html( $field->post_title ); ?></strong>
			&rarr; <?php echo stripslashes( $field->post_excerpt ); ?>
			&rarr; <?php ( ( $field->post_status === 'publish' ) ? esc_html_e( 'displayed', 'buddypress-groups-extras' ) : esc_html_e( 'not displayed', 'buddypress-groups-extras' ) ); ?>
			&rarr; <?php ( ( $field->pinged === 'req' ) ? esc_html_e( 'required', 'buddypress-groups-extras' ) : esc_html_e( 'not required', 'buddypress-groups-extras' ) ); ?>

			<span class="items-link">
                <a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $slug . '/fields-manage/?edit=' . $field->ID ); ?>"
	                class="button" title="<?php esc_attr_e( 'Change its title, description etc', 'buddypress-groups-extras' ); ?>">
	                <?php esc_html_e( 'Edit field', 'buddypress-groups-extras' ); ?>
                </a>&nbsp;

				<?php do_action( 'bpge_template_extras_fields_list_actions', $field ); ?>

				<a href="#" class="button delete_field" title="<?php esc_attr_e( 'Delete this item and all its content', 'buddypress-groups-extras' ); ?>">
					<?php esc_html_e( 'Delete', 'buddypress-groups-extras' ); ?>
				</a>
            </span>
		</li>
	<?php } ?>
</ul>
