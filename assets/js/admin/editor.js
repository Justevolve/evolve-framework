( function( $ ) {
	"use strict";

	/**
	 * Adding the rich textarea component to the UI building queue.
	 */
	window.evf_ui_rich_textareas = 0;

	$.evf.ui.add( "textarea.ev-rich", function() {
		$( this ).each( function() {
			window.evf_ui_rich_textareas++;

			var id = $( this ).attr( "id" );

			id = id.replace( /\[/g, "_" );
			id = id.replace( /\]/g, "_" );

			$( this ).attr( "id", id + "-" + window.evf_ui_rich_textareas );

			$( this ).wp_editor();
		} );
	} );

} )( jQuery );