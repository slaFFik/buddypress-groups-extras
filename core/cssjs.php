<?php

/**
 * Load js on appropriate pages only.
 */
function bpge_load_js() {

	if ( bp_is_group() && bp_current_action() === 'admin' && bp_action_variable( 0 ) === 'extras' ) {
		wp_enqueue_script( 'bpge-main', BPGE_URL . '/js/extra-scripts.js', array( 'jquery', 'jquery-ui-sortable' ), BPGE_VERSION );
		// Localize js string.
		wp_localize_script( 'bpge-main', 'bpge', bpge_get_localized_data() );
	}
}

add_action( 'wp_enqueue_scripts', 'bpge_load_js' );


/**
 * JS translatable strings.
 */
function bpge_get_localized_data() {

	return apply_filters(
		'bpge_load_js_localized',
		array(
			'enter_options'     => esc_html__( 'Please enter options for this Field', 'buddypress-groups-extras' ),
			'option_text'       => esc_html__( 'Option', 'buddypress-groups-extras' ),
			'remove_it'         => esc_html__( 'Remove It', 'buddypress-groups-extras' ),
			'apply_set'         => esc_html__( 'Do you want to apply this set of fields to all groups on your site?', 'buddypress-groups-extras' ),
			'applied'           => esc_html__( 'Applied', 'buddypress-groups-extras' ),
			'close'             => esc_html__( 'Close', 'buddypress-groups-extras' ),
			'yes'               => esc_html__( 'Yes', 'buddypress-groups-extras' ),
			'no'                => esc_html__( 'No', 'buddypress-groups-extras' ),
			'success'           => esc_html__( 'Success', 'buddypress-groups-extras' ),
			'success_apply_set' => esc_html__( 'This set of fields was successfully applied to all groups on this site.', 'buddypress-groups-extras' ),
			'error'             => esc_html__( 'Error', 'buddypress-groups-extras' ),
			'error_apply_set'   => esc_html__( 'Unfortunately, there was an error while applying this set of fields. Please try again a bit later or recreate the set from scratch. Be aware, that re-applying this set will double fields for those groups that were successful.', 'buddypress-groups-extras' ),
		) );
}

/**
 * Load css on appropriate pages only.
 */
function bpge_load_css() {

	if ( bp_is_group() ) {
		global $bpge;

		if (
			( is_array( $bpge['groups'] ) && in_array( bp_get_current_group_id(), $bpge['groups'], true ) ) ||
			( ! empty( $bpge['groups'] ) && $bpge['groups'] === 'all' )
		) {
			wp_enqueue_style( 'bpge-main', BPGE_URL . '/css/extra-styles.css', false, BPGE_VERSION );
		}
	}
}

add_action( 'wp_enqueue_scripts', 'bpge_load_css' );
