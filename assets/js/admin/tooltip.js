( function( $ ) {

	var tooltip_container = "ev-tooltip-container",
		tooltip_selector = ".ev-tooltip",
		tooltip_attr = "title",
		arrow_size = 16;

	/**
	 * Destroy a tooltip.
	 */
	window.ev_tooltip_destroy = function( tooltip ) {
		var s = $( "body" ).get( 0 ).style,
			transitionSupport = "transition" in s || "WebkitTransition" in s || "MozTransition" in s || "msTransition" in s || "OTransition" in s;

		if ( transitionSupport ) {
			var event_string = "transitionend.ev webkitTransitionEnd.ev oTransitionEnd.ev MSTransitionEnd.ev";

			$( this ).one( event_string, function( e ) {
				tooltip.remove();
			} );
		}
		else {
			tooltip.remove();
		}

		tooltip.removeClass( "ev-tooltip-active" );
	};

	/**
	 * When hovering a tooltip market, show the related tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseover", "tooltip", function() {
		var $link = $( this ),
			link_title = $( this ).attr( "data-" + tooltip_attr ) || $( this ).attr( tooltip_attr );

		if ( link_title == "" ) {
			return false;
		}

		var $container = $( '<div class="' + tooltip_container + '"></div>' ).appendTo( "body" ),
			link_height = $link.outerHeight(),
			link_width = $link.outerWidth();

		$( this ).data( "ev-tooltip", $container );

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
			mode = "vertical",
			style = {};

		if ( $link.attr( "data-horizontal" ) ) {
			mode = "horizontal";
		}

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
	$.evf.delegate( tooltip_selector, "mouseout click", "tooltip", function() {
		var tooltip = $( this ).data( "ev-tooltip" );

		if ( tooltip ) {
			window.ev_tooltip_destroy( tooltip );
		}
	});
} )( jQuery );
