( function( $ ) {
	"use strict";

	/**
	 * Adding the multiple select component to the UI building queue.
	 */
	$.evf.ui.add( "input.ev-multiple-select-input", function() {
		$( this ).each( function() {
			var options = $.parseJSON( $( this ).attr( "data-options" ) );

			$( this ).selectize( {
				plugins: ['remove_button', 'drag_drop'],
				options: options,
				valueField: 'val',
				labelField: 'label',
				searchField: [ 'label' ],
				dropdownParent: "body",
				maxItems: options.length + 1,
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