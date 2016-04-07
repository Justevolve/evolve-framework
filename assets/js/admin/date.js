( function( $ ) {
	"use strict";

	/**
	 * Adding the date component to the UI building queue.
	 */
	$.evf.ui.add( ".ev-date-input", function() {
		$( this ).each( function() {
			$( this ).datepicker( {
				dateFormat     : $( this ).attr( "data-format" ),
				dayNamesShort  : ev_date_field.dayNamesShort,
				dayNames       : ev_date_field.dayNames,
				monthNamesShort: ev_date_field.monthNamesShort,
				monthNames     : ev_date_field.monthNames,
				prevText       : ev_date_field.prevText,
				nextText       : ev_date_field.nextText,
				showAnim: ""
			} );
		} );
	} );

} )( jQuery );