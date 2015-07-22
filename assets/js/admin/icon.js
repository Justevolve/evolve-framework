( function( $ ) {
	"use strict";

	/**
	 * Switch between the available icon sets.
	 */
	$.evf.delegate( ".ev-field-icon select", "change", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			font_set = $( this ).val();

		$( ".ev-icon-sets ul" ).removeClass( "ev-on" );
		$( ".ev-icon-sets ul.ev-icon-set-" + font_set ).addClass( "ev-on" );
	} );

	/**
	 * Remove the currently selected icon.
	 */
	$.evf.delegate( ".ev-icon-remove", "click", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field ),
			icons = $( ".ev-icon", field );

		icons.removeClass( "ev-found ev-selected" );
		selected_wrapper.addClass( "ev-empty" );

		$( "[data-prefix]", field ).val( "" );
		$( "[data-set]", field ).val( "" );
		$( "[data-icon]", field ).val( "" );

		$( "[data-preview]", field ).attr( "class", "" );
		$( "[data-preview]", field ).attr( "class", "ev-icon ev-component" );
	} );

	/**
	 * Search between available icons.
	 */
	$.evf.delegate( "input[data-icon-search]", "keydown", "icon", function( e ) {
		if ( e.which == 13 ) {
			return false;
		}
	} );

	$.evf.delegate( "input[data-icon-search]", "keyup", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			search = $( this ).val(),
			icons = $( ".ev-icon", field );

		if ( search != "" ) {
			$( ".ev-icon-sets", field ).addClass( "ev-searching" );
		}
		else {
			$( ".ev-icon-sets", field ).removeClass( "ev-searching" );
		}

		icons.removeClass( "ev-found" );
		icons = icons.filter( '[data-icon-stripped*="' + search + '"]' ).addClass( "ev-found" );

		if ( icons.length ) {
			var k = icons.length > 1 ? 2 : 1;
			$( ".ev-icon-search-results", field ).html( ev_icon_field[k].replace( "%s", icons.length ) );
		}
		else {
			$( ".ev-icon-search-results", field ).html( ev_icon_field[0] );
		}

	} );

	/**
	 * Select an icon.
	 */
	$.evf.delegate( ".ev-icon-sets li", "click", "icon", function() {
		var icon_i = $( this ).find( 'i' ),
			field = icon_i.parents( ".ev-field" ).first(),
			prefix = icon_i.attr( "data-prefix" ),
			icon = icon_i.attr( "data-icon-name" ),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field ),
			icons = $( ".ev-icon", field );

		icons.removeClass( "ev-found ev-selected" );
		icon_i.addClass( "ev-selected" );
		$( ".ev-icon-sets", field ).removeClass( "ev-searching" );
		selected_wrapper.removeClass( "ev-empty" );

		$( "[data-prefix]", field ).val( prefix );
		$( "[data-set]", field ).val( icon_i.attr( "data-set" ) );
		$( "[data-icon]", field ).val( icon );

		$( "[data-preview]", field ).attr( "class", "" );
		$( "[data-preview]", field ).attr( "class", "ev-icon ev-component " + prefix + " " + icon );
	} );

} )( jQuery );