<?php do_action( 'bpge_template_display_fields_table_before', $fields ); ?>

<table class="profile-fields zebra bpge_fields_data">
	<?php
	foreach ( $fields as $field ) {
		$field->desc    = get_post_meta( $field->ID, 'bpge_field_desc', true );
		$field->options = json_decode( $field->post_content );

		echo '<tr>';

		echo '<td class="label" title="' . ( ! empty( $field->desc ) ? esc_attr( $field->desc ) : '' ) . '">' . esc_html( $field->post_title ) . '</td>';

		if ( is_array( $field->options ) ) {
			$data = implode( ', ', $field->options );
		} else {
			$data = $field->post_content;
		}
		echo '<td class="data">' . bpge_filter_link_group_data( $data ) . '</td>';

		echo '</tr>';
	} ?>
</table>

<?php do_action( 'bpge_template_display_fields_table_after', $fields ); ?>
