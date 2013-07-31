<div id="box_import_set_fields">
    <select name="import_def_set_fields">';
        <?php
        foreach($def_set_fields as $set){
                echo '<option value="' . $set->ID . '" desc="' . htmlspecialchars(strip_tags($set->post_content)) . '" >' . stripslashes($set->post_title) . '</option>';
        } ?>
    </select>
    <div class="import_desc"></div>

    <span class="items-link">
        <a class="button import_set_fields" href="#"><?php _e('Import', 'bpge'); ?></a>
    </span>

    <input id="approve_import" type="hidden" name="approve_import" value="" />
</div>