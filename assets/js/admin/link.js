( function( $ ) {
	"use strict";

	/**
	 * Check empty status on link inputs.
	 */
	$( document ).on( "input.ev_link", ".ev-link-input-wrapper input", function() {
		if ( $( this ).val() !== "" ) {
			$( this ).parent().addClass( "ev-not-empty" );
		}
		else {
			$( this ).parent().removeClass( "ev-not-empty" );
		}
	} );

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
                "class": $( "[data-link-class]", ctrl_wrapper ).val(),
            };

        var modal = new $.evf.modal( key, data, {
        	simple: true,

        	close: function() {
        		$( window ).off( "keydown.ev_link" );
        	},
			save: function( data, after_save, nonce ) {
				$( "[data-link-url]", ctrl_wrapper ).val( data["url"] );
				$( "[data-link-target]", ctrl_wrapper ).val( data["target"] );
				$( "[data-link-rel]", ctrl_wrapper ).val( data["rel"] );
				$( "[data-link-title]", ctrl_wrapper ).val( data["title"] );
				$( "[data-link-class]", ctrl_wrapper ).val( data["class"] );

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

			if ( _data["rel"] || data["title"] || data["class"] ) {
				$( origin ).addClass( "ev-link-modal-expanded" );
			}

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
					}, 1 );
				}
			);
		} );

		return false;
	} );

	// $.evf.delegate( ".ev-link-trigger", "click", "link", function() {
	// 	$( '.ev-modal-container[data-key="ev-link"]').addClass( 'ev-link-modal-expanded' );

	// 	return false;
	// } );

	/**
	 * Check if a string represents a URL.
	 */
	function ev_is_url( s ) {
		var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;

		return regexp.test( s );
	}

	$.evf.ui.add( ".ev-link-input-wrapper input", function() {
		$( this ).trigger( "input.ev_link" );
	} );

	$.evf.ui.add( ".ev-link-url-wrapper [name='url']", function() {
		var nonce = $( this ).attr( "data-nonce" );

		$( this ).selectize( {
			plugins: [],
			valueField: "id",
			labelField: "text",
			searchField: [ "text" ],
			dropdownParent: "body",
			create: true,
			createOnBlur: true,
			maxItems: 1,
			load: function( query, callback ) {
				if ( ! query.length || ev_is_url( query ) ) {
					return callback();
				}

				$.ajax( {
					url: ajaxurl,
					type: 'POST',
					data: {
						action: "ev_link_search_entries",
						search: query,
						nonce: nonce
					},
					error: function() {
						callback();
					},
					success: function( res ) {
						callback( $.parseJSON( res ) );
					}
				} );
			},
			render: {
				item: function( item, escape ) {
					var html = '<div>';

					if ( item.spec && item.spec !== "" ) {
						html += '<span>' + escape( item.spec ) + '</span>';
					}

					html += escape( item.text );
					html += '</div>';

					return html;
				},
				option: function( item, escape ) {
					var html = '<div>';

					if ( item.spec && item.spec !== "" ) {
						html += '<span>' + escape( item.spec ) + '</span>';
					}

					html += escape( item.text );
					html += '</div>';

					return html;
				},
				option_create: function(data, escape) {
					return '<div class="ev-link-create create">' + ev_framework.link.create + '</div>';
				}
			}
		} );

		$( this ).get( 0 ).selectize.focus();
	} );

} )( jQuery );
