<?php
/**
 * Set list item.
 *
 * @var WP_Post[] $fields List of fields stored as CPT.
 * @var WP_Post   $set    List of set of fields stored as CPT.
 */

$applied = ( isset( $set->options['applied'] ) && $set->options['applied'] === 'true' ) ? 'applied' : false;
?>

<li id="set-<?php echo (int) $set->ID; ?>">

	<span class="name"><?php echo esc_html( $set->post_title ); ?></span>
	<span class="desc"><?php echo wp_kses_post( $set->post_content ); ?></span>

	<span class="actions">
		<a class="button display_fields" data-set_id="<?php echo (int) $set->ID; ?>" href="#">
			<?php esc_html_e( 'Show Fields', 'buddypress-groups-extras' ); ?> (<?php echo count( $fields ); ?>)
		</a>
        <a class="button set_apply <?php echo esc_attr( $applied ); ?>" <?php echo( $applied ? 'disabled' : '' ); ?> data-set_id="<?php echo (int) $set->ID; ?>" href="#"
	        title="<?php echo ( ! $applied ? esc_attr__( 'Apply to all groups this set of fields', 'buddypress-groups-extras' ) : esc_attr__( 'Already applied', 'buddypress-groups-extras' ) ); ?>">
            <?php echo ( ! $applied ? esc_html__( 'Apply', 'buddypress-groups-extras' ) : esc_html__( 'Applied', 'buddypress-groups-extras' ) ); ?>
        </a>
        <a class="button field_edit" data-set_id="<?php echo (int) $set->ID; ?>" href="#">
	        <?php esc_html_e( 'Edit', 'buddypress-groups-extras' ); ?>
        </a>
        <a class="button-link-delete field_delete" data-set_id="<?php echo (int) $set->ID; ?>" href="#">
	        <?php esc_html_e( 'Delete', 'buddypress-groups-extras' ); ?>
        </a>
    </span>

	<div class="fields">

		<?php if ( empty( $fields ) ) { ?>

			<div class="clear"></div>
			<p class="no-fields">
				<strong><?php esc_html_e( 'There are no fields in this set, yet.', 'buddypress-groups-extras' ); ?></strong>
			</p>

		<?php } else { ?>

			<table id="fields_<?php echo (int) $set->ID; ?>">
				<thead>
				<tr>
					<th class="field-title"><?php esc_html_e( 'Field Title', 'buddypress-groups-extras' ); ?></th>
					<th class="field-type"><?php esc_html_e( 'Type', 'buddypress-groups-extras' ); ?></th>
					<th class="field-desc"><?php esc_html_e( 'Description', 'buddypress-groups-extras' ); ?></th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $fields as $field ) { ?>
					<tr>
						<td><?php echo esc_html( $field->post_title ); ?></td>
						<td><code><?php echo esc_html( $field->post_excerpt ); ?></code></td>
						<td><?php echo wp_kses_post( $field->post_content ); ?></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>

		<?php } ?>

		<div class="clear"></div>

		<a class="button add_field" data-set_id="<?php echo (int) $set->ID; ?>" href="#">
			<?php esc_html_e( 'Add field', 'buddypress-groups-extras' ); ?>
		</a>

	</div>

</li>
