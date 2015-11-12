( function( $ ) {
	"use strict";

	/**
	 * Adding the AJAX-variant of the multiple select component to the UI building queue.
	 */
	$.evf.ui.add( "select.ev-multiple-select-input-ajax", function() {
		$( this ).each( function() {
			var action = $( this ).attr( "data-action" ),
				value = $( this ).attr( "data-value-field" ),
				label = $( this ).attr( "data-label-field" ),
				search = $( this ).attr( "data-search-field" ),
				nonce = $( this ).attr( "data-nonce" ),
				max = $( this ).attr( "data-max" ) ? parseInt( $( this ).attr( "data-max" ), 10 ) : 1000;

			$( this ).selectize( {
				plugins: ['remove_button', 'drag_drop'],
				valueField: value,
				labelField: label,
				searchField: [ search ],
				dropdownParent: "body",
				create: false,
				maxItems: max,
				load: function( query, callback ) {
					if ( ! query.length ) {
						return callback();
					}

					$.ajax( {
						url: ajaxurl,
						type: 'POST',
						data: {
							action: action,
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
					}
				}
			} );
		} );
	} );

	/**
	 * Adding the multiple select component to the UI building queue.
	 */
	$.evf.ui.add( "input.ev-multiple-select-input", function() {
		$( this ).each( function() {
			var options = $.parseJSON( $( this ).attr( "data-options" ) ),
				max = $( this ).attr( "data-max" ) ? parseInt( $( this ).attr( "data-max" ), 10 ) : options.length;

			$( this ).selectize( {
				plugins: ['remove_button', 'drag_drop'],
				options: options,
				valueField: 'val',
				labelField: 'label',
				searchField: [ 'label' ],
				dropdownParent: "body",
				maxItems: max,
				create: false,
				render: {
					item: function( item, escape ) {
						var html = '<div>';

						if ( item.spec && item.spec !== "" ) {
							html += '<span>' + escape( item.spec ) + '</span>';
						}

						html += escape( item.label );
						html += '</div>';

						return html;
					},
					option: function( item, escape ) {
						var html = '<div>';

						if ( item.spec && item.spec !== "" ) {
							html += '<span>' + escape( item.spec ) + '</span>';
						}

						html += escape( item.label );
						html += '</div>';

						return html;
					}
				}
			} );
		} );
	} );

} )( jQuery );