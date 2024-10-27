<?php
/* @var $pages WP_Post[] */
/* @var $bp BuddyPress */
/* @var $page_slug string */
/* @var $slug string */
?>
<?php do_action( 'bpge_template_pages_list_sortable_before_list', $pages ); ?>

<ul id="pages-sortable" class="bpge-pages-list-sortable">
	<?php foreach ( $pages as $page ) : ?>
		<li id="position_<?php echo (int) $page->ID; ?>" class="default">
			<strong><?php echo esc_html( $page->post_title ); ?></strong> &rarr;
			<?php echo '<code>' . ( ( $page->post_status === 'publish' ) ? esc_html__( 'public', 'buddypress-groups-extras' ) : esc_html__( 'hidden', 'buddypress-groups-extras' ) ) . '</code>'; ?>

			<span class="items-link">
	            <a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . $page_slug . '/' . $page->post_name ); ?>" class="button view_page" target="_blank">
	                <?php esc_html_e( 'View', 'buddypress-groups-extras' ); ?>
	            </a>&nbsp;

				<a href="<?php echo esc_url( bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $slug . '/pages-manage/?edit=' . $page->ID ); ?>" class="button edit_page">
		            <?php esc_html_e( 'Edit', 'buddypress-groups-extras' ); ?>
	            </a>&nbsp;

				<?php do_action( 'bpge_template_pages_list_sortable_actions', $page, $pages ); ?>

				<a href="#" class="button delete_page" title="<?php esc_attr_e( 'Delete this page and its content', 'buddypress-groups-extras' ); ?>">
					<?php esc_html_e( 'Delete', 'buddypress-groups-extras' ); ?>
				</a>
	        </span>
		</li>
	<?php endforeach; ?>
</ul>

<?php do_action( 'bpge_template_pages_list_sortable_after_list', $pages ); ?>
