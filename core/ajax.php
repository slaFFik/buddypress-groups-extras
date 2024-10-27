<?php

/**
 * WP Admin area.
 * Remove set of fields on appropriate tabs.
 */
function bpge_fields_set_delete() {

	global $wpdb;

	if ( ! current_user_can( 'manage_options' ) ) {
		die;
	}

	check_admin_referer( 'bpge_manage_sets_nonce', 'nonce' );

	switch_to_blog( bpge_get_main_site_id() );

	$set_id = absint( $_POST['id'] ?? 0 );

	if ( $set_id ) {
		// Delete the set and all the associated fields.
		// phpcs:disable WordPress.DB.DirectDatabaseQuery
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->posts}` WHERE `ID` = %d", $set_id ) );
		$wpdb->query( $wpdb->prepare( "DELETE FROM `{$wpdb->posts}` WHERE `post_parent` = %d", $set_id ) );
		// phpcs:enable WordPress.DB.DirectDatabaseQuery

		clean_post_cache( $set_id );
	}

	restore_current_blog();

	die( 'deleted' );
}

add_action( 'wp_ajax_fields_set_delete', 'bpge_fields_set_delete' );

/**
 * Front-end ajax requests.
 */
function bpge_ajax() {

	$method = sanitize_key( $_POST['method'] ?? '' );

	$return = 'error';

	switch_to_blog( bpge_get_main_site_id() );

	do_action( 'bpge_ajax', $method );

	switch ( $method ) {
		case 'reorder_fields':
			// Check the nonce first.
			check_ajax_referer( 'bpge_group_edit_fields' );

			global $wpdb;
			parse_str( sanitize_text_field( wp_unslash( $_POST['field_order'] ?? '' ) ), $field_order );

			// Reorder all fields according to new positions.
			$i = 1;

			if ( empty( $field_order['position'] ) ) {
				break;
			}

			foreach ( (array) $field_order['position'] as $field_id ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $i ],
					[ 'ID' => (int) $field_id ],
					[ '%d' ],
					[ '%d' ]
				);

				clean_post_cache( (int) $field_id );

				++$i;
			}
			$return = 'saved';
			break;

		case 'delete_field':
			// Check the nonce first.
			check_ajax_referer( 'bpge_group_edit_fields' );

			$field_id = absint( $_POST['field'] ?? 0 );

			if ( wp_delete_post( $field_id, true ) ) {
				$return = 'deleted';
			}
			break;

		case 'reorder_pages':
			// Check the nonce first.
			check_ajax_referer( 'bpge_group_edit_pages' );

			parse_str( sanitize_text_field( wp_unslash( $_POST['page_order'] ?? '' ) ), $page_order );

			if ( empty( $page_order['position'] ) ) {
				break;
			}

			global $wpdb;

			// Reorder all fields according to new positions.
			$i = 1;

			// Update menu_order for each gpage.
			foreach ( $page_order['position'] as $page_id ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$wpdb->update(
					$wpdb->posts,
					[ 'menu_order' => $i ],
					[ 'ID' => (int) $page_id ],
					[ '%d' ],
					[ '%d' ]
				);

				clean_post_cache( (int) $page_id );

				++$i;
			}
			$return = 'saved';
			break;

		case 'delete_page':
			// Check the nonce first.
			check_ajax_referer( 'bpge_group_edit_pages' );

			$page_id = absint( $_POST['page'] ?? 0 );

			if ( wp_delete_post( $page_id, true ) ) {
				$return = 'deleted';
			}
			break;
	}

	restore_current_blog();

	die( sanitize_key( $return ) );
}

add_action( 'wp_ajax_bpge', 'bpge_ajax' );

/**
 * Admin-specific AJAX actions.
 */
function bpge_ajax_admin() {

	// Check the nonce first.
	check_admin_referer( 'bpge-options' );

	$method = sanitize_key( $_POST['method'] ?? '' );

	$return = 'error';

	switch_to_blog( bpge_get_main_site_id() );

	do_action( 'bpge_ajax_admin', $method );

	switch ( $method ) {
		case 'apply_set':
			$set_id = absint( $_POST['set_id'] ?? 0 );

			if ( empty( $set_id ) ) {
				break;
			}

			// Get all fields for that set.
			$fields = new WP_Query(
				[
					'post_parent' => $set_id,
					'post_type'   => BPGE_FIELDS,
				]
			);

			$to_import = [];

			foreach ( $fields->posts as $field ) {
				$field->desc    = $field->post_content;
				$field->options = get_post_meta( $field->ID, 'bpge_field_options', true );
				$field->display = get_post_meta( $field->ID, 'bpge_field_display', true );

				unset(
					$field->post_date,
					$field->post_date_gmt,
					$field->post_modified,
					$field->post_modified_gmt,
					$field->ID,
					$field->post_content,
					$field->post_parent,
					$field->guid,
					$field->filter,
					$field->post_type
				);

				$to_import[] = $field;
			}

			// Get all groups ids.
			$groups = groups_get_groups(
				[
					'fields'          => 'ids',
					'show_hidden'     => true,
					'populate_extras' => false,
					'per_page'        => 999,
				]
			);

			if ( empty( $groups['groups'] ) ) {
				$return = 'success';

				break;
			}

			// Insert in the loop all the fields where parent_id = group_id.
			foreach ( $groups['groups'] as $group_id ) {
				foreach ( $to_import as $field ) {
					$data                = (array) $field;
					$data['post_parent'] = $group_id;
					$data['post_type']   = BPGE_GFIELDS;
					$data['post_status'] = $field->display === 'yes' ? 'publish' : 'draft';
					$field_id            = wp_insert_post( $data );

					if ( is_int( $field_id ) ) {
						// Save field description.
						update_post_meta( $field_id, 'bpge_field_desc', $data['desc'] );

						// Now save options.
						if ( ! empty( $data['options'] ) ) {
							$options = [];

							foreach ( $data['options'] as $option ) {
								$options[] = htmlspecialchars( wp_strip_all_tags( $option ) );
							}

							update_post_meta( $field_id, 'bpge_field_options', $options );
						}
					}
				}
			}

			update_post_meta( $set_id, 'bpge_set_options', [ 'applied' => 'true' ] );

			$return = 'success';
			break;

		case 'dismiss_review':
			$bpge             = bpge_get_options();
			$bpge['reviewed'] = 'yes';

			bp_update_option( 'bpge', $bpge );

			$return = 'success';
			break;
	}

	restore_current_blog();

	die( sanitize_key( $return ) );
}

add_action( 'wp_ajax_bpge_admin', 'bpge_ajax_admin' );
