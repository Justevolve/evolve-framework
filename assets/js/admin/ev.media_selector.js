/**
 * WordPress media selector plugin.
 * by Evolve, info@justevolve.it
 * http://justevolve.it
 *
 * Copyright (c) 2016 Andrea Gandino, Simone Maranzana
 * Licensed under the GPL (http://www.gnu.org/licenses/quick-guide-gplv3.html) license.
 *
 * Version: 1.0.2, 07.08.2015
 */
;( function( $ ) {
	"use strict";

	window.Ev_MediaSelector = function( options ) {
		/**
		 * Instance to self.
		 */
		window.ev_ms_obj = this;

		/**
		 * Configuration options.
		 */
		window.ev_ms_obj.options = $.extend( {}, {
			/**
			 * Title of the media selection modal window.
			 *
			 * @type {String}
			 */
			title: undefined,

			/**
			 * Set to true to activate multiple selection.
			 *
			 * @type {Boolean}
			 */
			multiple: false,

			/**
			 * Restrict items by type.
			 * Pick from: "", "image", "audio", "video".
			 */
			type: "",

			/**
			 * Text of the modal submit button.
			 *
			 * @type {String}
			 */
			button: undefined,

			/**
			 * Modal window close callback function. Fired when the modal
			 * window is closed without confirming the selection.
			 */
			close: function() {},

			/**
			 * Modal window select callback function. Fired when the modal
			 * window is closed confirming the selection. The passed
			 * argument is an array containing the selected image(s).
			 *
			 * @param {Array} selection Array containing the selected image(s).
			 */
			select: function( selection ) {}
		}, options );

		window.ev_ms_obj.frame = null;

		/**
		 * Open the WordPress Media Manager modal window. If an array of IDs is
		 * specified, the modal window will pre-select the relative attachments.
		 * If IDs are specified, the modal window will open itself on the "Browse"
		 * tab instead of the "Upload" tab.
		 *
		 * @param  {Array} ids An array of attachment IDs.
		 */
		window.ev_ms_obj.open = function( ids ) {
			var insertImage = wp.media.controller.Library.extend( {
			    defaults :  _.defaults({
					id                 : 'ev-media-selector',
					title              : window.ev_ms_obj.options.title,
					allowLocalEdits    : false,
					displaySettings    : false,
					displayUserSettings: false,
					multiple           : window.ev_ms_obj.options.multiple,
					library            : wp.media.query( { type: window.ev_ms_obj.options.type } )
					// type               : window.ev_ms_obj.options.type
				}, wp.media.controller.Library.prototype.defaults )
			} );

			window.ev_ms_obj.frame = wp.media( {
				title: window.ev_ms_obj.options.title,
				button: { text: window.ev_ms_obj.options.button },
				state : 'ev-media-selector',
				states : [
					new insertImage()
				]
			} );

			window.ev_ms_obj.frame.on( "open", function() {
				var selection = window.ev_ms_obj.frame.state( 'ev-media-selector' ).get( "selection" );
				selection.reset();

				_.each( ids, function( id ) {
					var attachment = wp.media.attachment( id );

					attachment.fetch();
					selection.add( attachment ? [ attachment ] : [] );
				} );
			} );

			window.ev_ms_obj.frame.on( "select", function() {
				var selection = window.ev_ms_obj.frame.state( 'ev-media-selector' ).get( "selection" ),
					result = window.ev_ms_obj.options.multiple ? selection.toJSON() : selection.first().toJSON();

				window.ev_ms_obj.options.select( result );

				// var attachment = selection.first();

				// var display = window.ev_ms_obj.frame.state( 'ev-media-selector' ).display( attachment ).toJSON();
				//         var obj_attachment = attachment.toJSON()

				//         display = wp.media.string.props( display, obj_attachment );

				// if ( window.ev_ms_obj ) {
				// 	delete window.ev_ms_obj;
				// }
			} );

			window.ev_ms_obj.frame.on( "close", function() {
				window.ev_ms_obj.options.close();

				// if ( window.ev_ms_obj ) {
				// 	delete window.ev_ms_obj;
				// }
			} );

			window.ev_ms_obj.frame.open();
		};
	}
} )( jQuery );