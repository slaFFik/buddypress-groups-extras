<div id="box_edit_set_fields">
	<h4>
		<?php esc_html_e( 'New Set of Fields', 'buddypress-groups-extras' ); ?>
	</h4>

	<div>
		<label for="edit_set_fields_name">
			<?php esc_html_e( 'Name', 'buddypress-groups-extras' ); ?>
		</label>
		<input type="text" id="edit_set_fields_name" name="edit_set_fields_name" />
	</div>

	<div>
		<label for="edit_set_field_description">
			<?php esc_html_e( 'Description', 'buddypress-groups-extras' ); ?>
		</label>
		<textarea id="edit_set_field_description" name="edit_set_field_description"></textarea>
	</div>

	<input type="hidden" name="edit_set_fields_id" value="" />

	<input id="editsf" type="submit" class="button-primary" name="editsetfields" value="<?php esc_attr_e( 'Save Changes', 'buddypress-groups-extras' ); ?>" />

	<div class="clear"></div>
</div>
