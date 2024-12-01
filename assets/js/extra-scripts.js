jQuery( document ).ready( function( $ ) {

	/*
	 * SECTION: MANAGE FIELDS / PAGES.
	 */
	// Sorting fields.
	$( '#fields-sortable' ).sortable( {
		placeholder: 'highlight',
		update: function( event, ui ) {
			$.post(
				ajaxurl,
				{
					action: 'bpge',
					method: 'reorder_fields',
					_ajax_nonce: $( '#_wpnonce' ).val(),
					field_order: $( this ).sortable( 'serialize' ),
				},
				function( response ) {
				}
			);
		},
	} );
	$( '#fields-sortable' ).disableSelection();

	// Sorting pages.
	$( '#pages-sortable' ).sortable( {
		placeholder: 'highlight',
		update: function( event, ui ) {
			$.post(
				ajaxurl,
				{
					action: 'bpge',
					method: 'reorder_pages',
					_ajax_nonce: $( '#_wpnonce' ).val(),
					page_order: $( this ).sortable( 'serialize' ),
				},
				function( response ) {
				}
			);
		},
	} );
	$( '#pages-sortable' ).disableSelection();

	// Sorting nav.
	$( '#nav-sortable' ).sortable( {
		items: 'li:not(.ui-state-disabled)',
		placeholder: 'highlight',
		update: function( event, ui ) {
			$( 'input[name="bpge_group_nav_position"]' ).val(
				$( this ).sortable( 'serialize' )
			);
		},
	} );
	$( '#nav-sortable' ).disableSelection();

	// Delete field.
	$( '#fields-sortable li span a.delete_field' ).on( 'click', function( e ) {
		e.preventDefault();
		const field = $( this ).parent().parent().attr( 'id' ).split( '_' )[ 1 ];

		$.post(
			ajaxurl,
			{
				action: 'bpge',
				method: 'delete_field',
				_ajax_nonce: $( '#_wpnonce' ).val(),
				field: field,
			},
			function( response ) {
				if ( response === 'deleted' ) {
					$( '#fields-sortable li#position_' + field ).fadeOut( 'fast' );
				}
			}
		);
	} );

	// Delete page
	$( '#pages-sortable li span a.delete_page' ).on( 'click', function( e ) {
		e.preventDefault();
		const page = $( this ).parent().parent().attr( 'id' ).split( '_' )[ 1 ];

		$.post(
			ajaxurl,
			{
				action: 'bpge',
				method: 'delete_page',
				_ajax_nonce: $( '#_wpnonce' ).val(),
				page: page,
			},
			function( response ) {
				if ( response === 'deleted' ) {
					$( '#pages-sortable li#position_' + page ).fadeOut( 'fast' );
				}
			}
		);
	} );

	/*
	 * SECTION: ADD / EDIT FIELDS
	 */
	var options_count = 2;

	function new_option( type, id ) {
		return '<span class="' + type + '_' + id + '">' + bpge.option_text + ': <input type="text" tabindex="' + id + '" name="options[' + id + ']" value="" /> <a href="#" rel="remove_' + type + '_' + id + '" class="remove_it">' + bpge.remove_it + '</a><br /></span>';
	}

	$( 'select#extra-field-type' ).on( 'change', function() {
		var type = $( this ).val();
		var html = '';
		if ( type === 'checkbox' || type === 'radio' || type === 'select' ) {
			html += '<label>' + bpge.enter_options + '</label>';
			html += new_option( type, 1 );
			html += new_option( type, 2 );

			$( '#extra-field-vars .content' ).html( html );
			$( '#extra-field-vars' ).css( 'display', 'block' );
		}
		else {
			$( '#extra-field-vars' ).css( 'display', 'none' );
			$( '#extra-field-vars .content' ).html( '' );
		}
	} );

	$( document ).on( 'click', '#extra-field-vars a.remove_it', function( e ) {
		e.preventDefault();

		var extra = $( this ).attr( 'rel' ).split( '_' );
		var action = extra[ 0 ];
		var type = extra[ 1 ];
		var id = extra[ 2 ];

		$( '#extra-field-vars span.' + type + '_' + id ).remove();
	} );

	$( document ).on( 'click', '#extra-field-vars a#add_new', function( e ) {
		e.preventDefault();
		options_count += 1;
		const type = $( 'select#extra-field-type' ).val();
		const option = new_option( type, options_count );

		$( '#extra-field-vars .content' ).append( option );
	} );

	/**
	 * Import Area.
	 */
	// Display selected Set description.
	$( '#box_import_set_fields .import_desc' ).html(
		$( '#box_import_set_fields select option:selected' ).attr( 'desc' )
	);

	// Change description on set change.
	$( '#box_import_set_fields select' ).on( 'change', function() {
		$( '#box_import_set_fields .import_desc' ).html(
			$( '#box_import_set_fields select option:selected' ).attr( 'desc' )
		);
	} );
	// Do the import.
	$( '#box_import_set_fields .import_set_fields' ).on( 'click', function(e) {
		e.preventDefault();

		if ( ! confirm( bpge.apply_set_group ) ) {
			return false;
		}

		$( '#box_import_set_fields #approve_import' ).val( true );
		$( '#group-settings-form' ).trigger( 'submit' );

		return false;
	} );

	$( '#group-settings-form .box_field #save' ).on( 'click', function() {
		var field_name = $( '#group-settings-form input[name="extra-field-title"]' ).val();

		if ( $.trim( field_name ) === '' ) {
			$( '#group-settings-form input[name="extra-field-title"]' ).css( 'border', '2px inset red' );
			return false;
		}
		else {
			$( '#group-settings-form input[name="extra-field-title"]' ).css( 'border', '1px inset #ccc' );
		}
	} );

	$( '#group-settings-form .box_page #save' ).on( 'click', function() {
		let edit = false;
		const page_name = $( '#group-settings-form input[name="extra-page-title"]' ).val();
		let page_slug = '';

		if ( $( this ).attr( 'name' ) === 'save_pages_edit' ) {
			edit = true;
			page_slug = $( '#group-settings-form input[name="extra-page-slug"]' ).val();
		}

		var error = 0;

		if ( $.trim( page_name ) === '' ) {
			$( '#group-settings-form input[name="extra-page-title"]' ).css( 'border', '2px inset red' );
			error ++;
		}
		else {
			$( '#group-settings-form input[name="extra-page-title"]' ).css( 'border', '1px inset #ccc' );
		}

		if ( edit && $.trim( page_slug ) === '' ) {
			$( '#group-settings-form input[name="extra-page-slug"]' ).css( 'border', '2px inset red' );
			error ++;
		}
		else {
			$( '#group-settings-form input[name="extra-page-slug"]' ).css( 'border', '1px inset #ccc' );
		}

		if ( error !== 0 ) {
			return false;
		}
	} );
} );
