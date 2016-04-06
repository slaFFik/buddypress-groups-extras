<div id="box_add_field">
	<h4><?php _e( 'Add field into', BPGE_I18N ); ?> &rarr; <span></span></h4>

	<div>
		<label><?php _e( 'Field Title', BPGE_I18N ); ?></label>
		<input type="text" value="" name="extra-field-title">
	</div>

	<div>
		<label><?php _e( 'Field Type', BPGE_I18N ); ?></label>
		<select name="extra-field-type" id="extra-field-type">
			<option value="text"><?php _e( 'Text Box', BPGE_I18N ); ?></option>
			<option value="textarea"><?php _e( 'Multi-line Text Box', BPGE_I18N ); ?></option>
			<option value="checkbox"><?php _e( 'Checkboxes', BPGE_I18N ); ?></option>
			<option value="radio"><?php _e( 'Radio Buttons', BPGE_I18N ); ?></option>
			<!--option value="datebox"><?php _e( 'Date Selector', BPGE_I18N ); ?></option-->
			<option value="select"><?php _e( 'Dropdown Select Box', BPGE_I18N ); ?></option>
		</select>
	</div>

	<div>
		<label><?php _e( 'Display this field?', BPGE_I18N ); ?></label>
		<input type="radio" name="extra-field-display" style="width:auto" value="yes"/>&nbsp;<?php _e( 'Yes', BPGE_I18N ); ?>&nbsp;<span
			class="description"><?php _e( 'or', BPGE_I18N ); ?></span>&nbsp;
		<input type="radio" name="extra-field-display" style="width:auto" value="no"/>&nbsp;<?php _e( 'No', BPGE_I18N ); ?>
	</div>

	<div id="extra-field-vars" style="display:none;">
		<div class="content"></div>
		<div class="links">
			<a class="button" href="#" id="add_new"><?php _e( 'Add New', BPGE_I18N ); ?></a>
		</div>
	</div>

	<div>
		<label><?php _e( 'Field Description', BPGE_I18N ); ?></label>
		<textarea name="extra-field-desc"></textarea>
	</div>

	<input type="hidden" name="sf_id_for_field" value=""/>
	<input id="addnewfield" type="submit" class="button-primary" name="addnewfield" value="<?php _e( 'Add New Field', BPGE_I18N ); ?>"/>
</div>

<a class="button add_set_fields" href="#"><?php _e( 'Create the Set of Fields', BPGE_I18N ); ?></a>