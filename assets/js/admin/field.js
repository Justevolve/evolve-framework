( function( $ ) {
	"use strict";

	/**
	 * Get the input field of a controller.
	 */
	function ev_controller_field_get( field ) {
		var type = field.attr( "data-type" );

		switch ( type ) {
			case 'select':
				return $( "select[name]", field ).first();
				break;
			case 'checkbox':
				return $( "input[type='checkbox'][name]", field ).first();
				break;
			default:
				break;
		}

		return false;
	}

	/**
	 * Handle a slave field display.
	 */
	function ev_handle_slave_field_display( field ) {
		var ctrl_key = field.attr( "data-slave" ),
			ctrl = $( "[data-controller='" + ctrl_key + "']" ),
			ctrl_field = ev_controller_field_get( ctrl );

		if ( ! ctrl_field ) {
			return;
		}

		var ctrl_value = ctrl_field.val(),
			expected_value = field.attr( "data-controller-value" );

		if ( expected_value != ctrl_value || ctrl.hasClass( "ev-hidden" ) ) {
			field.addClass( "ev-hidden" );
			$( "[data-slave='" + field.attr( "data-controller" ) + "']" ).addClass( "ev-hidden" );
		}
		else {
			field.removeClass( "ev-hidden" );
			$( "[data-slave='" + field.attr( "data-controller" ) + "']" ).removeClass( "ev-hidden" );
		}

		if ( field.is( "[data-controller]" ) ) {
			ev_controller_field_get( field ).trigger( "change" );
		}
	}

	/**
	 * Building the UI for hidden fields.
	 */
	$.evf.ui.add( "[data-slave]", function() {
		$( this ).each( function() {
			ev_handle_slave_field_display( $( this ) );
		} );
	} );

	/**
	 * Handle the change event of controller fields.
	 */
	$.evf.delegate( "[data-controller] :input", "change", "field", function() {
		var field = $( this ).parents( ".ev-field" ).first();

		if ( $( this ).is( ev_controller_field_get( field ) ) ) {
			var controller_value = $( this ).val(),
				controller = $( this ).parents( "[data-controller]" ).first(),
				controller_key = controller.attr( "data-controller" );

			$( "[data-slave='" + controller_key + "']" ).each( function() {
				ev_handle_slave_field_display( $( this ) );
			} );
		}
	} );
} )( jQuery );