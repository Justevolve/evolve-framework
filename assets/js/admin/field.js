( function( $ ) {
	"use strict";

	function ev_handle_slave_field_display( field ) {
		var ctrl_key = field.attr( "data-slave" ),
			ctrl = $( "[data-controller='" + ctrl_key + "']" ),
			ctrl_value = $( ":input", ctrl ).first().val(),
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
			$( ":input", field ).first().trigger( "change" );
		}
	}

	$.evf.ui.add( "[data-slave]", function() {
		$( this ).each( function() {
			ev_handle_slave_field_display( $( this ) );
		} );
	} );

	$.evf.delegate( "[data-controller] :input", "change", "field", function() {
		var controller_value = $( this ).val(),
			controller = $( this ).parents( "[data-controller]" ).first(),
			controller_key = controller.attr( "data-controller" );

		$( "[data-slave='" + controller_key + "']" ).each( function() {
			ev_handle_slave_field_display( $( this ) );
		} );
	} );
} )( jQuery );