<div id="box_edit_set_fields">
	<h4><?php esc_html_e( 'Edit Set of Fields', 'buddypress-groups-extras' ); ?> &rarr; <span></span></h4>
	<div>
		<label><?php esc_html_e( 'Name', 'buddypress-groups-extras' ); ?></label>
		<input type="text" name="edit_set_fields_name" />
	</div>
	<div>
		<label><?php esc_html_e( 'Description', 'buddypress-groups-extras' ); ?></label>
		<textarea name="edit_set_field_description"></textarea>
	</div>
	<input type="hidden" name="edit_set_fields_id" value="" />
	<input id="editsf" type="submit" class="button-primary" name="editsetfields" value="<?php esc_attr_e( 'Edit Set of Fields', 'buddypress-groups-extras' ); ?>" />
</div>
