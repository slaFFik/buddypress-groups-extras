<p>
	<label for="group_extras_display_name"><?php esc_html_e( 'Please specify the page name, where all fields will be displayed', 'buddypress-groups-extras' ); ?></label>
	<input type="text" value="<?php echo esc_attr( $nav_item_name ); ?>" name="group-extras-display-name">
</p>

<p>
	<label for="group_extras_display">
		<?php
		echo sprintf( /* translators: %s - Title of the page. */
			esc_html__( 'Do you want to make the "%s" page public? Everyone will see this page.', 'buddypress-groups-extras' ),
			$nav_item_name
		);
		?>
	</label>
	<input type="radio" value="public" <?php echo checked( $bp->groups->current_group->args['extras']['display_page'], 'public', false ); ?>
	       name="group-extras-display"> <?php esc_html_e( 'Show it', 'buddypress-groups-extras' ); ?><br/>
	<input type="radio" value="private" <?php echo checked( $bp->groups->current_group->args['extras']['display_page'], 'private', false ); ?>
	       name="group-extras-display"> <?php esc_html_e( 'Hide it', 'buddypress-groups-extras' ); ?>
</p>

<p>
	<label for="group_extras_display_layout">
		<?php
		echo sprintf( /* translators: %s - Title of the page. */
			esc_html__( 'Please choose the layout for the "%s" page', 'buddypress-groups-extras' ),
			$nav_item_name
		);
		?>
	</label>
	<input type="radio" value="plain" <?php echo checked( $bp->groups->current_group->args['extras']['display_page_layout'], 'plain', false ); ?>
	       name="group-extras-display-layout"> <?php esc_html_e( 'Plain (field title and its data below)', 'buddypress-groups-extras' ); ?><br/>
	<input type="radio" value="profile" <?php echo checked( $bp->groups->current_group->args['extras']['display_page_layout'], 'profile', false ); ?>
	       name="group-extras-display-layout"> <?php esc_html_e( 'Profile style (in a table)', 'buddypress-groups-extras' ); ?>
</p>

<hr/>

<p>
	<label for="group_extras_display_name"><?php esc_html_e( 'Please specify the page name, where all custom pages will be displayed', 'buddypress-groups-extras' ); ?></label>
	<input type="text" value="<?php echo esc_attr( $gpages_item_name ); ?>" name="group-gpages-display-name">
</p>

<p>
	<label for="group_extras_display">
		<?php
		echo sprintf( /* translators: %s - Title of the page. */
			esc_html__( 'Do you want to make the "%s" page public (extra group pages will be displayed there)?', 'buddypress-groups-extras' ),
			$gpages_item_name
		);
		?>
	</label>
	<input type="radio" value="public" <?php echo checked( $bp->groups->current_group->args['extras']['display_gpages'], 'public', false ); ?>
	       name="group-gpages-display"> <?php esc_html_e( 'Show it', 'buddypress-groups-extras' ); ?><br/>
	<input type="radio" value="private" <?php echo checked( $bp->groups->current_group->args['extras']['display_gpages'], 'private', false ); ?>
	       name="group-gpages-display"> <?php esc_html_e( 'Hide it', 'buddypress-groups-extras' ); ?>
</p>

<?php do_action( 'bpge_template_extras_general_options' ); ?>

<hr/>

<label>
	<?php esc_html_e( 'You can reorder here all navigation links in this group. The first item will become a landing page for this group. Save changes after reordering.', 'buddypress-groups-extras' ); ?><br>
	<?php esc_html_e( 'Please do NOT make admin only pages on first place - that will cause problems.', 'buddypress-groups-extras' ); ?>
</label>

<?php bpge_the_sortable_nav(); ?>

<p>
	<label for="group_extras_home_name">
		<?php esc_html_e( 'Rename the "Home" default group page to "Activity" (for example), which is far better.', 'buddypress-groups-extras' ); ?>
	</label>
	<input type="text" value="<?php echo esc_attr( bpge_get_home_name() ); ?>" name="group-extras-home-name">
</p>

<?php do_action( 'bpge_template_extras_general_before_submit' ); ?>

<div class="clear">&nbsp;</div>

<p>
	<input type="submit" name="save_general" id="save" value="<?php esc_attr_e( 'Save Changes', 'buddypress-groups-extras' ); ?>">
</p>

<?php do_action( 'bpge_template_extras_general_after_submit' ); ?>
