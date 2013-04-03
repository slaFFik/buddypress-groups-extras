<div class="box_field">

    <!-- Title -->
    <label for="extra-field-title"><?php _e('Field Title', 'bpge'); ?></label>
    <input type="text" value="<?php esc_attr_e($field->post_title); ?>" name="extra-field-title" id="extra-field-title">
    <script>jQuery('#extra-field-title').focus();</script>

    <!-- Type -->
    <?php if (empty($field->ID)){ ?>
        <label><?php _e('Field Type', 'bpge'); ?></label>
        <select name="extra-field-type" id="extra-field-type">
            <option value="text" <?php selected($field->post_excerpt, 'text'); ?>><?php _e('Text Box', 'bpge'); ?></option>
            <option value="textarea" <?php selected($field->post_excerpt, 'textarea'); ?>><?php _e('Multi-line Text Box', 'bpge'); ?></option>
            <option value="checkbox" <?php selected($field->post_excerpt, 'checkbox'); ?>><?php _e('Checkboxes', 'bpge'); ?></option>
            <option value="radio" <?php selected($field->post_excerpt, 'radio'); ?>><?php _e('Radio Buttons', 'bpge'); ?></option>
            <!-- <option value="datebox" <?php selected($field->post_excerpt, 'datebox'); ?>><?php _e('Date Selector', 'bpge'); ?></option> -->
            <option value="select" <?php selected($field->post_excerpt, 'select'); ?>><?php _e('Drop Down Select Box', 'bpge'); ?></option>
        </select>

        <div id="extra-field-vars">
            <div class="content"></div>
            <div class="links">
                <a class="button" href="#" id="add_new"><?php _e('Add New', 'bpge'); ?></a>
            </div>
        </div>
    <?php }else{ ?>
        <input type="hidden" name="extra-field-type" value="<?php echo $field->post_excerpt; ?>" />
    <?php } ?>

    <!-- Description -->
    <label><?php _e('Field Description', 'bpge'); ?></label>
    <textarea name="extra-field-desc"><?php echo $field->desc; ?></textarea>

    <!-- Required or not? -->
    <?php if(empty($field->pinged)) $field->pinged = 'not_req'; ?>
    <label for="extra-field-required"><?php _e('Is this field required (will be marked as required on group Edit Details page)?','bpge'); ?></label>
    <input type="radio" value="req" <?php checked($field->pinged, 'req'); ?> name="extra-field-required"> <?php _e('Required', 'bpge'); ?><br />
    <input type="radio" value="not_req" <?php checked($field->pinged, 'not_req'); ?> name="extra-field-required"> <?php _e('Not Required', 'bpge'); ?><br />

    <!-- Display or not? -->
    <label for="extra-field-display"><?php echo sprintf(__('Should this field be displayed for public on "<u>%s</u>" page?','bpge'), $nav_item_name); ?></label>
    <?php if(empty($field->post_status)) $field->post_status = 'draft'; ?>
    <input type="radio" name="extra-field-display" value="publish" <?php checked($field->post_status, 'publish'); ?>> <?php _e('Display it', 'bpge'); ?><br />
    <input type="radio" name="extra-field-display" value="draft" <?php checked($field->post_status, 'draft'); ?>> <?php _e('Do NOT display it', 'bpge'); ?>

    <?php if (empty($field->ID)){ ?>
        <p><input type="submit" name="save_fields_add" id="save" value="<?php _e('Create New &rarr;','bpge'); ?>"></p>
    <?php }else{ ?>
        <input type="hidden" name="extra-field-id" value="<?php echo $field->ID; ?>">
        <p><input type="submit" name="save_fields_edit" id="save" value="<?php _e('Save Changes &rarr;','bpge'); ?>"></p>
    <?php } ?>
</div>

<?php wp_nonce_field('groups_edit_group_extras'); ?>