( function( $ ) {
	"use strict";

	/* Custom component namespace. */
	var namespace = "modal";

	/**
	 * Remove the modal when clicking on its background.
	 *
	 * @return {boolean}
	 */
	// $.evf.delegate( ".ev-modal-container", "click", namespace, function( e ) {
	// 	if ( $( e.target ).is( ".ev-modal-container" ) ) {
	// 		$( this ).remove();
	// 		$( "body" ).removeClass( "ev-modal-open" );

	// 		return false;
	// 	}
	// } );

	/**
	 * Remove the modal when clicking on its close button.
	 *
	 * @return {boolean}
	 */
	$.evf.delegate( ".ev-modal-close", "click", namespace, function( e ) {
		$( this ).parents( ".ev-modal-container" ).first().data( "ev-modal" ).close();

		return false;
	} );

	/**
	 * Remove the foremost modal when pressing the ESC key.
	 *
	 * @return {boolean}
	 */
	$.evf.key( "esc", function() {
		var modals = $( ".ev-modal-container" );

		if ( modals.length ) {
			modals.last().data( "ev-modal" ).close();

			return false;
		}
	} );

	/**
	 * Modal window.
	 *
	 * @param {String} key The modal key.
	 * @param {Object} data The data supplied to the modal window when opening it.
	 * @param {Object} config The configuration object.
	 */
	$.evf.modal = function( key, data, config ) {
		config = $.extend( {
			/* Callback function fired after the modal is saved. */
			save: function() {},

			/* Callback function fired after the modal is closed. */
			close: function() {},

			/* Additional CSS class to be passed to the modal container. */
			class: "",

			/* Wait for the save function to be completed before closing the modal. */
			wait: false,

			/* Set to true if the modal is reduced in size. */
			simple: false,
		}, config );

		var self = this;

		self.config = config;
		// self.scroll = 0;

		/**
		 * Close the modal.
		 */
		this.close = function() {
			config.close();

			$( ".ev-modal-container[data-key='" + key + "']" ).nextAll( ".ev-modal-container" ).remove();
			$( ".ev-modal-container[data-key='" + key + "']" ).remove();
			// $( window ).trigger( "resize" );

			var modals = $( ".ev-modal-container" );

			if ( ! modals.length ) {
				$( "body" ).removeClass( "ev-modal-open" );
			}

			// setTimeout( function() {
			// 	$.scrollTo( self.scroll );
			// 	self.scroll = 0;
			// }, 200 );

			$( window ).trigger( "ev-modal-close" );
		};

		/**
		 * Close the modal and serialize its contents.
		 *
		 * @param {Object} data The modal serialized data.
		 */
		this.save = function( data ) {
			var origin = ".ev-modal-container[data-key='" + key + "']",
				save_btn = origin + " .ev-modal-footer .ev-save",
				nonce = $( save_btn ).attr( "data-nonce" );

			if ( config.wait ) {
				config.save( data, this.close, nonce );
			}
			else {
				config.save( data, null, nonce );
				this.close();
			}
		};

		/**
		 * Open the modal.
		 *
		 * @param {Function} content The function that populates the modal content.
		 */
		this.open = function( content ) {
			if ( typeof content !== "function" ) {
				throw new Error( "Content is not a function." );
			}

			// self.scroll = $( window ).scrollTop();
			var origin = ".ev-modal-container[data-key='" + key + "']";

			$( origin ).remove();

			var modal_class = config.class;

			if ( config.simple ) {
				modal_class += " ev-modal-container-simple";
			}

			var html = '<div class="ev-modal-container ' + modal_class + '" data-key="' + key + '">';
				html += '<div class="ev-modal-wrapper">';
					html += '<a class="ev-modal-close" href="#"><i data-icon="ev-modal-close" class="ev-icon ev-component" aria-hidden="true"></i></a>';

					html += '<div class="ev-modal-wrapper-inner">';
					html += '</div>';
				html += '</div>';
			html += '</div>';

			html = $( html );

			if ( ! $( "body" ).hasClass( "ev-modal-open" ) ) {
				html.appendTo( $( "#ev-modals-container" ) );
				$( "body" ).addClass( "ev-modal-open" );
			}
			else {
				$( ".ev-modal-container" ).last().after( html );
			}

			$( ".ev-modal-container" ).last().data( "ev-modal", self );

			content(
				$( origin + " .ev-modal-wrapper-inner" ),
				key,
				data
			);
		};

		/**
		 * Initialize the component.
		 */
		this.init = function() {
			var origin = ".ev-modal-container[data-key='" + key + "']",
				save_btn = origin + " .ev-modal-footer .ev-save",
				form = origin + " form",
				modal_namespace = namespace + "-form-" + key;

			$.evf.undelegate( "submit", modal_namespace );
			$.evf.undelegate( "click", modal_namespace );

			$.evf.delegate( save_btn, "click", modal_namespace, function() {
				$( form ).trigger( "submit." + modal_namespace );

				return false;
			} );

			$.evf.delegate( form, "submit", modal_namespace, function() {
				$.evSaveRichTextareas( this );

				ev_idle_button( $( save_btn ) );

				self.save( $( this ).serializeObject() );

				$.evf.undelegate( "submit", modal_namespace );
				$.evf.undelegate( "click", modal_namespace );

				return false;
			} );
		};

		this.init();
	};
} )( jQuery );
