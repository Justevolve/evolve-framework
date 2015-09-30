( function( $ ) {

	var tooltip_container = "ev-tooltip",
		tooltip_selector = ".ev-tooltip",
		tooltip_attr = "title",
		arrow_size = 16;

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
			livetip_width = $container.outerWidth(),
			mode = "horizontal",
			style = {};

		if ( mode === "vertical" ) {
			style.left = $link.offset().left - ( livetip_width / 2 ) + ( link_width / 2 );

			livetip_height += ( arrow_size / 2 );

			if ( livetip_height <= $link.offset().top ) {
				$container.addClass( "ev-tooltip-expand-top" );
				style.top = $link.offset().top - livetip_height;
			}
			else {
				$container.addClass( "ev-tooltip-expand-bottom" );
				style.top = $link.offset().top + link_height + ( arrow_size / 2 );
			}
		}
		else {
			style.top = $link.offset().top - ( livetip_height / 2 ) + ( link_height / 2 );

			livetip_width += ( arrow_size / 2 );

			if ( $( window ).width() >= $link.offset().left + link_width + livetip_width ) {
				$container.addClass( "ev-tooltip-expand-right" );
				style.left = $link.offset().left + link_width + ( arrow_size / 2 );
			}
			else {
				$container.addClass( "ev-tooltip-expand-left" );
				style.left = $link.offset().left - livetip_width;
			}
		}

		$container
			.addClass( "ev-tooltip-" + mode )
			.css( style );
	});

	/**
	 * When moving away from a tooltip marker, hide the tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseout", "tooltip", function() {
		$( "#" + tooltip_container ).removeClass( 'ev-tooltip-active ev-tooltip-vertical ev-tooltip-horizontal ev-tooltip-expand-top ev-tooltip-expand-bottom ev-tooltip-expand-right ev-tooltip-expand-left' );
	});
} )( jQuery );