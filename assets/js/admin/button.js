( function( $ ) {
	"use strict";

	var idle_class = "ev-btn-idle";

	/**
	 * Set a button to idle.
	 */
	window.ev_idle_button = function( btn ) {
		$( btn ).addClass( idle_class );
		$( btn ).trigger( "start.ev_button" );
	}

	/**
	 * Unidle a button.
	 */
	window.ev_unidle_button = function( btn ) {
		$( btn ).removeClass( idle_class );
		$( btn ).trigger( "done.ev_button" );
	}

	/**
	 * When clicking a button with an AJAX action attached to it, set it to idle.
	 */
	// $.evf.delegate( ".ev-btn[data-callback]", "click", "ev_button", function() {
	// 	ev_idle_button( this );
	// } );

	/**
	 * After executing the AJAX action attached to a button, unidle it.
	 */
	// $( document ).on( "done.ev_button", ".ev-btn[data-callback]", function() {
	// 	ev_unidle_button( this );
	// } );

} )( jQuery );