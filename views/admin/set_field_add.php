<div id="box_add_field">
	<h4><?php _e( 'Add field into', 'buddypress-groups-extras' ); ?> &rarr; <span></span></h4>

	<div>
		<label><?php _e( 'Field Title', 'buddypress-groups-extras' ); ?></label>
		<input type="text" value="" name="extra-field-title">
	</div>

	<div>
		<label><?php _e( 'Field Type', 'buddypress-groups-extras' ); ?></label>
		<select name="extra-field-type" id="extra-field-type">
			<option value="text"><?php _e( 'Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="textarea"><?php _e( 'Multi-line Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="checkbox"><?php _e( 'Checkboxes', 'buddypress-groups-extras' ); ?></option>
			<option value="radio"><?php _e( 'Radio Buttons', 'buddypress-groups-extras' ); ?></option>
			<!--option value="datebox"><?php _e( 'Date Selector', 'buddypress-groups-extras' ); ?></option-->
			<option value="select"><?php _e( 'Dropdown Select Box', 'buddypress-groups-extras' ); ?></option>
		</select>
	</div>

	<div>
		<label><?php _e( 'Display this field?', 'buddypress-groups-extras' ); ?></label>
		<input type="radio" name="extra-field-display" style="width:auto" value="yes"/>&nbsp;<?php _e( 'Yes', 'buddypress-groups-extras' ); ?>&nbsp;<span
			class="description"><?php _e( 'or', 'buddypress-groups-extras' ); ?></span>&nbsp;
		<input type="radio" name="extra-field-display" style="width:auto" value="no"/>&nbsp;<?php _e( 'No', 'buddypress-groups-extras' ); ?>
	</div>

	<div id="extra-field-vars" style="display:none;">
		<div class="content"></div>
		<div class="links">
			<a class="button" href="#" id="add_new"><?php _e( 'Add New', 'buddypress-groups-extras' ); ?></a>
		</div>
	</div>

	<div>
		<label><?php _e( 'Field Description', 'buddypress-groups-extras' ); ?></label>
		<textarea name="extra-field-desc"></textarea>
	</div>

	<input type="hidden" name="sf_id_for_field" value=""/>
	<input id="addnewfield" type="submit" class="button-primary" name="addnewfield" value="<?php _e( 'Add New Field', 'buddypress-groups-extras' ); ?>"/>
</div>

<a class="button add_set_fields" href="#"><?php _e( 'Create the Set of Fields', 'buddypress-groups-extras' ); ?></a>
