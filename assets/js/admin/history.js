( function( $ ) {
	"use strict";

	$.evf.history = {};

	/**
	 * Add a URL to the browser history without reloading the page.
	 *
	 * @param  {String} url The URL string.
	 */
	$.evf.history.push = function( url ) {
		if ( history && history.pushState ) {
			history.pushState( null, null, url );
		}
	}

	/**
	 * Add a query string parameter to the browser history without reloading the
	 * page considering a particular element's "href" and "data-target"
	 * attributes.
	 *
	 * @param  {Object} element The element object.
	 * @param  {String} key The key value for the query string element to be appended or modified.
	 */
	$.evf.history.pushQueryString = function( element, key ) {
		element = $( element );

		var target = "",
			current_url = window.location.toString();

		if ( element.attr( "href" ) && element.attr( "href" ).indexOf( "#" ) === 0 ) {
			target = element.attr( "href" );
		}
		else if ( element.attr( "data-target" ) && element.attr( "data-target" ).indexOf( "#" ) === 0 ) {
			target = element.attr( "data-target" );
		}

		target = target.replace( "#", "" );

		if ( target !== "" ) {
			if ( window.location.search === "" ) {
				current_url += "?" + key + "=" + target;
			}
			else {
				var query_string = window.location.search.substring( 1 ),
					params = $.deparam( query_string );

				params[key] = target;

				current_url = current_url.replace( query_string, $.param( params ) );
			}

			$.evf.history.push( current_url );
		}
	};

	/**
	 * Add an hash to the browser history without reloading the page considering
	 * a particular element's "href" and "data-target" attributes.
	 *
	 * @param  {Object} element The element object.
	 */
	$.evf.history.pushHash = function( element ) {
		element = $( element );

		var target = "";

		if ( element.attr( "href" ) && element.attr( "href" ).indexOf("#") === 0 ) {
			target = element.attr( "href" );
		}
		else if ( element.attr( "data-target" ) && element.attr( "data-target" ).indexOf("#") === 0 ) {
			target = element.attr( "data-target" );
		}

		if ( target !== "" ) {
			$.evf.history.push( target );
		}
	};
} )( jQuery );