( function( $ ) {
	"use strict";

	/**
	 * Select a color from the palette.
	 */
	$.evf.delegate( ".ev-color-palette-variant", "click", "color", function() {
		var container = $( this ).parents( ".ev-color-inner-wrapper" ).first(),
			input = $( "input[name]", container );

		$( ".ev-selected", container ).removeClass( "ev-selected" );
		$( this ).addClass( "ev-selected" );
		input.val( $( this ).attr( "data-color" ) );

		return false;
	} );

	/**
	 * Adding the color component to the UI building queue.
	 */
	$.evf.ui.add( "input.ev-color-input", function() {
		$( this ).each( function() {
			$( this ).minicolors();
		} );
	} );

} )( jQuery );
