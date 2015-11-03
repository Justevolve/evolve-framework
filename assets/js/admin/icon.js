( function( $ ) {
	"use strict";

	/**
	 * Switch between the available icon sets.
	 */
	$.evf.delegate( ".ev-selected-icon-wrapper", "click", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first();

		var template = $( "script[type='text/template'][data-template='ev-icon-modal']" );

		if ( $( ".ev-icon-sets-external-wrapper" ).length ) {
			$( ".ev-icon-sets-external-wrapper" ).remove();
		}
		else {
			var wrapper = $( $.evf.template( template, {} ) );
			wrapper.data( "ev-field", field );

			var prefix = $( "[data-prefix]", field ).val(),
				set = $( "[data-set]", field ).val(),
				icon = $( "[data-icon]", field ).val();

			$( ".ev-selected", wrapper ).removeClass( "ev-selected" );

			if ( icon ) {
				$( "[data-set='" + set + "'][data-prefix='" + prefix + "'][data-icon-name='" + icon + "']", wrapper ).addClass( "ev-selected" );
			}

			$( "body" ).append( wrapper );
		}

		return false;
	} );

	/**
	 * Close the icon selection modal.
	 */
	$.evf.delegate( ".ev-close-icon-modal", "click", "icon", function() {
		$( ".ev-icon-sets-external-wrapper" ).remove();

		return false;
	} );

	/**
	 * Remove the currently selected icon.
	 */
	$.evf.delegate( ".ev-icon-remove", "click", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field );

		selected_wrapper.addClass( "ev-empty" );
		$( ".ev-icon-sets-external-wrapper" ).remove();

		$( "[data-prefix]", field ).val( "" );
		$( "[data-set]", field ).val( "" );
		$( "[data-icon]", field ).val( "" );

		$( "[data-preview]", field ).attr( "class", "" );
		$( "[data-preview]", field ).attr( "class", "ev-icon ev-component" );

		return false;
	} );

	/**
	 * Prevent hitting the Enter key when searching through icons.
	 */
	$.evf.delegate( "input[data-icon-search]", "keydown", "icon", function( e ) {
		if ( e.which == 13 ) {
			return false;
		}
	} );

	/**
	 * Search through available icons.
	 */
	$.evf.delegate( "input[data-icon-search]", "keyup", "icon", function() {
		var wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			search = $( this ).val(),
			icons = $( ".ev-icon", wrapper );

		if ( search != "" ) {
			$( ".ev-icon-sets", wrapper ).addClass( "ev-searching" );
		}
		else {
			$( ".ev-icon-sets", wrapper ).removeClass( "ev-searching" );
		}

		icons.removeClass( "ev-found" );
		icons = icons.filter( '[data-icon-stripped*="' + search + '"]' ).addClass( "ev-found" );
		$( ".ev-icon-search-results", wrapper ).addClass( "ev-search-icon-results-visible" );

		if ( icons.length ) {
			var k = icons.length > 1 ? 2 : 1;
			$( ".ev-icon-search-results", wrapper ).html( ev_icon_field[k].replace( "%s", icons.length ) );
		}
		else {
			if ( search != '' ) {
				$( ".ev-icon-search-results", wrapper ).html( ev_icon_field[0] );
			}
			else {
				$( ".ev-icon-search-results", wrapper ).removeClass( "ev-search-icon-results-visible" );
			}
		}

	} );

	/**
	 * Select an icon.
	 */
	$.evf.delegate( ".ev-icon-sets i", "click", "icon", function() {
		var icon_i = $( this ),
			prefix = icon_i.attr( "data-prefix" ),
			icon = icon_i.attr( "data-icon-name" ),
			wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			field = wrapper.data( "ev-field" ),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field ),
			icons = $( ".ev-icon", wrapper );

		icons.removeClass( "ev-found ev-selected" );
		icon_i.addClass( "ev-selected" );
		$( ".ev-icon-sets", wrapper ).removeClass( "ev-searching" );
		selected_wrapper.removeClass( "ev-empty" );

		$( "[data-prefix]", field ).val( prefix );
		$( "[data-set]", field ).val( icon_i.attr( "data-set" ) );
		$( "[data-icon]", field ).val( icon );

		$( "[data-preview]", field ).attr( "class", "" );
		$( "[data-preview]", field ).attr( "class", "ev-icon ev-component " + prefix + " " + icon );

		$( "input[data-icon-search]", wrapper ).val( "" );
		$( ".ev-icon-search-results", wrapper ).html( "" ).removeClass( "ev-search-icon-results-visible" );

		$( ".ev-icon-sets-external-wrapper" ).remove();
	} );

} )( jQuery );