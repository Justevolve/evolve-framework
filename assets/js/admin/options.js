( function( $ ) {
	"use strict";

	/**
	 * Removes an appended admin message.
	 */
	$.evf.delegate( ".ev-close-persistent-message", "click", "messages", function() {
		$( this ).parents( '.ev-persistent-message' ).first().remove();

		return false;
	} );

	/**
	 * Handles the behavior of the save button in the admin pages options tabs.
	 *
	 * @param  {String} mode Switch the button "on" or "off".
	 * @param  {Object} response The AJAX response object.
	 * @return {Boolean}
	 */
	window.ev_save_options_tab_button = function( mode, response ) {
		// var parent = $( this ).parents( ".ev-btn" ).first(),
		// 	text = $( this ).text();

		// if ( mode === "on" ) {
		// 	if ( parent.hasClass( "ev-saving" ) || parent.hasClass( "ev-saved" ) ) {
		// 		return false;
		// 	}

		// 	parent.addClass( "ev-saving" );
		// }
		// else if ( mode === "off" && typeof response !== "undefined" ) {
		// 	parent.removeClass( "ev-saving" );

		// 	var response_heading = '';

		// 	if ( response.heading !== '' ) {
		// 		response_heading = '<h3>' + response.heading + '</h3>';
		// 	}

		// 	if ( response.type == 'success' ) {
		// 		parent.addClass( "ev-saved" );
		// 		parent.find( '.ev-btn-message' ).html( response.message );

		// 		if ( response.refresh ) {
		// 			window.location.href = window.location.href;
		// 		}

		// 		setTimeout( function() {
		// 			parent.removeClass( "ev-saved" );
		// 		}, 2000 );
		// 	} else {
		// 		$( '<div class="ev-persistent-message ev-' + response.type + '"><span class="ev-close-persistent-message"></span>' + response_heading + response.message + '</div>' )
		// 		.appendTo( '.ev-persistent-messages-container' );
		// 	}
		// }

		return false;
	};

	/**
	 * Triggers the saving action on the current tab.
	 *
	 * @param  {String} tab The current tab slug.
	 * @return {Boolean}
	 */
	window.ev_save_options_tab = function( tab ) {
		$.evSaveRichTextareas( tab );

		var form = $( "form", tab ).first(),
			action = $( ".ev-btn-type-save[data-callback]", form ).first(),
			data = form.serialize().replace( /%5B%5D/g, '[]' ),
			nonce = $( "#ev" ).val();

		data += "&action=" + action.attr( "data-callback" );
		data += "&nonce=" + nonce;

		ev_idle_button( action );

		$.post(
			form.attr( "action" ),
			data,
			function( response ) {
				ev_unidle_button( action, response );
			},
			'json'
		);

		return false;
	};

	/**
	 * Hooks to the submit event of admin pages forms in order to trigger their
	 * saving action.
	 */
	$.evf.on( ".ev-admin-page .ev-tab > form", "submit", "save-options-tab", function() {
		var tab = $( this ).parents( ".ev-tab" ).first();

		window.ev_save_options_tab( tab );

		return false;
	} );

	// $.evf.on( ".ev-tab .ev-field-ev_debug_button .ev-btn", "click", "asd", function() {
	// 	var btn = $( this ),
	// 		response = {
	// 			message: '',
	// 			type: 'success'
	// 		};

	// 	ev_idle_button( btn );

	// 	setTimeout( function() {
	// 		ev_unidle_button( btn, response );
	// 	}, 1000 );

	// 	return false;
	// } );

	/**
	 * Hooks to the click event of admin pages forms save buttons in order to
	 * trigger the tabs saving action.
	 */
	// $.evf.on( ".ev-admin-page .ev-tab > form .ev-btn-action[data-callback]", "click", "save-options-tab", function() {
	// 	var tab = $( this ).parents( ".ev-tab" ).first();

	// 	return window.ev_save_options_tab( tab );
	// } );

} )( jQuery );