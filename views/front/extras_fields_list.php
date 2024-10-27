<?php
/* @var $fields WP_Post[] */
/* @var $bp BuddyPress */
/* @var $slug string */
?>
<ul id="fields-sortable">
	<?php
	foreach ( $fields as $field ) { ?>
		<li id="position_<?php echo (int) $field->ID; ?>" class="default">
			<strong title="<?php echo esc_attr( wp_strip_all_tags( $field->post_content ) ); ?>">
				<?php echo esc_html( $field->post_title ); ?>
			</strong><br/>
			<span class="smaller" style="font-size: 80%">
				<?php esc_html_e( 'Type:', 'buddypress-groups-extras' ); ?> <code><?php echo esc_html( $field->post_excerpt ); ?></code>
				<br/>
				<?php esc_html_e( 'Visibility:', 'buddypress-groups-extras' ); ?> <code><?php echo esc_html( ( $field->post_status === 'publish' ) ? __( 'public', 'buddypress-groups-extras' ) : __( 'hidden', 'buddypress-groups-extras' ) ); ?></code>,
				<code><?php echo esc_html( ( $field->pinged === 'req' ) ? __( 'required', 'buddypress-groups-extras' ) : __( 'optional', 'buddypress-groups-extras' ) ); ?></code>
			</span>

			<span class="items-link">
                <a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $slug . '/fields-manage/?edit=' . $field->ID ); ?>" class="button">
	                <?php esc_html_e( 'Edit field', 'buddypress-groups-extras' ); ?>
                </a>

				<?php do_action( 'bpge_template_extras_fields_list_actions', $field ); ?>

				<a href="#" class="button delete_field" title="<?php esc_attr_e( 'Delete this field and its content', 'buddypress-groups-extras' ); ?>">
					<?php esc_html_e( 'Delete', 'buddypress-groups-extras' ); ?>
				</a>
            </span>
		</li>
	<?php } ?>
</ul>
