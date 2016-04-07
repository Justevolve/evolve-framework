/**
 * Evolve Framework Javascript bootstrap
 * Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * Licensed under GPL (http://www.gnu.org/licenses/old-licenses/gpl-2.0.html)
 */

"use strict";

if ( typeof jQuery === "undefined" ) {
	throw new Error( "jQuery is required." );
}

( function( $ ) {
	/**
	 * Evolve Framework master object. This object stores configuration used by
	 * the components.
	 *
	 * @type {Object}
	 */
	$.evf = {
		/* The CSS class that identifies framework components. */
		componentID: ".ev-component",

		/* Default namespace used by events bound by components. */
		namespace: ".evf",

		/* Complex UI components management. */
		ui: {
			/* Event name that identifies the creation of complex UI components. */
			event: "ui-build",

			signature: "ev-ui",

			components: [],

			components_count: 0,

			components_built: 0,

			/**
			 * Add a complex UI component to the building queue.
			 *
			 * @param  {String|Object} selector The UI component selector or object.
			 * @param  {Function} The UI component creation callback.
			 */
			add: function( selector, callback ) {
				$.evf.ui.components.push( {
					"selector": selector,
					"callback": callback
				} );

				$.evf.ui.components_count++;
			},

			/**
			 * Actually perform the building of the UI.
			 */
			do_build: function() {
				$.each( $.evf.ui.components, function() {
					$.evf.ui.components_built++;

					var selector = this.selector,
						callback = this.callback;

					var elements = $( selector );

					elements = elements.filter( function() {
						return $( this ).attr( "data-" + $.evf.ui.signature ) !== "1";
					} );

					if ( ! elements.length ) {
						$.evf.ui.end_build();
						return;
					}

					elements.attr( "data-" + $.evf.ui.signature, "1" );
					callback.call( elements );
					$.evf.ui.end_build();
				} );
			},

			/**
			 * Declare finished the building process.
			 */
			end_build: function() {
				if ( $.evf.ui.components_count === $.evf.ui.components_built ) {
					setTimeout( function() {
						$( ".ev-tab-container" ).addClass( "ev-tab-container-loaded" );
					}, 5 );
				}
			},

			/**
			 * This function is entitled to instantiate complex UI components that alter
			 * the DOM structure.
			 *
			 * Triggers a "ui-build.evf" event on the $( window ) object.
			 */
			build: function() {
				$.evf.ui.components_built = 0;

				$.evf.ui.do_build();
				$( window ).trigger( $.evf.resolveEventName( $.evf.ui.event, $.evf.namespace ) );
			},
		},

		/**
		 * Resolve a custom event namespace.
		 *
		 * @param  {String} ns The custom event namespace.
		 * @return {String}
		 */
		resolveNamespace: function( ns ) {
			if ( ns !== "" && ns.indexOf( "." ) === -1 ) {
				ns = "." + ns;
			}

			return $.evf.namespace + ns;
		},

		/**
		 * Resolve a custom event name with proper namespace.
		 *
		 * @param  {String} ns The event name.
		 * @return {String}
		 */
		resolveEventName: function( event, ns ) {
			var name = "";

			$.each( event.split( " " ), function() {
				name += " " + this + $.evf.resolveNamespace( ns );
			} );

			name = name.trim();

			return name;
		},

		/**
		 * Bind an event to an element with proper namespacing.
		 *
		 * @param  {jQuery}   element The DOM object.
		 * @param  {String}   event The event name.
		 * @param  {String}   ns The event namespace.
		 * @param  {Function} callback Function to be executed upon firing of the event.
		 */
		on: function( element, event, ns, callback ) {
			$( element ).on( $.evf.resolveEventName( event, ns ), callback );
		},

		/**
		 * Bind a one-time event to an element with proper namespacing.
		 *
		 * @param  {jQuery}   element The DOM object.
		 * @param  {String}   event The event name.
		 * @param  {String}   ns The event namespace.
		 * @param  {Function} callback Function to be executed upon firing of the event.
		 */
		one: function( element, event, ns, callback ) {
			$( element ).one( $.evf.resolveEventName( event, ns ), callback );
		},

		/**
		 * Bind an event to the document to be executed when firing on a specific set of elements with proper namespacing.
		 *
		 * @param  {String}   element The CSS selector string.
		 * @param  {String}   event The event name.
		 * @param  {String}   ns The event namespace.
		 * @param  {Function} callback Function to be executed upon firing of the event.
		 */
		delegate: function( element, event, ns, callback ) {
			$( document ).on( $.evf.resolveEventName( event, ns ), element, callback );
		},

		/**
		 * Undelegate an event from the document element.
		 *
		 * @param  {String}   event The event name.
		 * @param  {String}   ns The event namespace.
		 */
		undelegate: function( event, ns ) {
			$( document ).off( $.evf.resolveEventName( event, ns ) );
		},

		/**
		 * Templating function.
		 *
		 * @param  {String|Object} name The template key or the template object.
		 * @param  {Object} data The template data.
		 * @return {String} The template contents.
		 */
		template: function( name, data ) {
			if ( typeof _ === "undefined" ) {
				throw new Error( "Underscore is required." );
				return '';
			}

			var raw = name;

			if ( typeof name === "string" ) {
				raw = $( "script[type='text/template'][data-template='" + name + "']" );
			}

			if ( ! raw.length ) {
				throw new Error( "Template '" + name + "' not found." );
				return '';
			}

			var template_settings = {
					evaluate:    /<#([\s\S]+?)#>/g,
					interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
					escape:      /\{\{([^\}]+?)\}\}(?!\})/g
				},
				version = parseFloat( ev_framework.wp_version );

			if ( version < 4.5 ) {
				return _.template(
					raw.html(),
					data,
					template_settings
				);
			}
			else {
				var template = _.template( raw.html(), template_settings );

				return template( data );
			}
		}
	};

	/* Boot the components. */
	$( document ).on( "ready", function() {
		$.evf.ui.build();
		$.evf.tabs( ".ev-tabs" + $.evf.componentID );
		$.evf.accordion( ".ev-accordion" + $.evf.componentID );
	} );
} )( jQuery );