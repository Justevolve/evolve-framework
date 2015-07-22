( function( $ ) {
	"use strict";

	/**
	 * Adding the color component to the UI building queue.
	 */
	$.evf.ui.add( "input.ev-color-input", function() {
		$( this ).each( function() {
			$( this ).minicolors();
		} );
	} );

} )( jQuery );