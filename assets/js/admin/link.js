( function( $ ) {
	"use strict";

	/**
	 * Click on a link control.
	 */
	$.evf.delegate( ".ev-link-ctrl", "click", "link", function() {
        var key = "ev-link",
            ctrl = $( this ),
            data = {
                "url": $( "[data-link-url]", ctrl ).val(),
                "target": $( "[data-link-target]", ctrl ).val(),
                "rel": $( "[data-link-rel]", ctrl ).val(),
                "title": $( "[data-link-title]", ctrl ).val(),
            };

        var modal = new $.evf.modal( key, data, {
			save: function( data, after_save, nonce ) {
				$( "[data-link-url]", ctrl ).val( data["url"] );
				$( "[data-link-target]", ctrl ).val( data["target"] );
				$( "[data-link-rel]", ctrl ).val( data["rel"] );
				$( "[data-link-title]", ctrl ).val( data["title"] );

				ctrl.removeClass( "ev-link-on" );

				if ( data["url"] != "" ) {
					ctrl.addClass( "ev-link-on" );
				}
			}
		} );

        modal.open( function( content, key, _data ) {
			var modal_data = {
				"action": "ev_link_modal_load",
				"nonce": ctrl.attr( "data-nonce" ),
				"data": _data
			};

			var origin = ".ev-modal-container[data-key='" + key + "']";
			$( origin + " .ev-modal-wrapper" ).addClass( "ev-loading" );

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

} )( jQuery );
