<?php
/*******************
 * Several Helpers *
 *******************/

/**
 * Check access level: true or false as return.
 */
if ( ! function_exists( 'bpge_user_can' ) ) {
	function bpge_user_can( $item, $user_id = 0 ) {

		global $bpge;

		if ( empty( $user_id ) || ! is_int( $user_id ) || $user_id < 1 ) {
			$user_id = bp_loggedin_user_id();
		}

		if ( is_super_admin( $user_id ) ) {
			return true;
		}

		$current_group = groups_get_current_group();

		switch ( $item ) {
			case 'group_extras_admin':
				if ( $bpge['access_extras'] === 'g_s_admin' && groups_is_user_admin( $user_id, $current_group->id ) ) {
					return true;
				}
				break;
		}

		return false;
	}
}

/**
 * Helper for generating some titles.
 *
 * @param string $name
 *
 * @return string
 */
function bpge_names( $name = 'name' ) {

	$text = '';

	switch ( $name ) {
		case 'title_general':
			$text = esc_html__( 'Group Extras &rarr; General Settings', 'buddypress-groups-extras' );
			break;
		case 'title_fields':
			$text = esc_html__( 'Group Extras &rarr; Fields Management', 'buddypress-groups-extras' );
			break;
		case 'title_pages':
			$text = esc_html__( 'Group Extras &rarr; Pages Management', 'buddypress-groups-extras' );
			break;
		case 'title_fields_add':
			$text = esc_html__( 'Group Extras &rarr; Add Field', 'buddypress-groups-extras' );
			break;
		case 'title_fields_edit':
			$text = esc_html__( 'Group Extras &rarr; Edit Field', 'buddypress-groups-extras' );
			break;
		case 'title_pages_add':
			$text = esc_html__( 'Group Extras &rarr; Add Page', 'buddypress-groups-extras' );
			break;
		case 'title_pages_edit':
			$text = esc_html__( 'Group Extras &rarr; Edit Page', 'buddypress-groups-extras' );
			break;
		case 'name':
			$text = esc_html__( 'Description', 'buddypress-groups-extras' );
			break;
		case 'nav':
			$text = esc_html__( 'Extras', 'buddypress-groups-extras' );
			break;
		case 'gpages':
			$text = esc_html__( 'Pages', 'buddypress-groups-extras' );
			break;
		case 'home':
			$text = esc_html__( 'Home', 'buddypress-groups-extras' );
			break;
	}

	return $text;
}

/**
 * Empty defaults.
 *
 * @return Stdclass
 */
function bpge_get_field_defaults() {

	$field = new stdClass();

	$field->ID           = '';
	$field->desc         = '';
	$field->post_title   = '';
	$field->post_content = '';
	$field->post_excerpt = ''; // field type - radio, select, checkbox, text, textarea, datebox
	$field->post_status  = '';
	$field->pinged       = ''; // required or not
	$field->post_type    = BPGE_GFIELDS;

	return $field;
}

/**
 * Get all group fields.
 *
 * @param string $status
 * @param bool   $group_id
 *
 * @return array
 */
function bpge_get_group_fields( $status = 'publish', $group_id = false ) {

	$bp = buddypress();

	if ( empty( $group_id ) ) {
		$group_id = bp_get_current_group_id();
	}

	// Possible statuses: draft | publish | any.

	switch_to_blog( bpge_get_main_site_id() );

	$fields = get_posts(
		array(
			'posts_per_page' => 99,
			'numberposts'    => 99,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_status'    => $status,
			'post_parent'    => $group_id,
			'post_type'      => BPGE_GFIELDS,
		)
	);

	restore_current_blog();

	return $fields;
}

/**
 * Check that we are on BuddyPress 2.6+.
 *
 * @return bool
 */
function bpge_is_bp_26() {

	return version_compare( bp_get_version(), '2.6', '>=' );
}

/**
 * Get the group navigation array.
 *
 * @return array
 */
function bpge_get_group_nav() {

	if ( bpge_is_bp_26() ) {
		$nav = buddypress()->groups->nav->get_secondary(
			array(
				'parent_slug' => bp_get_current_group_slug(),
			)
		);
	} else {
		$bp  = buddypress();
		$nav = $bp->bp_options_nav[ $bp->groups->current_group->slug ];
	}

	return $nav;
}

/**
 * Display a new home name for group navigation.
 */
function bpge_get_home_name() {

	return ! empty( buddypress()->groups->current_group->args['extras']['home_name'] ) ? buddypress()->groups->current_group->args['extras']['home_name'] : bpge_names( 'home' );
}
