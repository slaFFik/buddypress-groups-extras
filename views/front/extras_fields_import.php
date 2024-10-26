<div id="box_import_set_fields">
	<select name="import_def_set_fields">
		<?php
		foreach ( $def_set_fields as $set ) {
			echo '<option value="' . (int) $set->ID . '" desc="' . esc_attr( strip_tags( $set->post_content ) ) . '" >' . esc_html( $set->post_title ) . '</option>';
		} ?>
	</select>
	<div class="import_desc"></div>

	<span class="items-link">
        <a class="button import_set_fields" href="#"><?php esc_html_e( 'Import', 'buddypress-groups-extras' ); ?></a>
    </span>

	<input id="approve_import" type="hidden" name="approve_import" value="" />
</div>
