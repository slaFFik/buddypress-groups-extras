<?php

if ( ! class_exists( 'BPGE_ADMIN_SETS' ) ) {
	/**
	 * Class BPGE_ADMIN_SETS.
	 */
	class BPGE_ADMIN_SETS extends BPGE_ADMIN_TAB {

		// Position is used to define where exactly this tab will appear
		public $position = 20;
		// Slug that is used in url to access this tab
		public $slug = 'sets';
		// Title is used as a tab name
		public $title = null;

		public function __construct() {

			$this->title = esc_html__( 'Default Sets of Fields', 'buddypress-groups-extras' );

			parent::__construct();
		}

		public function display() {

			echo '<p class="description">';
			esc_html_e( 'Please create/edit here fields you want to be available as standard blocks of data.', 'buddypress-groups-extras' );
			echo '<br>';
			esc_html_e( 'This will be helpful for group admins - no need for them to create lots of fields from scratch.', 'buddypress-groups-extras' );
			echo '</p>';

			$set_args      = array(
				'posts_per_page' => 50,
				'numberposts'    => 50,
				'orderby'        => 'ID',
				'order'          => 'ASC',
				'post_type'      => BPGE_FIELDS_SET,
			);
			$set_of_fields = get_posts( $set_args );

			echo '<ul class="sets">';
			if ( ! empty( $set_of_fields ) ) {
				$fields_args = array(
					'posts_per_page' => 50,
					'numberposts'    => 50,
					'orderby'        => 'ID',
					'order'          => 'ASC',
					'post_type'      => BPGE_FIELDS,
				);
				foreach ( $set_of_fields as $set ) {
					// Get some extra options.
					$set->options = get_post_meta( $set->ID, 'bpge_set_options', true );
					// Get all fields in that set.
					$fields_args['post_parent'] = $set->ID;
					$fields                     = get_posts( $fields_args );
					// Display the html.
					bpge_view( 'admin/set_list', array( 'fields' => $fields, 'set' => $set ) );
				}
			} else {
				echo '<li>';
				echo '<span class="no_fields">' . esc_html__( 'Currently there are no predefined fields. Groups admins should create all fields by themselves.', 'buddypress-groups-extras' ) . '</span>';
				echo '</li>';
			}
			echo '</ul>';

			echo '<div class="clear"></div>';

			// Adding set of fields.
			bpge_view( 'admin/set_add' );

			// Editing for set of fields.
			bpge_view( 'admin/set_edit' );

			// Form to add fields to a set.
			bpge_view( 'admin/set_field_add' );
		}

		public function save() {

			// Save new Set of fields.
			if ( ! empty( $_POST['add_set_fields_name'] ) ) {
				$set = array(
					'post_type'    => BPGE_FIELDS_SET,
					'post_status'  => 'publish',
					'post_title'   => $_POST['add_set_fields_name'],
					'post_content' => $_POST['add_set_field_description'],
				);
				wp_insert_post( $set );
			}

			// Edit Set of fields.
			if ( ! empty( $_POST['edit_set_fields_name'] ) && ! empty( $_POST['edit_set_fields_id'] ) ) {
				$set = array(
					'ID'           => (int) $_POST['edit_set_fields_id'],
					'post_title'   => sanitize_text_field( $_POST['edit_set_fields_name'] ),
					'post_content' => sanitize_text_field( $_POST['edit_set_field_description'] ),
				);
				wp_update_post( $set );
			}

			// Save field for a set.
			if ( ! empty( $_POST['extra-field-title'] ) && ! empty( $_POST['sf_id_for_field'] ) ) {
				// Save field.
				$field_id = wp_insert_post(
					array(
						'post_type'    => BPGE_FIELDS,
						'post_parent'  => (int) $_POST['sf_id_for_field'],
						// Assign to a set of fields.
						'post_title'   => sanitize_text_field( $_POST['extra-field-title'] ),
						'post_content' => sanitize_text_field( $_POST['extra-field-desc'] ),
						'post_excerpt' => sanitize_key( $_POST['extra-field-type'] ),
						'post_status'  => 'publish',
					)
				);

				if ( ! empty( $_POST['options'] ) && is_int( $field_id ) ) {
					$options = array();
					foreach ( $_POST['options'] as $option ) {
						$options[] = htmlspecialchars( strip_tags( $option ) );
					}
					update_post_meta( $field_id, 'bpge_field_options', $options );
				}
				update_post_meta( $field_id, 'bpge_field_display', sanitize_key( $_POST['extra-field-display'] ) );
			}
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_SETS;
	}
}
