<div id="box_add_field">
    <h4><?php _e('Add field into','bpge');?> &rarr; <span></span></h4>

    <div>
        <label><?php _e('Field Title','bpge');?></label>
        <input type="text" value="" name="extra-field-title">
    </div>

    <div>
        <label><?php _e('Field Type','bpge');?></label>
        <select name="extra-field-type" id="extra-field-type">
            <option value="text"><?php _e('Text Box','bpge');?></option>
            <option value="textarea"><?php _e('Multi-line Text Box','bpge');?></option>
            <option value="checkbox"><?php _e('Checkboxes','bpge');?></option>
            <option value="radio"><?php _e('Radio Buttons','bpge');?></option>
            <!--option value="datebox"><?php _e('Date Selector','bpge');?></option-->
            <option value="select"><?php _e('Dropdown Select Box','bpge');?></option>
        </select>
    </div>

    <div>
        <label><?php _e('Display this field?', 'bpge'); ?></label>
        <input type="radio" name="extra-field-display" style="width:auto" value="yes" />&nbsp;<?php _e('Yes', 'bpge');?>&nbsp;<span class="description"><?php _e('or', 'bpge'); ?></span>&nbsp;
        <input type="radio" name="extra-field-display" style="width:auto" value="no" />&nbsp;<?php _e('No', 'bpge');?>
    </div>

    <div id="extra-field-vars" style="display:none;">
        <div class="content"></div>
        <div class="links">
            <a class="button" href="#" id="add_new"><?php _e('Add New','bpge');?></a>
       </div>
    </div>

    <div>
        <label><?php _e('Field Description','bpge');?></label>
        <textarea name="extra-field-desc"></textarea>
    </div>

    <input type="hidden" name="sf_id_for_field" value="" />
    <input id="addnewfield" type="submit" class="button-primary" name="addnewfield" value="<?php _e('Add New Field','bpge');?>" />
</div>

<a class="button add_set_fields" href="#"><?php _e('Create the Set of Fields','bpge');?></a>