<li id="set-<?php echo (int) $set->ID ?>">

	<span class="name"><?php echo esc_html( $set->post_title ) ?></span>
	<span class="desc"><?php echo wp_kses_post( $set->post_content ) ?></span>

	<span class="actions">
        <?php $applied = ( isset( $set->options['applied'] ) && $set->options['applied'] === 'true' ) ? 'applied' : false; ?>
	    <a class="button display_fields" data-set_id="<?php echo (int) $set->ID; ?>" href="#"><?php esc_html_e( 'Show Fields', 'buddypress-groups-extras' ); ?>
		    (<?php echo count( $fields ); ?>)</a>
        <a class="button set_apply <?php echo $applied; ?>" <?php echo( $applied ? 'disabled' : '' ); ?> data-set_id="<?php echo (int) $set->ID; ?>" href="#"
           title="<?php echo( ! $applied ? esc_html__( 'Apply to all groups this set of fields', 'buddypress-groups-extras' ) : esc_html__( 'Already applied', 'buddypress-groups-extras' ) ); ?>">
            <?php echo( ! $applied ? esc_html__( 'Apply', 'buddypress-groups-extras' ) : esc_html__( 'Applied', 'buddypress-groups-extras' ) ); ?>
        </a>
        <a class="button field_edit" data-set_id="<?php echo (int) $set->ID; ?>" href="#"><?php esc_html_e( 'Edit', 'buddypress-groups-extras' ); ?></a>
        <a class="button field_delete" data-set_id="<?php echo (int) $set->ID; ?>" href="#"><?php esc_html_e( 'Delete', 'buddypress-groups-extras' ); ?></a>
    </span>

	<ul class="fields" id="fields_<?php echo (int) $set->ID ?>" class="fields">
		<?php
		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				echo '<li>' . esc_html( $field->post_title ) . ' &rarr; <em class="description">' . esc_html( $field->post_excerpt ) . '</em> &rarr; ' . wp_kses_post( $field->post_content ) . '</li>';
			}
		} else {
			echo '<li><strong>' . esc_html__( 'Fields not yet created', 'bpde' ) . '</strong></li>';
		} ?>
		<li>
			<a class="button add_field" data-set_id="<?php echo (int) $set->ID; ?>" href="#">
				<?php esc_html_e( 'Add field', 'buddypress-groups-extras' ); ?>
			</a>
		</li>
	</ul>

</li>
