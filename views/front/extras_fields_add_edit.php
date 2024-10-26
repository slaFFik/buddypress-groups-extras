<div class="box_field">

	<!-- Title -->
	<label for="extra-field-title"><?php esc_html_e( 'Field Title', 'buddypress-groups-extras' ); ?></label>
	<input type="text" value="<?php esc_attr_e( $field->post_title ); ?>" name="extra-field-title" id="extra-field-title">
	<script>jQuery( '#extra-field-title' ).focus();</script>

	<!-- Type -->
	<?php if ( empty( $field->ID ) ) { ?>
		<label><?php esc_html_e( 'Field Type', 'buddypress-groups-extras' ); ?></label>
		<select name="extra-field-type" id="extra-field-type">
			<option value="text" <?php selected( $field->post_excerpt, 'text' ); ?>><?php esc_html_e( 'Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="textarea" <?php selected( $field->post_excerpt, 'textarea' ); ?>><?php esc_html_e( 'Multi-line Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="checkbox" <?php selected( $field->post_excerpt, 'checkbox' ); ?>><?php esc_html_e( 'Checkboxes', 'buddypress-groups-extras' ); ?></option>
			<option value="radio" <?php selected( $field->post_excerpt, 'radio' ); ?>><?php esc_html_e( 'Radio Buttons', 'buddypress-groups-extras' ); ?></option>
			<!-- <option value="datebox" <?php selected( $field->post_excerpt, 'datebox' ); ?>><?php esc_html_e( 'Date Selector', 'buddypress-groups-extras' ); ?></option> -->
			<option value="select" <?php selected( $field->post_excerpt, 'select' ); ?>><?php esc_html_e( 'Drop Down Select Box', 'buddypress-groups-extras' ); ?></option>
		</select>

		<div id="extra-field-vars">
			<div class="content"></div>
			<div class="links">
				<a class="button" href="#" id="add_new"><?php esc_html_e( 'Add New', 'buddypress-groups-extras' ); ?></a>
			</div>
		</div>
	<?php } else { ?>
		<input type="hidden" name="extra-field-type" value="<?php esc_attr_e( $field->post_excerpt ); ?>" />
	<?php } ?>

	<!-- Description -->
	<label><?php esc_html_e( 'Field Description', 'buddypress-groups-extras' ); ?></label>
	<textarea name="extra-field-desc"><?php echo esc_textarea( $field->desc ); ?></textarea>

	<!-- Required or not? -->
	<?php if ( empty( $field->pinged ) ) {
		$field->pinged = 'not_req';
	} ?>
	<label for="extra-field-required"><?php esc_html_e( 'Is this field required (will be marked as required on group Edit Details page)?', 'buddypress-groups-extras' ); ?></label>
	<input type="radio" value="req" <?php checked( $field->pinged, 'req' ); ?>name="extra-field-required"> <?php esc_html_e( 'Required', 'buddypress-groups-extras' ); ?><br />
	<input type="radio" value="not_req" <?php checked( $field->pinged, 'not_req' ); ?>name="extra-field-required"> <?php esc_html_e( 'Not Required', 'buddypress-groups-extras' ); ?>
	<br />

	<!-- Display or not? -->
	<?php if ( empty( $field->post_status ) ) {
		$field->post_status = 'draft';
	} ?>
	<label>
		<?php
		echo sprintf( /* translators: %s - Title of the page. */
			esc_html__( 'Should this field be displayed publicly on "%s" page?', 'buddypress-groups-extras' ),
			$nav_item_name
		);
		?>
	</label>
	<input type="radio" name="extra-field-display" value="publish" <?php checked( $field->post_status, 'publish' ); ?>>&nbsp;<?php esc_html_e( 'Display it', 'buddypress-groups-extras' ); ?>
	<br />
	<input type="radio" name="extra-field-display" value="draft" <?php checked( $field->post_status, 'draft' ); ?>>&nbsp;<?php esc_html_e( 'Do NOT display it', 'buddypress-groups-extras' ); ?>

	<?php do_action( 'bpge_template_display_fields_manage', $field ); ?>

	<?php if ( empty( $field->ID ) ) { ?>
		<p><input type="submit" name="save_fields_add" id="save" value="<?php esc_attr_e( 'Create New &rarr;', 'buddypress-groups-extras' ); ?>"></p>
	<?php } else { ?>
		<input type="hidden" name="extra-field-id" value="<?php echo (int) $field->ID; ?>">
		<p><input type="submit" name="save_fields_edit" id="save" value="<?php esc_attr_e( 'Save Changes &rarr;', 'buddypress-groups-extras' ); ?>"></p>
	<?php } ?>
</div>

<?php wp_nonce_field( 'groups_edit_group_extras' ); ?>
