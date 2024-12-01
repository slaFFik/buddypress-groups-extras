<?php
/* @var $nav_item_name string */
/* @var $gpages_item_name string */
/* @var $bp BuddyPress */
/* @var $bpge array */

$extras = $bp->groups->current_group->args['extras'];
?>
<p>
	<label for="group-extras-display-name">
		<?php esc_html_e( 'Please specify the page name, where all fields will be displayed.', 'buddypress-groups-extras' ); ?>
	</label>
	<input type="text" value="<?php echo esc_attr( $nav_item_name ); ?>" id="group-extras-display-name" name="group-extras-display-name">
</p>

<p>
	<?php
	printf( /* translators: %s - Title of the page. */
		esc_html__( 'Do you want to make the "%s" page public? Everyone will see this page.', 'buddypress-groups-extras' ),
		esc_html( $nav_item_name )
	);
	?>
</p>

<ul style="margin-left: 0;list-style: none;padding-left: 0;">
	<li>
		<label>
			<input type="radio" value="public" <?php echo checked( $extras['display_page'], 'public', false ); ?> name="group-extras-display">
			<?php esc_html_e( 'Public', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
	<li>
		<label>
			<input type="radio" value="private" <?php echo checked( $extras['display_page'], 'private', false ); ?> name="group-extras-display">
			<?php esc_html_e( 'Hidden', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
</ul>

<p>
	<?php
	printf( /* translators: %s - Title of the page. */
		esc_html__( 'Please choose the layout for the "%s" page', 'buddypress-groups-extras' ),
		esc_html( $nav_item_name )
	);
	?>
</p>
<ul style="margin-left: 0;list-style: none;padding-left: 0;">
	<li>
		<label>
			<input type="radio" value="plain" <?php echo checked( $extras['display_page_layout'], 'plain', false ); ?> name="group-extras-display-layout">
			<?php esc_html_e( 'Plain (field title and its data below)', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
	<li>
		<label>
			<input type="radio" value="profile" <?php echo checked( $extras['display_page_layout'], 'profile', false ); ?> name="group-extras-display-layout">
			<?php esc_html_e( 'Profile style (in a table)', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
</ul>

<hr/>

<p>
	<label for="group-gpages-display-name">
		<?php esc_html_e( 'Please specify the page name, where all custom pages will be displayed.', 'buddypress-groups-extras' ); ?>
	</label>
	<input type="text" value="<?php echo esc_attr( $gpages_item_name ); ?>" id="group-gpages-display-name" name="group-gpages-display-name">
</p>

<p>
	<?php
	printf( /* translators: %s - Title of the page. */
		esc_html__( 'Do you want to make the "%s" page public (extra group pages will be displayed there)?', 'buddypress-groups-extras' ),
		esc_html( $gpages_item_name )
	);
	?>
</p>

<ul style="margin-left: 0;list-style: none;padding-left: 0;">
	<li>
		<label>
			<input type="radio" value="public" <?php echo checked( $extras['display_gpages'], 'public', false ); ?> name="group-gpages-display">
			<?php esc_html_e( 'Public', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
	<li>
		<label>
			<input type="radio" value="private" <?php echo checked( $extras['display_gpages'], 'private', false ); ?> name="group-gpages-display">
			<?php esc_html_e( 'Hidden', 'buddypress-groups-extras' ); ?>
		</label>
	</li>
</ul>

<?php do_action( 'bpge_template_extras_general_options' ); ?>

<hr/>

<?php if ( empty( $bpge['access_nav_reorder'] ) || $bpge['access_nav_reorder'] === 'yes' ) : ?>

	<label>
		<?php esc_html_e( 'Reorder group navigation links', 'buddypress-groups-extras' ); ?>
	</label>
	<p class="description">
		<?php esc_html_e( 'The first item will become a landing page for this group. Save changes after reordering.', 'buddypress-groups-extras' ); ?><br>
	</p>

	<?php bpge_the_sortable_nav(); ?>

<?php endif; ?>

<p>
	<label for="group-extras-home-name">
		<?php esc_html_e( 'Rename the "Home" default group page to "Activity" (for example), which is far better.', 'buddypress-groups-extras' ); ?>
	</label>

	<input type="text" value="<?php echo esc_attr( bpge_get_home_name() ); ?>" id="group-extras-home-name" name="group-extras-home-name">
</p>

<?php do_action( 'bpge_template_extras_general_before_submit' ); ?>

<div class="clear"></div>

<p>
	<input type="submit" name="save_general" id="save" value="<?php esc_attr_e( 'Save Changes', 'buddypress-groups-extras' ); ?>">
</p>

<?php do_action( 'bpge_template_extras_general_after_submit' ); ?>
