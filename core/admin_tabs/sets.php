<?php
if ( ! class_exists( 'BPGE_ADMIN_SETS' ) ) {

	/**
	 *
	 */
	class BPGE_ADMIN_SETS extends BPGE_ADMIN_TAB {
		// position is used to define where exactly this tab will appear
		var $position = 20;
		// slug that is used in url to access this tab
		var $slug = 'sets';
		// title is used as a tab name
		var $title = null;

		function __construct() {
			$this->title = __( 'Default Sets of Fields', 'bpge' );

			parent::__construct();
		}

		function display() {
			echo '<p class="description">';
			_e( 'Please create/edit here fields you want to be available as standard blocks of data.<br />This will be helpful for group admins - no need for them to create lots of fields from scratch.', 'bpge' );
			echo '</p>';

			$set_args      = array(
				'posts_per_page' => 50,
				'numberposts'    => 50,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => BPGE_FIELDS_SET
			);
			$set_of_fields = get_posts( $set_args );

			echo '<ul class="sets">';
			if ( ! empty( $set_of_fields ) ) {
				$fields_args = array(
					'posts_per_page' => 50,
					'numberposts'    => 50,
					'orderby'        => 'ID',
					'order'          => 'ASC',
					'post_type'      => BPGE_FIELDS
				);
				foreach ( $set_of_fields as $set ) {
					// get some extra options
					$set->options = get_post_meta( $set->ID, 'bpge_set_options', true );
					// get all fields in that set
					$fields_args['post_parent'] = $set->ID;
					$fields                     = get_posts( $fields_args );
					// display the html
					bpge_view( 'admin/set_list', array( 'fields' => $fields, 'set' => $set ) );
				}
			} else {
				echo '<li>';
				echo '<span class="no_fields">' . __( 'Currently there are no predefined fields. Groups admins should create all fields by themselves.', 'bpge' ) . '</span>';
				echo '</li>';
			}
			echo '</ul>';

			echo '<div class="clear"></div>';

			// Adding set of fields
			bpge_view( 'admin/set_add' );

			// Editing for set of fields
			bpge_view( 'admin/set_edit' );

			// Form to add fields to a set
			bpge_view( 'admin/set_field_add' );
		}

		function save() {
			// Save new Set of fields
			if ( ! empty( $_POST['add_set_fields_name'] ) ) {
				$set = array(
					'post_type'    => BPGE_FIELDS_SET,
					'post_status'  => 'publish',
					'post_title'   => $_POST['add_set_fields_name'],
					'post_content' => $_POST['add_set_field_description']
				);
				wp_insert_post( $set );
			}

			// Edit Set of fields
			if ( ! empty( $_POST['edit_set_fields_name'] ) && ! empty( $_POST['edit_set_fields_id'] ) ) {
				$set = array(
					'ID'           => $_POST['edit_set_fields_id'],
					'post_title'   => $_POST['edit_set_fields_name'],
					'post_content' => $_POST['edit_set_field_description']
				);
				wp_update_post( $set );
			}

			// Save field for a set
			if ( ! empty( $_POST['extra-field-title'] ) && ! empty( $_POST['sf_id_for_field'] ) ) {
				// save field
				$field_id = wp_insert_post( array(
					                            'post_type'    => BPGE_FIELDS,
					                            'post_parent'  => $_POST['sf_id_for_field'],
					                            // assign to a set of fields
					                            'post_title'   => $_POST['extra-field-title'],
					                            'post_content' => $_POST['extra-field-desc'],
					                            'post_excerpt' => $_POST['extra-field-type'],
					                            'post_status'  => 'publish'
				                            ) );

				if ( ! empty( $_POST['options'] ) && is_integer( $field_id ) ) {
					$options = array();
					foreach ( $_POST['options'] as $option ) {
						$options[] = htmlspecialchars( strip_tags( $option ) );
					}
					update_post_meta( $field_id, 'bpge_field_options', $options );
				}
				update_post_meta( $field_id, 'bpge_field_display', $_POST['extra-field-display'] );
			}
		}
	}

	/**
	 * Now we need to init this class
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_SETS;
	}

}