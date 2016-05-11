( function( $ ) {

	var tooltip_container = "ev-tooltip-container",
		tooltip_container_selector = "." + tooltip_container,
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

			$( this ).on( event_string, function( e ) {
				tooltip.remove();
			} );
		}
		else {
			tooltip.remove();
		}

		tooltip.removeClass( "ev-tooltip-active" );
	};

	/**
	 * Destroy all tooltips;
	 */
	window.ev_seek_and_destroy_tooltips = function() {
		$( "." + tooltip_container ).remove();
	};

	/**
	 * Correctly position a tooltip depending on the viewport's boundaries.
	 */
	function _ev_position_tooltip( $link, $container, mode ) {
		var link_height = $link.outerHeight(),
			link_width = $link.outerWidth(),
			livetip_height = $container.outerHeight() + ( arrow_size / 2 ),
			livetip_width = $container.outerWidth(),
			style = {},
			offset_top = $link.offset().top,
			scroll = offset_top - $( window ).scrollTop();

		if ( $( "body" ).is( ".admin-bar" ) ) {
			scroll -= $( "#wpadminbar" ).outerHeight();
		}

		style.left = $link.offset().left - ( livetip_width / 2 ) + ( link_width / 2 );

		$container.removeClass( "ev-tooltip-expand-top ev-tooltip-expand-bottom ev-tooltip-expand-left ev-tooltip-expand-right ev-tooltip-vertical ev-tooltip-horizontal" );

		var mode_class = ! mode || mode == "vertical" ? "ev-tooltip-vertical" : "ev-tooltip-horizontal";

		/* Vertical expansion. */
		if ( livetip_height <= scroll ) {
			offset_top += 2;
			$container.addClass( "ev-tooltip-expand-top" );
			style.top = offset_top - livetip_height;
		}
		else {
			offset_top -= 2;
			$container.addClass( "ev-tooltip-expand-bottom" );
			style.top = offset_top + link_height + ( arrow_size / 2 );
		}

		/* Horizontal expansion. */

		$container
			.addClass( mode_class )
			.css( style );
	};

	/**
	 * Create a tooltip on a specific element.
	 */
	window.ev_create_tooltip = function( element, tooltip_content, config ) {
		var $link = $( element );

		if ( typeof tooltip_content === "undefined" || ! tooltip_content ) {
			tooltip_content = $link.attr( "data-" + tooltip_attr ) || $link.attr( tooltip_attr );

			if ( typeof tooltip_content === "undefined" || tooltip_content === "" ) {
				return false;
			}
		}

		/**
		 * Configuration.
		 */
		config = $.extend( true, {}, {
			'class': '',
			'mode': 'horizontal'
		}, config );

		ev_seek_and_destroy_tooltips();

		var tooltip_class = tooltip_container + ' ' + config.class,
			$container = $( '<div class="' + tooltip_class + '"></div>' ).appendTo( "body" );

		$link.data( "ev-tooltip", $container );

		$container
			.html( tooltip_content )
			.css( {
				top       : 0,
				left      : 0
			} );

		$( window ).on( "resize scroll", function() {
			_ev_position_tooltip( $link, $container, config.mode );
		} );

		_ev_position_tooltip( $link, $container, config.mode );

		$container.show();

		$container.addClass( 'ev-tooltip-active' );

		return $container;
	};

	/**
	 * When hovering a tooltip market, show the related tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseover", "tooltip", function() {
		ev_create_tooltip( $( this ) );
	} );

	/**
	 * When moving away from a tooltip marker, hide the tooltip.
	 */
	$.evf.delegate( tooltip_selector, "mouseout click", "tooltip", function() {
		var tooltip = $( this ).data( "ev-tooltip" );

		if ( tooltip ) {
			window.ev_tooltip_destroy( tooltip );
		}
	} );

	/**
	 * When clicking on a persistent tooltip, hide the tooltip.
	 */
	$.evf.delegate( tooltip_container_selector, "click", "tooltip", function() {
		window.ev_tooltip_destroy( $( this ) );
	} );
} )( jQuery );