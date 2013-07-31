<?php setup_postdata($page); ?>

<div class="gpage">

    <?php the_content(); ?>

    <?php bpge_the_gpage_edit_link($page->ID); ?>

</div>