jQuery(document).ready(function($){

    /*
     * SECTION: MANAGE FIELDS / PAGES
     */
    // sorting fields
    jQuery("#fields-sortable").sortable({
        placeholder: "highlight",
        update: function(event, ui){
            jQuery.post( ajaxurl, {
                action: 'bpge',
                method: 'reorder_fields',
                field_order: jQuery(this).sortable('serialize')
            },
            function(response){});
        }
    });
    jQuery( "#fields-sortable" ).disableSelection();

    // sorting pages
    jQuery("#pages-sortable").sortable({
        placeholder: "highlight",
        update: function(event, ui){
            jQuery.post( ajaxurl, {
                action: 'bpge',
                method: 'reorder_pages',
                page_order: jQuery(this).sortable('serialize')
            },
            function(response){});
        }
    });
    jQuery( "#pages-sortable" ).disableSelection();

    // sorting nav
    jQuery("#nav-sortable").sortable({
        placeholder: "highlight",
        update: function(event, ui){
            jQuery('input[name="bpge_group_nav_position"]').val(jQuery(this).sortable('serialize'));
        }
    });
    jQuery( "#nav-sortable" ).disableSelection();

    // delete field
    jQuery("#fields-sortable li span a.delete_field").click(function(e){
        e.preventDefault();
        var field = jQuery(this).parent().parent().attr('id').split('_')[1];
        jQuery.post( ajaxurl, {
            action: 'bpge',
            method: 'delete_field',
            field: field
        },
        function(response){
            if (response == 'deleted' )
                jQuery('#fields-sortable li#position_'+field).fadeOut('fast');
        });
    });

    // delete page
    jQuery("#pages-sortable li span a.delete_page").click(function(e){
        e.preventDefault();
        var page = jQuery(this).parent().parent().attr('id').split('_')[1];
        jQuery.post( ajaxurl, {
            action: 'bpge',
            method: 'delete_page',
            page: page
        },
        function(response){
            if (response == 'deleted' )
                jQuery('#pages-sortable li#position_'+page).fadeOut('fast');
        });
    });

    /*
     * SECTION: ADD / EDIT FIELDS
     */
    var options_count = 2;
    function new_option(type, id){
        return '<span class="'+type+'_'+id+'">' + bpge.option_text + ': &rarr; <input type="text" tabindex="'+id+'" name="options['+id+']" value="" /> <a href="#" rel="remove_'+type+'_'+id+'" class="remove_it">'+bpge.remove_it+'</a><br /></span>';
    }

    jQuery('select#extra-field-type').change(function(){
        var type = jQuery(this).val();
        var html = '';
        if ( type == 'checkbox' ||  type == 'radio' || type == 'select' ){
            html += '<label>' + bpge.enter_options + '</label>';
            html += new_option(type, 1);
            html += new_option(type, 2);
            jQuery('#extra-field-vars .content').html(html);
            jQuery('#extra-field-vars').css('display', 'block');
        }else{
            jQuery('#extra-field-vars').css('display', 'none');
            jQuery('#extra-field-vars .content').html('');
        }
    });

    jQuery('#extra-field-vars a.remove_it').live('click', function(e){
        e.preventDefault();
        var extra = jQuery(this).attr('rel').split('_');
        var action = extra[0];
        var type = extra[1];
        var id = extra[2];
        jQuery('#extra-field-vars span.'+type+'_'+id).remove();
    });

    jQuery('#extra-field-vars a#add_new').live('click', function(e){
        e.preventDefault();
        options_count += 1;
        var type = jQuery('select#extra-field-type').val();
        var option = new_option(type, options_count);
        jQuery('#extra-field-vars .content').append(option);
    });

    /**
     * Import Area
     */
    // Display selected Set description
    var desc = $('#box_import_set_fields select option:selected').attr('desc');
    $('#box_import_set_fields .import_desc').html(desc);
    // Change description on set change
    $('#box_import_set_fields select').change(function(){
        var desc = $('#box_import_set_fields select option:selected').attr('desc');
        $('#box_import_set_fields .import_desc').html(desc);
    });
    // Make the import
    $('#box_import_set_fields .import_set_fields').click(function(){
        $('#box_import_set_fields #approve_import').val(true);
        $('#group-settings-form').submit();
        return false;
    });

    $('#group-settings-form .box_field #save').click(function(){
        var field_name = $('#group-settings-form input[name="extra-field-title"]').val();

        if($.trim(field_name) == ''){
            $('#group-settings-form input[name="extra-field-title"]').css('border','2px inset red');
            return false;
        }else{
            $('#group-settings-form input[name="extra-field-title"]').css('border','1px inset #ccc');
        }
    });

    $('#group-settings-form .box_page #save').click(function(){
        var edit = false;
        var page_name = $('#group-settings-form input[name="extra-page-title"]').val();
        if($(this).attr('name') == 'save_pages_edit'){
            edit = true;
            var page_slug = $('#group-settings-form input[name="extra-page-slug"]').val();
        }
        var error = 0;

        if($.trim(page_name) == ''){
            $('#group-settings-form input[name="extra-page-title"]').css('border','2px inset red');
            error++;
        }else{
            $('#group-settings-form input[name="extra-page-title"]').css('border','1px inset #ccc');
        }

        if(edit && $.trim(page_slug) == ''){
            $('#group-settings-form input[name="extra-page-slug"]').css('border','2px inset red');
            error++;
        }else{
            $('#group-settings-form input[name="extra-page-slug"]').css('border','1px inset #ccc');
        }

        if(error != 0){
            return false;
        }
    });

});
