<div id="box_add_set_fields">
	<h4><?php esc_html_e( 'Add new Set of Fields', 'buddypress-groups-extras' ); ?></h4>
	<div>
		<label><?php esc_html_e( 'Name', 'buddypress-groups-extras' ); ?></label>
		<input type="text" name="add_set_fields_name" />
	</div>
	<div>
		<label><?php esc_html_e( 'Description', 'buddypress-groups-extras' ); ?></label>
		<textarea name="add_set_field_description"></textarea>
	</div>
	<input id="savenewsf" type="submit" class="button-primary" name="savenewsetfields"
		value="<?php esc_attr_e( 'Save New Set of Fields', 'buddypress-groups-extras' ); ?>" />
</div>
