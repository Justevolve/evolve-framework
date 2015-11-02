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
	 * Adding the sortable component to the UI building queue.
	 */
	$.evf.ui.add( ".ev-sortable .ev-container, .ev-sortable .ev-bundle-fields-wrapper", function() {
		$( this ).sortable( {
			handle: ".ev-sortable-handle",
			items: "> .ev-field-inner, .ev-bundle-fields-wrapper",
			stop: function( e, ui ) {
				var sortable = $( ui.item ).parents( ".ev-sortable" ).first(),
					fields = $( "> .ev-field-inner, .ev-bundle-fields-wrapper", sortable );

				fields.each( function( index, field ) {
					$( "[name]", field ).each( function() {
						var name_attr = $( this ).attr( "name" ),
							reg = new RegExp( /\[\d+\]/g ),
							matches = name_attr.match( reg );

						if ( matches && matches.length ) {
							var last_match = matches[matches.length - 1];
							name_attr = name_attr.replaceLast( last_match, "[" + index + "]" );

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
		var current_master_field = $( this ).parents( ".ev-field" ).first(),
			container = $( ".ev-container, .ev-bundle-fields-wrapper", current_master_field ).first(),
			current_field = $( this ).parents( ".ev-field-inner, .ev-bundle-fields-wrapper" ).first();

		current_field.remove();

		if ( ! $( ".ev-field-inner", container ).length ) {
			if ( $( ".ev-empty-state", container ).length ) {
				container.addClass( "ev-container-empty" );
			}

			container.addClass( "ev-no-fields" );
		}

		return false;
	} );

	/**
	 * Remove all the added repeatable fields.
	 */
	$.evf.delegate( ".ev-repeat-remove-all", "click", "repeatable", function() {
		var current_master_field = $( this ).parents( ".ev-field" ).first(),
			container = $( ".ev-container", current_master_field ).first(),
			fields = $( ".ev-field-inner, .ev-bundle-fields-wrapper", container );

		fields.remove();

		if ( $( ".ev-empty-state", container ).length ) {
			container.addClass( "ev-container-empty" );
		}

		container.addClass( "ev-no-fields" );

		return false;
	} );

	/**
	 * When clicking on a repeatable control, load a field template and append
	 * it to the set of already created fields.
	 */
	$.evf.delegate( ".ev-field .ev-repeat", "click", "repeatable", function() {
		var nested_fields = $( this ).parents( ".ev-field" ),
			current_field = nested_fields.first(),
			current_control = $( ".ev-repeatable-controls", current_field ),
			current_count = parseInt( current_control.first().attr( "data-count" ), 10 ),
			container = current_control.first().parents( ".ev-container" ).first(),
			key = current_control.first().attr( "data-key" ),
			tpl = $( "script[type='text/template'][data-template='" + key + "']", current_control.first() );

		var sanitize_and_insert = function( html ) {
			nested_fields.each( function() {
				var control = $( ".ev-repeatable-controls", $( this ) ).first(),
					count = parseInt( control.attr( "data-count" ), 10 );

				if ( ! control.is( current_control ) ) {
					count = count - 1;
				}

				$( "[name]", html ).each( function() {
					$( this ).attr( "name", this.name.replaceLast( "[]", "[" + count + "]" ) );
				} );
			} );

			current_count = current_count + 1;
			current_control.attr( "data-count", current_count );

			var first_inner_field = $( ".ev-field-inner", container ).first();

			if ( current_field.hasClass( "ev-repeatable-prepend" ) && first_inner_field.length ) {
				html.insertBefore( first_inner_field );
			}
			else {
				html.insertBefore( current_control.last() );
			}

			if ( $( ".ev-empty-state", container ).length ) {
				container.removeClass( "ev-container-empty" );
			}

			container.removeClass( "ev-no-fields" );

			setTimeout( function() {
				$.evf.ui.build();

				ev_repeatable_maybe_scroll( html );
			}, 1 );
		};

		if ( $( this ).attr( "data-action" ) ) {
			window[$( this ).attr( "data-action" )]( tpl, sanitize_and_insert );
		}
		else {
			var html = $( $.evf.template( tpl, {} ) );
			sanitize_and_insert( html );
		}

		return false;
	} );

} )( jQuery );