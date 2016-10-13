( function( $ ) {
	"use strict";

	/**
	 * RGB(a) to Hex.
	 */
	function _rgb2hex( rgb ){
		rgb = rgb.match(/^rgba?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i);

		return (rgb && rgb.length === 4) ? "#" +
			("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
			("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : '';
	}

	/**
	 * RGB(a) to Hex.
	 */
	function _rgb2opacity( rgb ){
		rgb = rgb.replace( "rgba(", "" );
		rgb = rgb.replace( ")", "" );
		rgb = rgb.replace( " ", "" );
		rgb = rgb.split( "," );

		return rgb[rgb.length-1];
	}

	/**
	 * Select a color from the palette.
	 */
	$.evf.delegate( ".ev-color-preset", "click", "color", function() {
		var wrapper = $( this ).parents( ".ev-color-presets-manager-wrapper" ).first(),
			hex_input = $( "[data-hex-value-input]", wrapper );

		if ( $( this ).hasClass( "ev-selected" ) ) {
			$( this ).removeClass( "ev-selected" );

			hex_input.val( "" );
		}
		else {
			$( ".ev-selected", wrapper ).removeClass( "ev-selected" );
			$( this ).addClass( "ev-selected" );

			hex_input.val( $( this ).attr( "data-hex" ) );
		}

		return false;
	} );

	/**
	 * Delete a color preset.
	 */
	$.evf.delegate( "[data-color-delete-preset]", "click", "color", function() {
		var ctrl = $( this ),
			preview = $( this ).parents( ".ev-color-preset" ).first(),
			id = preview.attr( "data-id" ),
			wrapper = $( this ).parents( ".ev-color-user-presets" ).first(),
			outer_wrapper = $( this ).parents( ".ev-color-presets-wrapper" ).first();

		if ( id ) {
			preview.remove();
			window.ev_seek_and_destroy_tooltips();

			if ( ! $( ".ev-color-preset", outer_wrapper ).length ) {
				$( "body" ).removeClass( "ev-has-color-presets" );
			}

			if ( ! $( ".ev-color-preset", wrapper ).length ) {
				wrapper.removeClass( "ev-color-has-user-presets" );
			}
			else {
				wrapper.addClass( "ev-color-has-user-presets" );
			}

			ev_framework.color.presets = _.without(
				ev_framework.color.presets,
				_.findWhere( ev_framework.color.presets, { user: true, id: id } )
			);

			$.post(
				ajaxurl,
				{
					action: "ev_color_delete_preset",
					nonce: ctrl.attr( "data-nonce" ),
					id: id,
				},
				function( response ) {
				}
			);
		}

		return false;
	} );

	/**
	 * Save a color preset.
	 */
	$.evf.delegate( "[data-color-save-preset]", "click", "color", function() {
		var ctrl = $( this ),
			wrapper = ctrl.parents( ".ev-color-wrapper" ).first(),
			input = $( ".ev-color-input", wrapper ),
			hex = input.val();

		if ( hex ) {
			var preset_name = prompt( ev_framework.color.new_preset_name );

			if ( ! preset_name ) {
				return false;
			}

			ev_idle_button( ctrl );

			$.post(
				ajaxurl,
				{
					action: "ev_color_save_preset",
					nonce: ctrl.attr( "data-nonce" ),
					hex: hex,
					name: preset_name,
					id: ev_framework.color.presets.length + 1
				},
				function( response ) {
					if ( ! ev_framework.color.presets.user ) {
						ev_framework.color.presets.user = [];
					}

					ev_framework.color.presets.user.push( {
						user: true,
						hex: hex,
						label: preset_name
					} );

					$( "body" ).addClass( "ev-has-color-presets" );

					ev_unidle_button( ctrl );
				}
			);
		}

		return false;
	} );

	/**
	 * Display the color presets selection modal.
	 */
	$.evf.delegate( "[data-color-presets]", "click", "color", function() {
		var key = "ev-color-presets",
			ctrl = $( this ),
			wrapper = ctrl.parents( ".ev-color-wrapper" ).first(),
			input = $( ".ev-color-input", wrapper ),
			opacity = input.attr( "data-opacity" ),
			opacity_input = $( "[data-input-color-opacity]", wrapper ),
			data = {
				"hex": input.val()
			};

		var modal = new $.evf.modal( key, data, {
			simple: true,

			save: function( data, after_save, nonce ) {
				if ( data["hex"] ) {
					if ( opacity ) {
						if ( data["hex"].indexOf( "#" ) === -1 ) {
							opacity_input.val( _rgb2opacity( data["hex"] ) );
						}
						else {
							opacity_input.val( "1" );
						}

						input.attr( "data-opacity", opacity_input.val() );
					}

					if ( ! opacity && data["hex"].indexOf( "rgba(" ) !== -1 ) {
						data["hex"] = _rgb2hex( data["hex"] );
					}

					input
						.val( data["hex"] )
						.trigger( "keyup" );
				}
			}
		} );

		modal.open( function( content, key, _data ) {
			var modal_data = {
				"action": "ev_color_presets_modal_load",
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
					}, 1 );
				}
			);
		} );

		return false;
	} );

	/**
	 * Adding the color component to the UI building queue.
	 */
	$.evf.ui.add( "input.ev-color-input", function() {
		$( this ).each( function() {
			var input = $( this ),
				wrapper = input.parents( ".ev-color-wrapper" ).first(),
				opacity = input.attr( "data-opacity" ),
				options = {
					control: "wheel",
					change: function( value, op ) {
						input.css( "border-color", value );

						if ( opacity !== undefined ) {
							$( "[data-input-color-opacity]", wrapper ).val( op );
						}

						if ( value !== '' ) {
							wrapper.addClass( "ev-color-can-be-saved" );
						}
						else {
							wrapper.removeClass( "ev-color-can-be-saved" );
						}
					}
				};

			if ( opacity !== undefined ) {
				options.opacity = true;
				options.format = "rgb";
			}

			$( this )
				.minicolors( options )
				.trigger( "change" );
		} );
	} );

} )( jQuery );
