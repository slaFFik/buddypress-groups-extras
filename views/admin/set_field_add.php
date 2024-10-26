<div id="box_add_field">
	<h4>
		<?php esc_html_e( 'New field in set:', 'buddypress-groups-extras' ); ?> <span></span>
	</h4>

	<div>
		<label for="extra-field-title">
			<?php esc_html_e( 'Field Title', 'buddypress-groups-extras' ); ?>
		</label>
		<input type="text" value="" id="extra-field-title" name="extra-field-title">
	</div>

	<div>
		<label for="extra-field-type">
			<?php esc_html_e( 'Field Type', 'buddypress-groups-extras' ); ?>
		</label>
		<select name="extra-field-type" id="extra-field-type">
			<option value="text"><?php esc_html_e( 'Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="textarea"><?php esc_html_e( 'Multi-line Text Box', 'buddypress-groups-extras' ); ?></option>
			<option value="checkbox"><?php esc_html_e( 'Checkboxes', 'buddypress-groups-extras' ); ?></option>
			<option value="radio"><?php esc_html_e( 'Radio Buttons', 'buddypress-groups-extras' ); ?></option>
			<!--option value="datebox"><?php esc_html_e( 'Date Selector', 'buddypress-groups-extras' ); ?></option-->
			<option value="select"><?php esc_html_e( 'Dropdown Select Box', 'buddypress-groups-extras' ); ?></option>
		</select>
	</div>

	<div>
		<label>
			<?php esc_html_e( 'Display this field?', 'buddypress-groups-extras' ); ?>
		</label>

		<ul>
			<li>
				<input type="radio" name="extra-field-display" style="width:auto" value="yes" checked />
				<?php esc_html_e( 'Yes', 'buddypress-groups-extras' ); ?>&nbsp;
			</li>

			<li>
				<input type="radio" name="extra-field-display" style="width:auto" value="no" />
				<?php esc_html_e( 'No', 'buddypress-groups-extras' ); ?>
			</li>
		</ul>
	</div>

	<div id="extra-field-vars" style="display:none;">
		<div class="content"></div>
		<div class="links">
			<a class="button" href="#" id="add_new"><?php esc_html_e( 'Add New', 'buddypress-groups-extras' ); ?></a>
		</div>
	</div>

	<div>
		<label><?php esc_html_e( 'Field Description', 'buddypress-groups-extras' ); ?></label>
		<textarea name="extra-field-desc"></textarea>
	</div>

	<input type="hidden" name="sf_id_for_field" value="" />
	<input id="addnewfield" type="submit" class="button-primary" name="addnewfield" value="<?php esc_attr_e( 'Add New Field', 'buddypress-groups-extras' ); ?>" />

	<div class="clear"></div>
</div>

<a class="button add_set_fields" href="#"><?php esc_html_e( 'Create the Set of Fields', 'buddypress-groups-extras' ); ?></a>
