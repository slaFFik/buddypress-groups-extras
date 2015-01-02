var ajaxurl = window.ajaxurl,
    bpge    = window.bpge;

jQuery(document).ready(function($){

    /**
     * Review Dismiss button
     */
    jQuery('#bpge-admin .bpge_review_dismiss').click(function(e){
        e.preventDefault();
        var link = this;
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bpge',
                method: 'dismiss_review'
            }
        })
        .done(function(data){
            if(data == 'ok'){
                jQuery(link).parent().parent().parent().fadeOut();
            }
        });

    });

    /**
     * Groups checkboxes
     */
    jQuery('#bp-gtm-admin-table .bpge_allgroups').change(function(){
        var status = jQuery(this).attr('checked');
        if(status == 'checked'){
            jQuery('#bp-gtm-admin-table .bpge_groups').attr('checked','checked');
            jQuery('#bp-gtm-admin-table .bpge_allgroups').attr('checked','checked');
        }else{
            jQuery('#bp-gtm-admin-table .bpge_groups').removeAttr('checked');
            jQuery('#bp-gtm-admin-table .bpge_allgroups').removeAttr('checked');
        }
    });

    jQuery('#bp-gtm-admin-table .bpge_groups').change(function(){
        jQuery('#bp-gtm-admin-table .bpge_allgroups').removeAttr('checked');
    });


    /*
     * Fields set
     **/
    // show fields under the set
    jQuery('#bpge-admin .display_fields').click(function(e){
        e.preventDefault();
        var set_fields_id = $(this).data('set_id');
        jQuery('#bpge-admin #fields_'+set_fields_id).slideToggle('fast');
        jQuery('#bpge-admin #fields_'+set_fields_id+' li:last').css('list-style','none');
        return false;
    });

    // apply the set to groups via ajax
    jQuery('#bpge-admin .set_apply').click(function(e){
        e.preventDefault();
        var set_id = $(this).data('set_id');
        var set_name = $('#set-'+set_id+' .name').text();
        var link = jQuery(this);

        if(link.hasClass('applied'))
           return;

        new Messi(bpge.apply_set, {
            title: set_name,
            buttons: [{id: 0, label: bpge.yes, val: 'Y'}, {id: 1, label: bpge.no, val: 'N'}],
            callback: function(val) {
                if(val == 'N')
                    return;

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bpge',
                        method: 'apply_set',
                        set_id: set_id
                    },
                    success: function(response){
                        if(response == 'success'){
                            link.addClass('applied')
                                .text(bpge.applied);
                            new Messi(bpge.success_apply_set, {title: bpge.success, titleClass: 'success', buttons: [{id: 0, label: bpge.close, val: 'X'}]});
                        }else{
                            new Messi(bpge.error_apply_set, {title: bpge.error, titleClass: 'error', buttons: [{id: 0, label: bpge.close, val: 'X'}]});
                        }
                    },
                    error: function(response){
                        new Messi(bpge.error_apply_set, {title: bpge.error, titleClass: 'error', buttons: [{id: 0, label: bpge.close, val: 'X'}]});
                    },
                    complete: function(){}
                });
            }
        });
    });

    // delete set of fields with all its fields
    jQuery('ul.sets a.field_delete').click(function(e){
        e.preventDefault();
        var field_id = jQuery(this).data('set_id');

        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'fields_set_delete',
                id: field_id
            },
            success: function(response){
                if(response == 'deleted'){
                    jQuery('ul.sets li#set-'+field_id).fadeOut('fast',function(){
                        jQuery(this).remove();
                    });
                }
            }
        });
    });

    // Edit set of fields
    $('#bpge-admin .field_edit').click(function(){
        var display_add = $('#box_add_set_fields').css('display');
        var display_addf = $('#box_add_field').css('display');
        if(display_add == 'block'){
            $('#box_add_set_fields').css('display','none');
            $('#box_add_set_fields input[name="edit_set_fields_name"]').val('');
            $('#box_add_set_fields textarea').html('');
        }
        if(display_addf == 'block'){
            $('#box_add_field').css('display','none');
            $('#box_add_field input[name="extra-field-title"]').val('');
            $('#box_add_field textarea[name="extra-field-desc"]').val('');
            $('#box_add_field select#extra-field-type option:first').attr('selected','selected');
        }
        $('#bpge-admin .fields').slideUp('fast');
        var set_fields_id = $(this).data('set_id');
        var name_set_fields = $('#set-' + set_fields_id + ' .name').html();
        var desc_set_fields = $('#set-' + set_fields_id + ' .desc').html();
        $('#box_edit_set_fields h4 span').html(name_set_fields);
        $('#box_edit_set_fields input[name="edit_set_fields_name"]').val(name_set_fields);
        $('#box_edit_set_fields textarea').html(desc_set_fields);
        $('#box_edit_set_fields input[name="edit_set_fields_id"]').val(set_fields_id);
        $('#box_edit_set_fields').css('display','block');
        return false;
    });

    // Add set of fields
    $('#bpge-admin .add_set_fields').click(function(){
        var display_edit = $('#box_edit_set_fields').css('display');
        var display_addf = $('#box_add_field').css('display');
        if(display_edit == 'block'){
            $('#box_edit_set_fields').css('display','none');
            $('#box_edit_set_fields input[name="edit_set_fields_name"]').val('');
            $('#box_edit_set_fields textarea').html('');
            $('#box_edit_set_fields input[name="slug_set_fields"]').val('');
        }
        if(display_addf == 'block'){
            $('#box_add_field').css('display','none');
            $('#box_add_field input[name="extra-field-title"]').val('');
            $('#box_add_field textarea[name="extra-field-desc"]').val('');
            $('#box_add_field select#extra-field-type option:first').attr('selected','selected');
        }
        $('#bpge-admin .fields').slideUp('fast');
        $('#bpge-admin #box_add_set_fields').css('display','block');
        return false;
    });

    // Add field for a set of fields
    $('#bpge-admin .add_field').click(function(){
        var display_add  = $('#box_add_set_fields').css('display');
        var display_edit = $('#box_edit_set_fields').css('display');
        if(display_add == 'block'){
            $('#box_add_set_fields').css('display','none');
            $('#box_add_set_fields input[name="edit_set_fields_name"]').val('');
            $('#box_add_set_fields textarea').html('');
        }
        if(display_edit == 'block'){
            $('#box_edit_set_fields').css('display','none');
            $('#box_edit_set_fields input[name="edit_set_fields_name"]').val('');
            $('#box_edit_set_fields textarea').html('');
            $('#box_edit_set_fields input[name="slug_set_fields"]').val('');
        }
        var set_fields_id = $(this).data('set_id');
        var name_set_fields = $('#set-' + set_fields_id + ' .name').html();
        $('#box_add_field h4 span').html(name_set_fields);
        $('#box_add_field input[name="sf_id_for_field"]').val(set_fields_id);
        $('#extra-field-vars').css('display', 'none');
        $('#bpge-admin #box_add_field').css('display','block');
        return false;
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
            jQuery('#extra-field-vars .content').html('');
            jQuery('#extra-field-vars').css('display', 'none');
        }
    });

    jQuery('#extra-field-vars a.remove_it').live('click', function(e){
        e.preventDefault();
        var extra  = jQuery(this).attr('rel').split('_');
        var action = extra[0];
        var type   = extra[1];
        var id     = extra[2];
        jQuery('#extra-field-vars span.'+type+'_'+id).remove();
    });

    jQuery('#extra-field-vars a#add_new').live('click', function(e){
        e.preventDefault();
        options_count += 1;
        var type   = jQuery('select#extra-field-type').val();
        var option = new_option(type, options_count);
        jQuery('#extra-field-vars .content').append(option);
    });

});
