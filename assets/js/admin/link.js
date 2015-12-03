( function( $ ) {
	"use strict";

	/**
	 * Click on a link control.
	 */
	$.evf.delegate( ".ev-link-ctrl-btn", "click", "link", function() {
        var key = "ev-link",
            ctrl = $( this ),
            ctrl_wrapper = $( this ).parents( '.ev-link-ctrl' ).first(),
            data = {
                "url": $( "[data-link-url]", ctrl_wrapper ).val(),
                "target": $( "[data-link-target]", ctrl_wrapper ).val(),
                "rel": $( "[data-link-rel]", ctrl_wrapper ).val(),
                "title": $( "[data-link-title]", ctrl_wrapper ).val(),
            };

        var modal = new $.evf.modal( key, data, {
        	class: 'ev-modal-container-simple',
        	close: function() {
        		$( window ).off( "keydown.ev_link" );
        	},
			save: function( data, after_save, nonce ) {
				$( "[data-link-url]", ctrl_wrapper ).val( data["url"] );
				$( "[data-link-target]", ctrl_wrapper ).val( data["target"] );
				$( "[data-link-rel]", ctrl_wrapper ).val( data["rel"] );
				$( "[data-link-title]", ctrl_wrapper ).val( data["title"] );

				ctrl.removeClass( "ev-link-on" );

				if ( data["url"] != "" ) {
					ctrl.addClass( "ev-link-on" );
				}
			}
		} );

        modal.open( function( content, key, _data ) {
			var modal_data = {
				"action": "ev_link_modal_load",
				"nonce": ctrl_wrapper.attr( "data-nonce" ),
				"data": _data
			};

			var origin = ".ev-modal-container[data-key='" + key + "']";
			$( origin + " .ev-modal-wrapper" ).addClass( "ev-loading" );

			$( window ).off( "keydown.ev_link" );
			$( window ).on( "keydown.ev_link", function( e ) {
				if ( e.which == 9 ) {
					$( '.ev-modal-container[data-key="ev-link"]' ).addClass( 'ev-link-modal-expanded' );
					$( window ).off( "keydown.ev_link" );

					return false;
				}
			} );

			$.post(
				ajaxurl,
				modal_data,
				function( response ) {
					response = $( response );

					$( origin + " .ev-modal-wrapper" ).removeClass( "ev-loading" );
					content.html( response );

					setTimeout( function() {
						$.evf.ui.build();

						$( "input[name=url]", content ).focus().select();
					}, 1 );
				}
			);
		} );

		return false;
	} );

	$.evf.delegate( ".ev-link-trigger", "click", "link", function() {
		$( '.ev-modal-container[data-key="ev-link"]').addClass( 'ev-link-modal-expanded' );

		return false;
	} );

} )( jQuery );
