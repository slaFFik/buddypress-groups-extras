jQuery(document).ready(function($){

    /*
     * Predefined fields navigation
     */
    // show fields in a set
    
    $('#bpge-admin-fields .display_fields').click(function(){
        var set_fields_id = $(this).attr('set_fields');
        $('#bpge-admin-fields #fields_'+set_fields_id).slideToggle('fast');
        $('#bpge-admin-fields #fields_'+set_fields_id+' li:last').css('list-style','none');
        return false;
    });
    
    // delete set of fields with all fields
    jQuery('ul.sets a.field_delete').click(function(e){
        e.preventDefault();
        var field_id = jQuery(this).parent().parent().attr('id').split('_');field_id = field_id[1];

        // @TODO : HERE WILL BE AJAX REQUEST TO DELETE THAT FIELD
        jQuery('ul.sets li#set_'+field_id).fadeOut('fast',function(){
            jQuery(this).remove();
        });
        
    });
    
    /*
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
    
    $('#bpge-admin-fields .add_set_fields').click(function(){
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
        $('#bpge-admin-fields .fields').slideUp('fast');
        $('#bpge-admin-fields #box_add_set_fields').css('display','block');
        return false;
    });
    
    $('#bpge-admin-fields .field_edit').click(function(){
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
        $('#bpge-admin-fields .fields').slideUp('fast');
        var set_fields_id = $(this).attr('set_fields');
        var name_set_fields = $('#' + set_fields_id + ' .name').html();
        var desc_set_fields = $('#' + set_fields_id + ' .desc').html();
        $('#box_edit_set_fields h4 span').html(name_set_fields);
        $('#box_edit_set_fields input[name="edit_set_fields_name"]').val(name_set_fields);
        $('#box_edit_set_fields textarea').html(desc_set_fields);
        $('#box_edit_set_fields input[name="slug_set_fields"]').val(set_fields_id);
        $('#box_edit_set_fields').css('display','block');
        return false;
    });
    
    /*
     * Delete set fields
     **/
    
    $('#bpge-admin-fields .field_delete').click(function(){
        var slug_set_fields = $(this).attr('set_fields');
        $('#'+slug_set_fields).hide('fast');
        $.post(ajaxurl,{action:'set_fields_delete',slug_set_fields:slug_set_fields},function(response){});
        return false;
    });
    
    /*
     * Add field
     **/
    
    $('#bpge-admin-fields .add_field').click(function(){
        var display_add = $('#box_add_set_fields').css('display');
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
        var set_fields_id = $(this).attr('set_fields');
        var name_set_fields = $('#' + set_fields_id + ' .name').html();
        $('#box_add_field h4 span').html(name_set_fields);
        $('#box_add_field input[name="slug_sf_for_field"]').val(set_fields_id);
        $('#extra-field-vars').css('display', 'none');
        $('#bpge-admin-fields #box_add_field').css('display','block');
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
		var extra = jQuery(this).attr('rel').split('_');
		var action = extra[0];
		var type = extra[1];
		var id = extra[2];
		jQuery('#extra-field-vars span.'+type+'_'+id).remove();
		console.log(action + id);
	});
	
	jQuery('#extra-field-vars a#add_new').live('click', function(e){
		e.preventDefault();
		options_count += 1;
		var type = jQuery('select#extra-field-type').val();
		var option = new_option(type, options_count);
		jQuery('#extra-field-vars .content').append(option);
	});

});
