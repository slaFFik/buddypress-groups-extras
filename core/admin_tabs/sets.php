<?php

if ( ! class_exists( 'BPGE_ADMIN_SETS' ) ) {
	/**
	 * Class BPGE_ADMIN_SETS.
	 */
	class BPGE_ADMIN_SETS extends BPGE_ADMIN_TAB {

		/**
		 * Position is used to define where exactly this tab will appear.
		 *
		 * @var int
		 */
		public $position = 20;
		/**
		 * Slug that is used in url to access this tab.
		 *
		 * @var string
		 */
		public $slug = 'sets';

		/**
		 * Title is used as a tab name.
		 *
		 * @var string
		 */
		public $title = '';

		/**
		 * BPGE_ADMIN_SETS constructor.
		 */
		public function __construct() {

			$this->title = esc_html__( 'Default Sets of Fields', 'buddypress-groups-extras' );

			parent::__construct();
		}

		/**
		 * Render the content of the tab.
		 */
		public function display() {

			echo '<p>';
			esc_html_e( 'Please create/edit here fields you want to be available as standard blocks of data.', 'buddypress-groups-extras' );
			echo '<br>';
			esc_html_e( 'This will be helpful for group admins - no need for them to create lots of fields from scratch.', 'buddypress-groups-extras' );
			echo '</p>';

			$set_of_fields = get_posts(
				[
					'posts_per_page' => 50,
					'numberposts'    => 50,
					'orderby'        => 'ID',
					'order'          => 'ASC',
					'post_type'      => BPGE_FIELDS_SET,
				]
			);
			?>

			<ul class="sets">

				<?php
				if ( ! empty( $set_of_fields ) ) {
					$fields_args = [
						'posts_per_page' => 50,
						'numberposts'    => 50,
						'orderby'        => 'ID',
						'order'          => 'ASC',
						'post_type'      => BPGE_FIELDS,
					];

					foreach ( $set_of_fields as $set ) {
						// Get some extra options.
						$set->options = get_post_meta( $set->ID, 'bpge_set_options', true );

						// Get all fields in that set.
						$fields_args['post_parent'] = $set->ID;
						$fields                     = get_posts( $fields_args );

						// Display the html.
						bpge_view(
							'admin/set_list',
							[
								'fields' => $fields,
								'set'    => $set,
							]
						);
					}
				} else {
				?>
				<li>
					<span class="no_fields">
						<?php esc_html_e( 'Currently there are no predefined fields. Groups admins should create all fields by themselves.', 'buddypress-groups-extras' ); ?>
					</span>
				</li>

				<?php } ?>

			</ul>

			<div class="clear"></div>

			<?php
			wp_nonce_field( 'bpge_manage_sets_nonce', 'bpge_manage_sets_nonce' );

			// Adding set of fields.
			bpge_view( 'admin/set_add' );

			// Editing for set of fields.
			bpge_view( 'admin/set_edit' );

			// Form to add fields to a set.
			bpge_view( 'admin/set_field_add' );
		}

		/**
		 * Save the data from the tab.
		 */
		public function save() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

			check_admin_referer( 'bpge_manage_sets_nonce', 'bpge_manage_sets_nonce' );

			// Save a new Set of fields.
			if ( ! empty( $_POST['add_set_fields_name'] ) ) {
				$set = [
					'post_type'    => BPGE_FIELDS_SET,
					'post_status'  => 'publish',
					'post_title'   => sanitize_text_field( wp_unslash( $_POST['add_set_fields_name'] ?? '' ) ),
					'post_content' => wp_kses_post( wp_unslash( $_POST['add_set_field_description'] ?? '' ) ),
				];

				wp_insert_post( $set );
			}

			// Edit Set of fields.
			if ( ! empty( $_POST['edit_set_fields_name'] ) && ! empty( $_POST['edit_set_fields_id'] ) ) {
				wp_update_post(
					[
						'ID'           => absint( $_POST['edit_set_fields_id'] ?? 0 ),
						'post_title'   => sanitize_text_field( wp_unslash( $_POST['edit_set_fields_name'] ?? '' ) ),
						'post_content' => sanitize_text_field( wp_unslash( $_POST['edit_set_field_description'] ?? '' ) ),
					]
				);
			}

			// Save field for a set.
			if ( ! empty( $_POST['extra-field-title'] ) && ! empty( $_POST['sf_id_for_field'] ) ) {
				// Save field.
				$field_id = wp_insert_post(
					[
						'post_type'    => BPGE_FIELDS,
						'post_parent'  => absint( $_POST['sf_id_for_field'] ?? 0 ),
						// Assign to a set of fields.
						'post_title'   => sanitize_text_field( wp_unslash( $_POST['extra-field-title'] ?? '' ) ),
						'post_content' => sanitize_text_field( wp_unslash( $_POST['extra-field-desc'] ?? '' ) ),
						'post_excerpt' => sanitize_key( $_POST['extra-field-type'] ?? '' ),
						'post_status'  => 'publish',
					]
				);

				if ( ! empty( $_POST['options'] ) && is_int( $field_id ) ) {
					$options = [];

					foreach ( wp_unslash( $_POST['options'] ) as $option ) {
						$options[] = htmlspecialchars( wp_strip_all_tags( $option ) );
					}

					update_post_meta( $field_id, 'bpge_field_options', $options );
				}

				update_post_meta( $field_id, 'bpge_field_display', sanitize_key( $_POST['extra-field-display'] ?? '' ) );
			}
		}
	}

	/**
	 * Now we need to init this class.
	 */
	if ( is_admin() ) {
		return new BPGE_ADMIN_SETS();
	}
}
