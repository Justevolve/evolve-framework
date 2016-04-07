( function( $ ) {
	"use strict";

	/**
	 * Switch between the available icon sets.
	 */
	$.evf.delegate( ".ev-selected-icon-wrapper", "click", "icon", function() {
		var key = "ev-icon",
			ctrl = $( this ),
			field = ctrl.parents( ".ev-field" ).first(),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field ),
			data = {
				"prefix": $( "[data-prefix]", field ).val(),
				"set": $( "[data-set]", field ).val(),
				"icon": $( "[data-icon]", field ).val(),
				"color": $( "[data-color]", field ).val(),
				"size": $( "[data-size]", field ).val(),
			};

		var modal = new $.evf.modal( key, data, {
			simple: true,

			save: function( data, after_save, nonce ) {
				$( "[data-prefix]", field ).val( data["prefix"] );
				$( "[data-set]", field ).val( data["set"] );
				$( "[data-icon]", field ).val( data["icon"] );
				$( "[data-color]", field ).val( data["color"]["color"] );
				$( "[data-size]", field ).val( data["size"] );

				$( "[data-preview]", field )
					.attr( "class", "" )
					.css( "color", "" );

				$( "[data-preview]", field )
					.attr( "class", "ev-icon ev-component " + data["prefix"] + " " + data["icon"] )
					.css( "color", data["color"]["color"] );

				if ( data["icon"] ) {
					selected_wrapper.removeClass( "ev-empty" );
				}
				else {
					selected_wrapper.addClass( "ev-empty" );
				}
			}
		} );

		modal.open( function( content, key, _data ) {
			var modal_data = {
				"action": "ev_icon_modal_load",
				"data": _data,
				"nonce": ctrl.attr( "data-nonce" )
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

					$( "[data-icon-search]", content ).focus();

					setTimeout( function() {
						$.evf.ui.build();
					}, 1 );
				}
			);
		} );

		return false;
	} );

	/**
	 * Remove the currently selected icon.
	 */
	$.evf.delegate( ".ev-icon-remove", "click", "icon", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			selected_wrapper = $( ".ev-selected-icon-wrapper", field );

		selected_wrapper.addClass( "ev-empty" );

		$( "[data-prefix]", field ).val( "" );
		$( "[data-set]", field ).val( "" );
		$( "[data-icon]", field ).val( "" );
		$( "[data-color]", field ).val( "" );
		$( "[data-size]", field ).val( "" );

		$( "[data-preview]", field ).attr( "class", "ev-icon ev-component" )
			.css( "color", "" );

		return false;
	} );

	/**
	 * Prevent hitting the Enter key when searching through icons.
	 */
	$.evf.delegate( "input[data-icon-search]", "keydown", "icon", function( e ) {
		if ( e.which == 13 ) {
			return false;
		}
	} );

	/**
	 * Search through available icons.
	 */
	$.evf.delegate( "input[data-icon-search]", "keyup", "icon", function() {
		var wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			search = $( this ).val(),
			icons = $( ".ev-icon", wrapper );

		if ( search != "" ) {
			$( ".ev-icon-sets", wrapper ).addClass( "ev-searching" );
		}
		else {
			$( ".ev-icon-sets", wrapper ).removeClass( "ev-searching" );
		}

		icons.removeClass( "ev-found" );
		icons = icons.filter( '[data-icon-stripped*="' + search + '"]' ).addClass( "ev-found" );
		$( ".ev-icon-search-results", wrapper ).addClass( "ev-search-icon-results-visible" );

		if ( icons.length ) {
			var k = icons.length > 1 ? 2 : 1;
			$( ".ev-icon-search-results", wrapper ).html( ev_icon_field[k].replace( "%s", icons.length ) );
		}
		else {
			if ( search != '' ) {
				$( ".ev-icon-search-results", wrapper ).html( ev_icon_field[0] );
			}
			else {
				$( ".ev-icon-search-results", wrapper ).removeClass( "ev-search-icon-results-visible" );
			}
		}

	} );

	/**
	 * Select an icon.
	 */
	$.evf.delegate( ".ev-icon-sets i", "click", "icon", function() {
		var icon = $( this ),
			wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			icons = $( ".ev-icon", wrapper ),
			color = $( "[name='color[color]']", wrapper ).val(),
			size = $( "[data-icon-size]", wrapper ).val(),
			local_preview = $( ".ev-selected-icon-preview", wrapper );

		icons.removeClass( "ev-found ev-selected" );
		icon.addClass( "ev-selected" );
		$( ".ev-icon-sets", wrapper ).removeClass( "ev-searching" );

		$( "[data-icon-prefix]", wrapper ).val( icon.attr( "data-prefix" ) );
		$( "[data-icon-set]", wrapper ).val( icon.attr( "data-set" ) );
		$( "[data-icon-name]", wrapper ).val( icon.attr( "data-icon-name" ) );

		local_preview
			.removeAttr( "class" )
			.attr( "class", "ev-selected-icon-preview ev-icon ev-component " + icon.attr( "data-prefix" ) + " " + icon.attr( "data-icon-name" ) )
			.css( {
				"color": color,
				"font-size": size
			} );

		$( "input[data-icon-search]", wrapper ).val( "" );
		$( ".ev-icon-search-results", wrapper ).html( "" ).removeClass( "ev-search-icon-results-visible" );
	} );

	$.evf.delegate( ".ev-icon-sets-controls-field-wrapper [name='color[color]']", "change", "icon", function() {
		var wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			local_preview = $( ".ev-selected-icon-preview", wrapper );

		local_preview
			.css( {
				"color": $( this ).val()
			} );
	} );

	$.evf.delegate( ".ev-icon-sets-controls-field-wrapper [name='size']", "input", "icon", function() {
		var wrapper = $( this ).parents( ".ev-icon-sets-external-wrapper" ).first(),
			local_preview = $( ".ev-selected-icon-preview", wrapper );

		local_preview
			.css( {
				"font-size": $( this ).val()
			} );
	} );

} )( jQuery );