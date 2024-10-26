<?php
/* @var $arg array */
/* @var $bpge array */
?>
<table id="bp-gtm-admin-table" class="widefat link-group striped">
	<thead>
		<tr>
			<th class="checkbox">
				<input type="checkbox" id="bpge_allgroups" class="bpge_allgroups" name="bpge_groups" <?php checked( $bpge['groups'], 'all' ); ?> value="all" />
			</th>
			<th>
				<label for="bpge_allgroups">
					<strong><?php esc_html_e( 'All groups', 'buddypress-groups-extras' ); ?></strong>
				</label>
			</th>
			<th>
				<strong><?php esc_html_e( 'Status', 'buddypress-groups-extras' ); ?></strong>
			</th>
		</tr>
	</thead>

	<tbody id="the-list">
		<?php
		if ( bp_has_groups( $arg ) ) {
			while ( bp_groups() ) {
				bp_the_group();

				$group_id = bp_get_group_id();
				?>

				<tr>
	                <td class="checkbox">
	                    <input id="bpge_groups_<?php echo (int) $group_id; ?>" name="bpge_groups[<?php echo (int) $group_id; ?>]" class="bpge_groups" type="checkbox" <?php echo ( ( $bpge['groups'] === 'all' || in_array( $group_id, $bpge['groups'], true ) ) ? 'checked="checked" ' : '' ); ?> value="<?php echo (int) $group_id; ?>" />
	                </td>
	                <td>
	                    <a href="<?php echo esc_url( bp_get_group_url() . 'admin/extras/' ); ?>" target="_blank">
		                    <strong><?php echo esc_html( bp_get_group_name() ); ?></strong>
	                    </a>
	                    <br/>
	                    <label for="bpge_groups_<?php echo (int) $group_id; ?>">
		                    <?php echo esc_html( wp_strip_all_tags( bp_get_group_description_excerpt() ) ); ?>
	                    </label>
	                </td>
					<td>
						<label for="bpge_groups_<?php echo (int) $group_id; ?>">
							<code><?php echo esc_html( bp_get_group_status() ); ?></code>
						</label>
					</td>
	            </tr>
				<?php
			}
		}
		?>
	</tbody>

	<tfoot>
		<tr>
			<th class="checkbox">
				<input type="checkbox" id="bpge_allgroups" class="bpge_allgroups" name="bpge_groups" <?php checked( $bpge['groups'], 'all' ); ?> value="all" />
			</th>
			<th>
				<label for="bpge_allgroups">
					<strong><?php esc_html_e( 'All groups', 'buddypress-groups-extras' ); ?></strong>
				</label>
			</th>
			<th>
				<strong><?php esc_html_e( 'Status', 'buddypress-groups-extras' ); ?></strong>
			</th>
		</tr>
	</tfoot>
</table>
