<?php
/*******************
 * Several Helpers *
 *******************/

/**
 * Check access level: true or false as return
 */
if ( ! function_exists( 'bpge_user_can' ) ) {
	function bpge_user_can( $item, $user_id = false ) {
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
				if ( $bpge['access_extras'] == 'g_s_admin' && groups_is_user_admin( $user_id, $current_group->id ) ) {
					return true;
				}
				break;
		}

		return false;
	}
}

/**
 * Helper for generating some titles
 *
 * @param string $name
 *
 * @return string
 */
function bpge_names( $name = 'name' ) {
	$text = '';

	switch ( $name ) {
		case 'title_general':
			$text = __( 'Group Extras &rarr; General Settings', 'bpge' );
			break;
		case 'title_fields':
			$text = __( 'Group Extras &rarr; Fields Management', 'bpge' );
			break;
		case 'title_pages':
			$text = __( 'Group Extras &rarr; Pages Management', 'bpge' );
			break;
		case 'title_fields_add':
			$text = __( 'Group Extras &rarr; Add Field', 'bpge' );
			break;
		case 'title_fields_edit':
			$text = __( 'Group Extras &rarr; Edit Field', 'bpge' );
			break;
		case 'title_pages_add':
			$text = __( 'Group Extras &rarr; Add Page', 'bpge' );
			break;
		case 'title_pages_edit':
			$text = __( 'Group Extras &rarr; Edit Page', 'bpge' );
			break;
		case 'name':
			$text = __( 'Description', 'bpge' );
			break;
		case 'nav':
			$text = __( 'Extras', 'bpge' );
			break;
		case 'gpages':
			$text = __( 'Pages', 'bpge' );
			break;
	}

	return $text;
}

/**
 * Empty defaults
 *
 * @return Stdclass
 */
function bpge_get_field_defaults() {
	$field = new Stdclass;

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
 * Get all group fields
 *
 * @param string $status
 * @param bool $group_id
 *
 * @return array
 */
function bpge_get_group_fields( $status = 'publish', $group_id = false ) {
	global $bp;

	if ( empty( $group_id ) ) {
		$group_id = $bp->groups->current_group->id;
	}

	// possible statuses: draft | publish | any

	switch_to_blog(bpge_get_main_site_id());

	$fields = get_posts( array(
		                     'posts_per_page' => 99,
		                     'numberposts'    => 99,
		                     'order'          => 'ASC',
		                     'orderby'        => 'menu_order',
		                     'post_status'    => $status,
		                     'post_parent'    => $group_id,
		                     'post_type'      => BPGE_GFIELDS,
	                     ) );

	restore_current_blog();

	return $fields;
}