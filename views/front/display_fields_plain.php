<?php do_action( 'bpge_template_display_fields_plain_before', $fields ); ?>

<div class="extra-data bpge_fields_data">
	<?php
	foreach ( $fields as $field ) :

		$field->desc    = get_post_meta( $field->ID, 'bpge_field_desc', true );
		$field->options = json_decode( $field->post_content );

		echo '<h4 title="' . ( ! empty( $field->desc ) ? esc_attr( $field->desc ) : '' ) . '">' . esc_html( $field->post_title ) . '</h4>';

		if ( is_array( $field->options ) ) {
			$data = implode( ', ', $field->options );
		} else {
			$data = $field->post_content;
		}

		echo '<p>' . bpge_filter_link_group_data( $data ) . '</p>';

		do_action( 'bpge_template_display_fields_plain_item', $field, $data );

	endforeach;
	?>
</div>

<?php do_action( 'bpge_template_display_fields_plain_after', $fields ); ?>
