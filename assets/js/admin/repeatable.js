( function( $ ) {
	"use strict";

	/**
	 * Adding the sortable component to the UI building queue.
	 */
	$.evf.ui.add( ".ev-sortable .ev-container, .ev-sortable .ev-bundle-fields-wrapper", function() {
		$( this ).sortable( {
			placeholder: "ev-sortable-placeholder",
			handle: ".ev-sortable-handle",
			items: "> .ev-field-inner, .ev-bundle-fields-wrapper"
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

		if ( ! $( ".ev-field-inner", container ).length && $( ".ev-empty-state", container ).length ) {
			container.addClass( "ev-container-empty" );
		}

		return false;
	} );

	/**
	 * When clicking on a repeatable control, load a field template and append
	 * it to the set of already created fields.
	 */
	$.evf.delegate( ".ev-field .ev-repeat", "click", "repeatable", function() {
		var nested_fields = $( this ).parents( ".ev-field" ),
			current_field = nested_fields.first(),
			current_control = $( ".ev-repeatable-controls", current_field ).first(),
			current_count = parseInt( current_control.attr( "data-count" ), 10 ),
			container = current_control.parents( ".ev-container" ).first(),
			key = current_control.attr( "data-key" ),
			tpl = $( "script[type='text/template'][data-template='" + key + "']", current_control );

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
				html.appendTo( container );
			}

			if ( $( ".ev-empty-state", container ).length ) {
				container.removeClass( "ev-container-empty" );
			}

			setTimeout( function() {
				$.evf.ui.build();
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