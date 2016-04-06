<div id="box_edit_set_fields">
	<h4><?php _e( 'Edit Set of Fields', BPGE_I18N ); ?> &rarr; <span></span></h4>
	<div>
		<label><?php _e( 'Name', BPGE_I18N ); ?></label>
		<input type="text" name="edit_set_fields_name"/>
	</div>
	<div>
		<label><?php _e( 'Description', BPGE_I18N ); ?></label>
		<textarea name="edit_set_field_description"></textarea>
	</div>
	<input type="hidden" name="edit_set_fields_id" value=""/>
	<input id="editsf" type="submit" class="button-primary" name="editsetfields" value="<?php _e( 'Edit Set of Fields', BPGE_I18N ); ?>"/>
</div>