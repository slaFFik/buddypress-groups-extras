<ul id="pages-sortable">
	<?php
	foreach ( $pages as $page ) {
		echo '<li id="position_' . $page->ID . '" class="default">
                <strong>' . stripslashes( $page->post_title ) . '</strong> &rarr; ' . ( ( $page->post_status == 'publish' ) ? __( 'displayed', BPGE_I18N ) : __( '<u>not</u> displayed', BPGE_I18N ) ) . '
                <span class="items-link">
                    <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . $page_slug . '/' . $page->post_name . '" class="button" target="_blank" title="' . __( 'View this page live', BPGE_I18N ) . '">' . __( 'View', BPGE_I18N ) . '</a>&nbsp;
                    <a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'admin/' . $slug . '/pages-manage/?edit=' . $page->ID . '" class="button" title="' . __( 'Change its title, content etc', BPGE_I18N ) . '">' . __( 'Edit', BPGE_I18N ) . '</a>&nbsp;
                    <a href="#" class="button delete_page" title="' . __( 'Delete this item and all its content', BPGE_I18N ) . '">' . __( 'Delete', BPGE_I18N ) . '</a>
                </span>
            </li>';
	} ?>
</ul>