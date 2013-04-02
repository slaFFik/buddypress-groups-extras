<div class="box_field">
    <!-- <p> -->

    <label><?php _e('Field Title', 'bpge'); ?></label>
    <input type="text" value="<?php echo $field->post_title; ?>" name="extra-field-title">

    <?php if (empty($field->ID)){ ?>
        <label><?php _e('Field Type', 'bpge'); ?></label>
        <select name="extra-field-type" id="extra-field-type">
            <option value="text"><?php _e('Text Box', 'bpge'); ?></option>
            <option value="textarea"><?php _e('Multi-line Text Box', 'bpge'); ?></option>
            <option value="checkbox"><?php _e('Checkboxes', 'bpge'); ?></option>
            <option value="radio"><?php _e('Radio Buttons', 'bpge'); ?></option>
            <!-- <option value="datebox"><?php _e('Date Selector', 'bpge'); ?></option> -->
            <option value="select"><?php _e('Drop Down Select Box', 'bpge'); ?></option>
        </select>

        <div id="extra-field-vars">
            <div class="content"></div>
            <div class="links">
                <a class="button" href="#" id="add_new"><?php _e('Add New', 'bpge'); ?></a>
            </div>
        </div>
    <?php } ?>

    <label><?php _e('Field Description', 'bpge'); ?></label>
    <textarea name="extra-field-desc"><?php echo $field->post_title; ?></textarea>

    <label for="extra-field-required"><?php _e('Is this field required (will be marked as required on group Edit Details page)?','bpge'); ?></label>
    <?php
    $req = '';
    $not_req = 'checked="checked"';
    if ( $field->to_ping == '1' ) {
        $req = 'checked="checked"';
        $not_req = '';
    } ?>
    <input type="radio" value="1" <?php echo $req;?>     name="extra-field-required"> <?php _e('Required', 'bpge'); ?><br />
    <input type="radio" value="0" <?php echo $not_req;?> name="extra-field-required"> <?php _e('Not Required', 'bpge'); ?><br />

    <label for="extra-field-display"><?php echo sprintf(__('Should this field be displayed for public on "<u>%s</u>" page?','bpge'), $nav_item_name); ?></label>
    <?php if(empty($field->post_status)) $field->post_status = 'draft'; ?>
    <input type="radio" name="extra-field-display" value="publish" <?php checked($field->post_status, 'pubish'); ?>> <?php _e('Display it', 'bpge'); ?><br />
    <input type="radio" name="extra-field-display" value="draft" <?php checked($field->post_status, 'draft'); ?>> <?php _e('Do NOT display it', 'bpge'); ?>

    <!-- </p> -->

    <?php if (empty($field->ID)){ ?>
        <p><input type="submit" name="save_fields_add" id="save" value="<?php _e('Create New &rarr;','bpge'); ?>"></p>
    <?php }else{ ?>
        <input type="hidden" name="extra-field-id" value="<?php echo $field->ID; ?>">
        <p><input type="submit" name="save_fields_edit" id="save" value="<?php _e('Save Changes &rarr;','bpge'); ?>"></p>
    <?php } ?>
</div>

<?php wp_nonce_field('groups_edit_group_extras'); ?>