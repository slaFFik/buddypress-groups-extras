<p class="description" style="margin-bottom:12px">
    <?php _e('Which groups do you allow to create custom fields and pages for?', 'bpge') ?>
</p>
<table id="bp-gtm-admin-table" class="widefat link-group">
    <tbody id="the-list">
        <tr>
            <td class="checkbox"><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
            <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
        </tr>
        <?php
        if ( bp_has_groups($arg) ){
            while ( bp_groups() ) {
                bp_the_group();
                $description = preg_replace( array('<<p>>', '<</p>>', '<<br />>', '<<br>>'), '', bp_get_group_description_excerpt() );
                echo '<tr>
                        <td class="checkbox">
                            <p><input name="bpge_groups['.bp_get_group_id().']" class="bpge_groups" type="checkbox" '.( ('all' == $bpge['groups'] || in_array(bp_get_group_id(), $bpge['groups']) ) ? 'checked="checked" ' : '').'value="'.bp_get_group_id().'" /></p>
                        </td>
                        <td>
                            <p><a href="'.bp_get_group_permalink().'admin/extras/" target="_blank">'. bp_get_group_name() .'</a> &rarr; '.$description.'</p>
                        </td>
                    </tr>';
            }
        }
        ?>
        <tr>
            <td class="checkbox"><p><input type="checkbox" class="bpge_allgroups" name="bpge_groups" <?php echo ('all' == $bpge['groups']) ? 'checked="checked" ' : ''; ?> value="all" /></p></td>
            <td><p><strong><?php _e('All groups', 'bpge') ?></strong></p></td>
        </tr>
    </tbody>
</table>