"use strict";

(function($){
	function is_scalar( obj ) {
		return ( /string|number|boolean/ ).test( typeof obj );
	};

	/**
	 * Deep extend an object.
	 */
	$.evExtendObject = function( obj, defaults ) {
		if ( is_scalar( obj ) ) {
			return obj;
		}

		var new_obj = obj;

		$.each( defaults, function( key, value ) {
			if ( typeof new_obj[key] === "undefined" ) {
				if ( is_scalar( defaults[key] ) ) {
					new_obj[key] = value;
				}
				else {
					new_obj[key] = {};
				}
			}
			else if ( is_scalar( new_obj[key] ) ) {
				new_obj[key] = $.evExtendObject( new_obj[key], defaults[key] );
			}
		} );

		return new_obj;
	};

	$.evSaveRichTextareas = function( context ) {
		if ( typeof tinymce !== 'undefined' ) {
			$( ".ev-rich", context ).each( function() {
				tinymce.get( this.id ).save();
			} );
		}
	};

	/**
	 * Serialize a <form> element into an Object.
	 *
	 * @return {Object}
	 */
	$.fn.serializeObject = function(){

	    var self = this,
	        json = {},
	        push_counters = {},
	        patterns = {
	            "validate": /^[a-zA-Z_][a-zA-Z0-9_]*(?:\[(?:\d*|[a-zA-Z0-9_]+)\])*$/,
	            "key":      /[a-zA-Z0-9_]+|(?=\[\])/g,
	            "push":     /^$/,
	            "fixed":    /^\d+$/,
	            "named":    /^[a-zA-Z0-9_]+$/
	        };


	    this.build = function(base, key, value){
	        base[key] = value;
	        return base;
	    };

	    this.push_counter = function(key){
	        if(push_counters[key] === undefined){
	            push_counters[key] = 0;
	        }
	        return push_counters[key]++;
	    };

	    $.each($(this).serializeArray(), function(){

	        // skip invalid keys
	        if(!patterns.validate.test(this.name)){
	            return;
	        }

	        var k,
	            keys = this.name.match(patterns.key),
	            merge = this.value,
	            reverse_key = this.name;

	        while((k = keys.pop()) !== undefined){

	            // adjust reverse_key
	            reverse_key = reverse_key.replace(new RegExp("\\[" + k + "\\]$"), '');

	            // push
	            if(k.match(patterns.push)){
	                merge = self.build([], self.push_counter(reverse_key), merge);
	            }

	            // fixed
	            else if(k.match(patterns.fixed)){
	                merge = self.build([], k, merge);
	            }

	            // named
	            else if(k.match(patterns.named)){
	                merge = self.build({}, k, merge);
	            }
	        }

	        json = $.extend(true, json, merge);
	    });

	    $.each( json, function( i, el ) {
	    	if ( json[i].push ) {
	    		json[i] = json[i].filter( function( e ) {
	    			return ( e===undefined||e===null )? false : ~e;
	    		} );
	    	}
	    } );

	    return json;
	};
})(jQuery);

( function( $ ) {
	if ( typeof $.deparam !== "function" ) {
		/**
		 * An extraction of the deparam method from Ben Alman's jQuery BBQ.
		 *
		 * @see http://benalman.com/projects/jquery-bbq-plugin/
		 * @param  {[type]} params [description]
		 * @param  {[type]} coerce [description]
		 * @return {[type]}        [description]
		 */
		$.deparam = function ( params, coerce ) {
			var obj = {},
				coerce_types = { 'true': !0, 'false': !1, 'null': null };

			// Iterate over all name=value pairs.
			$.each(params.replace(/\+/g, ' ').split('&'), function (j,v) {
			  var param = v.split('='),
				  key = decodeURIComponent(param[0]),
				  val,
				  cur = obj,
				  i = 0,

				  // If key is more complex than 'foo', like 'a[]' or 'a[b][c]', split it
				  // into its component parts.
				  keys = key.split(']['),
				  keys_last = keys.length - 1;

			  // If the first keys part contains [ and the last ends with ], then []
			  // are correctly balanced.
			  if (/\[/.test(keys[0]) && /\]$/.test(keys[keys_last])) {
				// Remove the trailing ] from the last keys part.
				keys[keys_last] = keys[keys_last].replace(/\]$/, '');

				// Split first keys part into two parts on the [ and add them back onto
				// the beginning of the keys array.
				keys = keys.shift().split('[').concat(keys);

				keys_last = keys.length - 1;
			  } else {
				// Basic 'foo' style key.
				keys_last = 0;
			  }

			  // Are we dealing with a name=value pair, or just a name?
			  if (param.length === 2) {
				val = decodeURIComponent(param[1]);

				// Coerce values.
				if (coerce) {
				  val = val && !isNaN(val)              ? +val              // number
					  : val === 'undefined'             ? undefined         // undefined
					  : coerce_types[val] !== undefined ? coerce_types[val] // true, false, null
					  : val;                                                // string
				}

				if ( keys_last ) {
				  // Complex key, build deep object structure based on a few rules:
				  // * The 'cur' pointer starts at the object top-level.
				  // * [] = array push (n is set to array length), [n] = array if n is
				  //   numeric, otherwise object.
				  // * If at the last keys part, set the value.
				  // * For each keys part, if the current level is undefined create an
				  //   object or array based on the type of the next keys part.
				  // * Move the 'cur' pointer to the next level.
				  // * Rinse & repeat.
				  for (; i <= keys_last; i++) {
					key = keys[i] === '' ? cur.length : keys[i];
					cur = cur[key] = i < keys_last
					  ? cur[key] || (keys[i+1] && isNaN(keys[i+1]) ? {} : [])
					  : val;
				  }

				} else {
				  // Simple key, even simpler rules, since only scalars and shallow
				  // arrays are allowed.

				  if ($.isArray(obj[key])) {
					// val is already an array, so push on the next value.
					obj[key].push( val );

				  } else if (obj[key] !== undefined) {
					// val isn't an array, but since a second value has been specified,
					// convert val into an array.
					obj[key] = [obj[key], val];

				  } else {
					// val is a scalar.
					obj[key] = val;
				  }
				}

			  } else if (key) {
				// No value was defined, so set something meaningful.
				obj[key] = coerce
				  ? undefined
				  : '';
			  }
			});

			return obj;
		};
	}
} )( jQuery );

if ( typeof String.prototype.replaceLast !== "function" ) {
	/**
	 * Replace the last instance of a substring in a string.
	 *
	 * @param  {String} find The string to look for.
	 * @param  {String} replace The string to look into.
	 * @return {String} The string without the last element replaced.
	 */
	String.prototype.replaceLast = function( find, replace ) {
		var index = this.lastIndexOf( find );

		if (index >= 0) {
			return this.substring( 0, index ) + replace + this.substring( index + find.length );
		}

		return this.toString();
	};
}