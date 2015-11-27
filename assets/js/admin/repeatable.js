( function( $ ) {
	"use strict";

	/**
	 * Slide to include a particular element in the viewport.
	 */
	function ev_repeatable_maybe_scroll( element ) {
		element = $( element ).get( 0 );

		var rect = element.getBoundingClientRect(),
			in_viewport =
				rect.top >= 0 &&
				rect.left >= 0 &&
				rect.bottom <= ( window.innerHeight || document.documentElement.clientHeight ) &&
				rect.right <= ( window.innerWidth || document.documentElement.clientWidth );

		if ( in_viewport ) {
			return;
		}

		$( element ).scrollintoview( {
			duration: 400,
			easing: "easeInOutCubic",
			direction: "vertical",
			offset: 40
		} );
	}

	/**
	 * Replace a string at a given position.
	 */
	function ev_repeatable_replace_at( string, index, character, how_many ) {
		return string.substr( 0, index ) + character + string.substr( index + how_many );
	}

	/**
	 * Adding the sortable component to the UI building queue.
	 */
	$.evf.ui.add( ".ev-sortable .ev-container, .ev-sortable .ev-bundle-fields-wrapper", function() {
		$( this ).sortable( {
			handle: ".ev-sortable-handle",
			items: "> .ev-field-inner, .ev-bundle-fields-wrapper",
			stop: function( e, ui ) {
				var sortable = $( ui.item ).parents( ".ev-sortable" ).first(),
					fields = $( "> .ev-field-inner, .ev-bundle-fields-wrapper", sortable ),
					depth = $( ui.item ).parents( ".ev-repeatable" ).length;

				fields.each( function( index, field ) {
					$( "[name]", field ).each( function() {
						var name_attr = $( this ).attr( "name" ),
							reg = new RegExp( /\[\d+\]/g ),
							matches = name_attr.match( reg ),
							i = 0;

						if ( matches && matches.length ) {
							for ( var j=0; j<matches.length; j++ ) {
								matches[j] = j === depth - 1 ? "[" + index + "]" : matches[j];
							}

							var match = null;

							while ( ( match = reg.exec( name_attr ) ) !== null ) {
								name_attr = ev_repeatable_replace_at( name_attr, match.index, matches[i], matches[i].length );
								i++;
							}

							$( this ).attr( "name", name_attr );
						}
					} );
				} );
			}
		} );
	} );

	/**
	 * When clicking on a repeatable remove button, remove its parent field.
	 */
	$.evf.delegate( ".ev-repeatable-remove", "click", "repeatable", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			container = $( ".ev-container, .ev-bundle-fields-wrapper", field ).first(),
			current_field = $( this ).parents( ".ev-field-inner, .ev-bundle-fields-wrapper" ).first();

		current_field.remove();

		if ( ! $( ".ev-field-inner", container ).length ) {
			field.addClass( "ev-no-fields" );
		}

		return false;
	} );

	/**
	 * Remove all the added repeatable fields.
	 */
	$.evf.delegate( ".ev-repeat-remove-all", "click", "repeatable", function() {
		var field = $( this ).parents( ".ev-field" ).first(),
			container = $( ".ev-container", field ).first(),
			fields = $( ".ev-field-inner, .ev-bundle-fields-wrapper", container );

		fields.remove();

		field.addClass( "ev-no-fields" );

		return false;
	} );

	/**
	 * When clicking on a repeatable control, load a field template and append
	 * it to the set of already created fields.
	 */
	$.evf.delegate( ".ev-field.ev-repeatable .ev-repeat", "click", "repeatable", function() {
		var ctrl 		= $( this ),
			field 		= ctrl.parents( ".ev-field.ev-repeatable" ).first(),
			inner 		= ctrl.parents( ".ev-field-inner, .ev-bundle-fields-wrapper" ).first(),
			container 	= ctrl.parents( ".ev-container" ).first(),
			empty_state = $( ".ev-empty-state", field );

		var update_count = function() {
			var current_count = parseInt( empty_state.attr( "data-count" ), 10 );

			$( ".ev-field", field ).each( function() {
				var control = $( ".ev-repeatable-controls", this ).first(),
					count = parseInt( empty_state.attr( "data-count" ), 10 );

				$( "[name]", html ).each( function() {
					$( this ).attr( "name", this.name.replaceLast( "[]", "[" + count + "]" ) );
				} );
			} );

			current_count = current_count + 1;
			empty_state.attr( "data-count", current_count );
		};

		var key = empty_state.attr( "data-key" ),
			tpl = $( "script[type='text/template'][data-template='" + key + "']" ),
			mode = ctrl.attr( "data-mode" );

		var insert = function( html, mode ) {
			update_count();

			if ( mode ) {
				if ( mode === "append" ) {
					html.insertAfter( inner );
				}
				else if ( mode === "prepend" ) {
					html.insertBefore( inner );
				}
			}
			else {
				html.appendTo( container );
			}

			field.removeClass( "ev-no-fields" );

			setTimeout( function() {
				$.evf.ui.build();

				ev_repeatable_maybe_scroll( html );
			}, 1 );
		};

		if ( ctrl.attr( "data-action" ) ) {
			window[ctrl.attr( "data-action" )]( tpl, insert );
		}
		else {
			var html = $( $.evf.template( tpl, {} ) );

			insert( html );
		}

		return false;
	} );

} )( jQuery );