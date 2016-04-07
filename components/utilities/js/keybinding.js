( function( $ ) {
	"use strict";

	/**
	 * Binds a keydown event based on a subset of allowed keys.
	 *
	 * @param {String} key The key literal name.
	 * @param {Function} callback The event callback function.
	 * @param {Object} config The configuration object.
	 */
	$.evf.key = function( key, callback, config ) {
		if ( typeof callback !== "function" ) {
			throw new Error( "Callback is not a function." );
		}

		config = $.extend( {
			/* Stop event propagation. */
			stop: true,

			/* Custom component namespace. */
			namespace: "ev",

			/* Set to true to execute one time only. */
			one: false
		}, config );

		var map = {
			"enter": 13,
			"left": 37,
			"up": 38,
			"right": 39,
			"down": 40,
			"esc": 27,
			"space": 32,
			"backspace": 8,
			"tab": 9
		};

		var namespace = "ev";

		if ( config.namespace ) {
			namespace = "." + config.namespace;
		}

		var _keydown = function( e ) {
			if( map[key] && e.which === map[key] ) {
				var ret = callback( e );

				if ( typeof ret !== "undefined" ) {
					return ret;
				}

				if ( config.stop ) {
					return false;
				}
			}

			return true;
		};

		if ( config.one ) {
			$( window ).one( "keydown." + namespace, _keydown );
		}
		else {
			$( window ).on( "keydown." + namespace, _keydown );
		}
	};
} )( jQuery );