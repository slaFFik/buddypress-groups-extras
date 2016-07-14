<?php do_action( 'bpge_template_pages_list_sortable_before_list', $pages ); ?>

<ul id="pages-sortable" class="bpge-pages-list-sortable">
	<?php foreach ( $pages as $page ) : ?>
		<li id="position_<?php echo (int) $page->ID; ?>" class="default">
			<strong><?php echo stripslashes( $page->post_title ); ?></strong> &rarr;
			<?php echo( ( $page->post_status == 'publish' ) ? __( 'displayed', 'buddypress-groups-extras' ) : __( '<u>not</u> displayed', 'buddypress-groups-extras' ) ); ?>

			<span class="items-link">
	            <a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . $page_slug . '/' . $page->post_name ); ?>"
	               class="button view_page" target="_blank"
	               title="<?php esc_attr_e( 'View this page live', 'buddypress-groups-extras' ); ?>">
	                <?php _e( 'View', 'buddypress-groups-extras' ); ?>
	            </a>&nbsp;

				<a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $slug . '/pages-manage/?edit=' . $page->ID ); ?>"
				   class="button edit_page"
				   title="<?php esc_attr_e( 'Change its title, content etc', 'buddypress-groups-extras' ); ?>">
		            <?php _e( 'Edit', 'buddypress-groups-extras' ); ?>
	            </a>&nbsp;

				<?php do_action( 'bpge_template_pages_list_sortable_actions', $page, $pages ); ?>

				<a href="#" class="button delete_page"
				   title="<?php esc_attr_e( 'Delete this item and all its content', 'buddypress-groups-extras' ); ?>">
					<?php _e( 'Delete', 'buddypress-groups-extras' ); ?>
				</a>
	        </span>
		</li>
	<?php endforeach; ?>
</ul>

<?php do_action( 'bpge_template_pages_list_sortable_after_list', $pages ); ?>
