<div id="box_add_set_fields">
	<h4>
		<?php esc_html_e( 'New Set of Fields', 'buddypress-groups-extras' ); ?>
	</h4>

	<div>
		<label for="add_set_fields_name">
			<?php esc_html_e( 'Name', 'buddypress-groups-extras' ); ?>
		</label>
		<input type="text" id="add_set_fields_name" name="add_set_fields_name" />
	</div>

	<div>
		<label for="add_set_field_description">
			<?php esc_html_e( 'Description', 'buddypress-groups-extras' ); ?>
		</label>
		<textarea id="add_set_field_description" name="add_set_field_description"></textarea>
	</div>

	<input id="savenewsf" type="submit" class="button-primary" name="savenewsetfields" value="<?php esc_attr_e( 'Create', 'buddypress-groups-extras' ); ?>" />

	<div class="clear"></div>
</div>
