<?php
/**
 * Register group fields
 */
function bpge_register_fields() {
	$labels = array(
		'name' => __( 'Groups Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'comments' )
	);

	register_post_type( BPGE_GFIELDS, $args );
}

/**
 * Register set of fields post types
 */
function bpge_register_set() {
	$labels = array(
		'name' => __( 'Sets of Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes' )
	);

	register_post_type( BPGE_FIELDS_SET, $args );
}

/**
 * Register groups fields post type, where all their content will be stored
 */
function bpge_register_set_fields() {
	$labels = array(
		'name' => __( 'Groups Fields', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => false,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes' )
	);

	register_post_type( BPGE_FIELDS, $args );
}

/**
 * Register groups pages post type, where all their content will be stored
 */
function bpge_register_groups_pages() {
	$labels = array(
		'name'          => __( 'Groups Pages', 'bpge' ),
		'singular_name' => __( 'Groups Page', 'bpge' )
	);
	$args   = array(
		'labels'              => $labels,
		'public'              => true,
		'show_in_menu'        => true,
		'exclude_from_search' => true,
		'show_in_nav_menus'   => false,
		'menu_position'       => 100,
		'hierarchical'        => true,
		'query_var'           => true,
		'rewrite'             => false,
		'capability_type'     => 'page',
		'supports'            => array( 'title', 'editor', 'custom-fields', 'page-attributes', 'comments' )
	);
	register_post_type( BPGE_GPAGES, $args );
}

/**
 * Delete associated gpage on group delete
 *
 * @param object $group_obj
 * @param array $user_ids
 */
function bpge_delete_group(
	$group_obj, /** @noinspection PhpUnusedParameterInspection */
	$user_ids
) {
	/** @var $wpdb WPDB */
	global $bp, $wpdb;
	$to_delete = false;
	$pages     = $fields = array();

	if ( isset( $bp->groups->current_group->extras['gpage_id'] ) && ! empty( $bp->groups->current_group->extras['gpage_id'] ) ) {
		$to_delete = $bp->groups->current_group->extras['gpage_id'];
	} else {
		$bpge = groups_get_groupmeta( $group_obj->id, 'bpge' );
		if ( $bpge && isset( $bpge['gpage_id'] ) && ! empty( $bpge['gpage_id'] ) ) {
			$to_delete = $bpge['gpage_id'];
		}
	}

	if ( ! empty( $to_delete ) ) {
		// remove all group pages
		$pages = $wpdb->get_col( $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE `post_parent` = %d",
			$to_delete
		) );
	}
	// remove all group fields
	$fields = $wpdb->get_col( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE `post_type` = '%s' AND  `post_parent` = %d",
		BPGE_GFIELDS, $group_obj->id
	) );
	$data   = array_merge( $pages, $fields );
	if ( ! empty( $data ) ) {
		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$wpdb->posts} WHERE `ID` IN (%s)",
			implode( ',', $data )
		) );
	}

	if ( ! empty( $to_delete ) ) {
		wp_delete_post( $to_delete, true );
	}
}

add_action( 'bp_groups_delete_group', 'bpge_delete_group', 10, 2 );

/**
 * Рide add new menu and redirect from it to the whole list - do not allow admin to add manually
 */
function bpge_gpages_hide_add_new() {
	global $submenu;

	unset(
		$submenu['edit.php?post_type=gpages'][10],
		$submenu[ 'edit.php?post_type=' . BPGE_FIELDS ][10],
		$submenu[ 'edit.php?post_type=' . BPGE_FIELDS_SET ][10]
	);
}

add_action( 'admin_menu', 'bpge_gpages_hide_add_new' );

/**
 * Do not allow create group pages in wp-admin area - redirect to the list
 */
function bpge_gpages_redirect_to_all() {
	$result = stripos( $_SERVER['REQUEST_URI'], 'post-new.php?post_type=gpages' );
	if ( $result !== false ) {
		wp_redirect( get_option( 'siteurl' ) . '/wp-admin/edit.php?post_type=gpages' );
	}
}

add_action( 'admin_menu', 'bpge_gpages_redirect_to_all' );