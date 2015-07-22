( function( $ ) {

	var tooltip_container = "ev-tooltip",
		tooltip_selector = ".ev-tooltip",
		tooltip_attr = "title";

	tooltip_selector += "[" + tooltip_attr + "]";

	$( '<div id="' + tooltip_container + '"></div>' ).appendTo( "body" );

	/**
	 * When hovering a tooltip market, show the related tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseover", "tooltip", function() {
		var $link = $( this ),
			link_title = $( this ).attr( tooltip_attr );

		if ( link_title == "" ) {
			return false;
		}

		var $container = $( "#" + tooltip_container ),
			link_height = $link.outerHeight(),
			link_width = $link.outerWidth();

		$container
			.html( link_title )
			.css( {
				top       : 0,
				left      : 0
			} )
			.addClass( 'ev-tooltip-active' )
			.show();

		var livetip_height = $container.outerHeight(),
			livetip_width = $container.outerWidth();

		$container
			.css( {
			top       : $link.offset().top - ( livetip_height ),
			left      : $link.offset().left - ( livetip_width / 2 ) + ( link_width / 2 )
		} );
	});

	/**
	 * When moving away from a tooltip marker, hide the tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseout", "tooltip", function() {
		$( "#" + tooltip_container ).removeClass( 'ev-tooltip-active' );
	});
} )( jQuery );