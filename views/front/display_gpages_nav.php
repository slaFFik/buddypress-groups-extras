<div role="navigation" id="subnav" class="item-list-tabs no-ajax">
    <ul>
        <?php
        foreach($pages as $page){
            echo '<li '. (($bp->action_variables[0] == $page->post_name) ? 'class="current"' : '') .'>
                <a href="'.bp_get_group_permalink( $bp->groups->current_group ) . $page_slug .'/'. $page->post_name.'">'
                    . stripslashes($page->post_title) .
                '</a>
            </li>';
        } ?>
    </ul>
</div>