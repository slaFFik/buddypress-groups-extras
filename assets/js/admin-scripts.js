var ajaxurl = window.ajaxurl,
	bpge = window.bpge;

jQuery( document ).ready( function( $ ) {

	/**
	 * Dismiss Review link.
	 */
	$( '#bpge-admin .bpge_review_dismiss' ).on( 'click', function( e ) {
		e.preventDefault();
		var link = this;
		$.ajax(
			{
				url: ajaxurl,
				type: 'POST',
				data: {
					action: 'bpge',
					method: 'dismiss_review'
				}
			} )
			  .done( function( data ) {
				  if ( data === 'ok' ) {
					  $( link ).parent().parent().parent().fadeOut();
				  }
			  } );
	} );

	/**
	 * Allowed Groups checkboxes.
	 */
	$( '#bp-gtm-admin-table .bpge_allgroups' ).on( 'change', function() {
		var isChecked = $( this ).prop( 'checked' );

		if ( isChecked ) {
			$( '#bp-gtm-admin-table .bpge_groups' ).prop( 'checked', true );
			$( '#bp-gtm-admin-table .bpge_allgroups' ).prop( 'checked', true );
		}
		else {
			$( '#bp-gtm-admin-table .bpge_groups' ).prop( 'checked', false );
			$( '#bp-gtm-admin-table .bpge_allgroups' ).prop( 'checked', false );
		}
	} );

	$( '#bp-gtm-admin-table .bpge_groups' ).on( 'change', function() {
		$( '#bp-gtm-admin-table .bpge_allgroups' ).prop( 'checked', false );
	} );

	/**
	 * Sets of Fields.
	 */
	// Show fields under the set.
	$( '#bpge-admin .display_fields' ).on( 'click', function( e ) {
		e.preventDefault();
		const set_fields_id = $( this ).data( 'set_id' );

		$( '#bpge-admin #fields_' + set_fields_id ).slideToggle( 'fast' );

		let btn_add_field = $( '#bpge-admin .sets #set-' + set_fields_id + ' .add_field' );

		if ( btn_add_field.is( ':visible' ) ) {
			btn_add_field.css( 'display', 'none' );
			$( '#bpge-admin .sets #set-' + set_fields_id + ' .no-fields' ).css( 'display', 'none' );
		} else {
			btn_add_field.css( 'display', 'block' );
			$( '#bpge-admin .sets #set-' + set_fields_id + ' .no-fields' ).css( 'display', 'block' );
		}

		return false;
	} );

	/**
	 * Apply the set to groups using AJAX.
	 */
	$( '#bpge-admin .set_apply' ).on( 'click', function( e ) {
		e.preventDefault();

		const set_id = $( this ).data( 'set_id' );
		const set_name = $( '#set-' + set_id + ' .name' ).text();
		const link = $( this );

		if ( link.hasClass( 'applied' ) ) {
			return;
		}

		new Messi(
			bpge.apply_set_globally,
			{
				title: set_name,
				buttons: [
					{ id: 0, label: bpge.yes, val: 'Y' },
					{ id: 1, label: bpge.no, val: 'N' }
				],
				callback: function( val ) {
					if ( val === 'N' ) {
						return;
					}

					jQuery.ajax( {
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'bpge',
							method: 'apply_set',
							set_id: set_id
						},
						success: function( response ) {
							if ( response === 'success' ) {
								link.addClass( 'applied' )
									.text( bpge.applied );
								new Messi( bpge.success_apply_set, { title: bpge.success, titleClass: 'success', buttons: [ { id: 0, label: bpge.close, val: 'X' } ] } );
							}
							else {
								new Messi( bpge.error_apply_set, { title: bpge.error, titleClass: 'error', buttons: [ { id: 0, label: bpge.close, val: 'X' } ] } );
							}
						},
						error: function( response ) {
							new Messi( bpge.error_apply_set, { title: bpge.error, titleClass: 'error', buttons: [ { id: 0, label: bpge.close, val: 'X' } ] } );
						},
						complete: function() {
						}
					} );
				}
			}
		);
	} );

	/**
	 * Delete set of fields with all its fields.
	 */
	$( '#bpge-admin ul.sets a.field_delete' ).on( 'click', function( e ) {
		e.preventDefault();

		const set_id = $( this ).data( 'set_id' );
		const set_name = $( '#set-' + set_id + ' .name' ).text();

		new Messi(
			bpge.confirm_delete_set,
			{
				title: set_name,
				buttons: [
					{ id: 0, label: bpge.yes, val: 'Y' },
					{ id: 1, label: bpge.no, val: 'N' }
				],
				callback: function( val ) {
					if ( val === 'N' ) {
						return;
					}

					$.ajax( {
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'fields_set_delete',
							nonce: $( '#bpge_manage_sets_nonce' ).val(),
							id: set_id
						},
						success: function( response ) {
							if ( response === 'deleted' ) {
								$( 'ul.sets li#set-' + set_id ).fadeOut( 'fast', function() {
									$( this ).remove();
								} );
							}
						}
					} );
				}
			}
		);
	} );

	/**
	 * Edit set of fields.
	 */
	$( '#bpge-admin .field_edit' ).on( 'click', function() {
		const display_add  = $( '#box_add_set_fields' ).css( 'display' );
		const display_addf = $( '#box_add_field' ).css( 'display' );

		if ( display_add === 'block' ) {
			$( '#box_add_set_fields' ).css( 'display', 'none' );
			$( '#box_add_set_fields input[name="edit_set_fields_name"]' ).val( '' );
			$( '#box_add_set_fields textarea' ).html( '' );
		}

		if ( display_addf === 'block' ) {
			$( '#box_add_field' ).css( 'display', 'none' );
			$( '#box_add_field input[name="extra-field-title"]' ).val( '' );
			$( '#box_add_field textarea[name="extra-field-desc"]' ).val( '' );
			$( '#box_add_field select#extra-field-type option:first' ).attr( 'selected', 'selected' );
		}

		$( '#bpge-admin .fields' ).slideUp( 'fast' );
		const set_fields_id = $( this ).data( 'set_id' );
		const name_set_fields = $( '#set-' + set_fields_id + ' .name' ).html();
		const desc_set_fields = $( '#set-' + set_fields_id + ' .desc' ).html();

		$( '#box_edit_set_fields h4 span' ).html( name_set_fields );
		$( '#box_edit_set_fields input[name="edit_set_fields_name"]' ).val( name_set_fields );
		$( '#box_edit_set_fields textarea' ).html( desc_set_fields );
		$( '#box_edit_set_fields input[name="edit_set_fields_id"]' ).val( set_fields_id );
		$( '#box_edit_set_fields' ).css( 'display', 'block' );

		return false;
	} );

	/**
	 * Add set of fields.
	 */
	$( '#bpge-admin .add_set_fields' ).on( 'click', function() {
		var display_edit = $( '#box_edit_set_fields' ).css( 'display' );
		var display_addf = $( '#box_add_field' ).css( 'display' );

		if ( display_edit === 'block' ) {
			$( '#box_edit_set_fields' ).css( 'display', 'none' );
			$( '#box_edit_set_fields input[name="edit_set_fields_name"]' ).val( '' );
			$( '#box_edit_set_fields textarea' ).html( '' );
			$( '#box_edit_set_fields input[name="slug_set_fields"]' ).val( '' );
		}

		if ( display_addf === 'block' ) {
			$( '#box_add_field' ).css( 'display', 'none' );
			$( '#box_add_field input[name="extra-field-title"]' ).val( '' );
			$( '#box_add_field textarea[name="extra-field-desc"]' ).val( '' );
			$( '#box_add_field select#extra-field-type option:first' ).attr( 'selected', 'selected' );
		}

		$( '#bpge-admin .fields' ).slideUp( 'fast' );
		$( '#bpge-admin #box_add_set_fields' ).css( 'display', 'block' );
		return false;
	} );

	/*
	 * SECTION: ADD / EDIT FIELDS.
	 */
	// Add field for a set of fields.
	$( '#bpge-admin .add_field' ).on( 'click', function() {
		const display_add  = $( '#box_add_set_fields' ).css( 'display' );
		const display_edit = $( '#box_edit_set_fields' ).css( 'display' );

		if ( display_add === 'block' ) {
			$( '#box_add_set_fields' ).css( 'display', 'none' );
			$( '#box_add_set_fields input[name="edit_set_fields_name"]' ).val( '' );
			$( '#box_add_set_fields textarea' ).html( '' );
		}

		if ( display_edit === 'block' ) {
			$( '#box_edit_set_fields' ).css( 'display', 'none' );
			$( '#box_edit_set_fields input[name="edit_set_fields_name"]' ).val( '' );
			$( '#box_edit_set_fields textarea' ).html( '' );
			$( '#box_edit_set_fields input[name="slug_set_fields"]' ).val( '' );
		}

		const set_fields_id = $( this ).data( 'set_id' );
		const name_set_fields = $( '#set-' + set_fields_id + ' .name' ).html();

		$( '#box_add_field h4 span' ).html( name_set_fields );
		$( '#box_add_field input[name="sf_id_for_field"]' ).val( set_fields_id );
		$( '#extra-field-vars' ).css( 'display', 'none' );
		$( '#bpge-admin #box_add_field' ).css( 'display', 'block' );

		return false;
	} );

	let options_count = 2;

	function bpge_admin_field_new_option( type, id ) {
		return '<span class="' + type + '_' + id + '">' + bpge.option_text + ': ' +
			'<input type="text" tabindex="' + id + '" name="options[' + id + ']" value="" /> ' +
			'<a href="#" rel="remove_' + type + '_' + id + '" class="button-link-delete" onclick="bpge_admin_field_new_option(\'' + type + '\', ' + id + ')">' + bpge.remove_it + '</a>' +
			'<br /></span>';
	}

	$( 'select#extra-field-type' ).on( 'change', function() {
		const type = $( this ).val();
		let html = '';

		if ( type === 'checkbox' || type === 'radio' || type === 'select' ) {
			html += '<label>' + bpge.enter_options + '</label>';
			html += bpge_admin_field_new_option( type, 1 );
			html += bpge_admin_field_new_option( type, 2 );

			$( '#extra-field-vars .content' ).html( html );
			$( '#extra-field-vars' ).css( 'display', 'block' );
		}
		else {
			$( '#extra-field-vars .content' ).html( '' );
			$( '#extra-field-vars' ).css( 'display', 'none' );
		}
	} );

	$( '#extra-field-vars a#add_new' ).on( 'click', function( e ) {
		e.preventDefault();

		options_count += 1;
		const type = $( 'select#extra-field-type' ).val();
		const option = bpge_admin_field_new_option( type, options_count );

		$( '#extra-field-vars .content' ).append( option );
	} );
} );

function bpge_admin_field_new_option(field_type, id ) {
	jQuery( '#extra-field-vars .content span.' + field_type + '_' + id ).remove();
	return false;
}
