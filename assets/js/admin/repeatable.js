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
			duration: 300,
			// easing: "easeInOutCubic",
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
	$.evf.ui.add( ".ev-sortable .ev-container-repeatable-inner-wrapper", function() {
		var ev_sortable_dragged_height = null;

		/**
		 * Add padding to the page wrap in order to avoid flickering when starting
		 * to drag.
		 */
		var ev_repeatable_sortable_mousedown = function( origin ) {
			if ( ev_sortable_dragged_height !== null ) {
				return false;
			}

			var sortable = 0;

			if ( $( origin ).parents( ".ev-bundle-fields-wrapper" ).length ) {
				sortable = $( origin ).parents( ".ev-bundle-fields-wrapper" ).first();
			}
			else {
				sortable = $( origin ).parents( ".ev-field-inner" ).first();
			}

			ev_sortable_dragged_height = sortable.outerHeight();

			$.evSaveRichTextareas( sortable );

			$( "#wpbody" ).css( "padding-bottom", ev_sortable_dragged_height + 10 );

			return false;
		};

		/**
		 * Remove the padding to the page wrap.
		 */
		var ev_repeatable_sortable_mouseup = function() {
			ev_sortable_dragged_height = null;
			$( "#wpbody" ).css( "padding-bottom", "" );
		};

		$( document )
			.off( "mousedown.ev_sortable" )
			.off( "mouseup.ev_sortable" );

		$( document ).on( "mousedown.ev_sortable", ".ev-sortable-handle", function() {
			ev_repeatable_sortable_mousedown( $( this ) );
		} );

		$( document ).on( "mouseup.ev_sortable", ".ev-sortable-handle", function() {
			ev_repeatable_sortable_mouseup();
		} );

		$( this ).sortable( {
			handle: ".ev-sortable-handle",
			items: "> .ev-field-inner, .ev-bundle-fields-wrapper",
			tolerance: "pointer",
			distance: 10,
			start: function( e, ui ) {
				var css = {
					height: ev_sortable_dragged_height,
				};

				$( ".ui-sortable-placeholder" ).css( css );

				ev_repeatable_sortable_mouseup();
			},
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

				$( document ).trigger( "ev-repeatable-sortable-stop", [ $( ui.item ) ] );
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
			container 	= ctrl.parents( ".ev-container-repeatable-inner-wrapper" ).first(),
			empty_state = $( ".ev-empty-state", field );

		var update_count = function() {
			var current_count = parseInt( empty_state.attr( "data-count" ), 10 );

			current_count = current_count + 1;
			empty_state.attr( "data-count", current_count );

			return current_count;
		};

		var update_names = function( count, field ) {
			$( ".ev-field-inner", field ).each( function() {
				var control = $( ".ev-repeatable-controls", this ).first(),
					count = parseInt( empty_state.attr( "data-count" ), 10 );

				$( "[name]", html ).each( function() {
					$( this ).attr( "name", this.name.replaceLast( "[]", "[" + count + "]" ) );
				} );
			} );
		};

		var key = empty_state.attr( "data-key" ),
			tpl = $( "script[type='text/template'][data-template='" + key + "']" ),
			mode = ctrl.attr( "data-mode" );

		var insert = function( html, mode ) {
			var count = update_count();

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

			update_names( count, field );

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

			insert( html, mode );
		}

		return false;
	} );

} )( jQuery );