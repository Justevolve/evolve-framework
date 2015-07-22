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
		$( this ).parents( ".ev-modal-container" ).first().remove();
		$( "body" ).removeClass( "ev-modal-open" );

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
			modals.last().remove();
			$( "body" ).removeClass( "ev-modal-open" );

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
			/* Callback function fired after the drawer transition starts. */
			save: function() {}
		}, config );

		var self = this;

		/**
		 * Close the modal.
		 */
		this.close = function() {
			$( ".ev-modal-container[data-key='" + key + "']" ).remove();
			$( "body" ).removeClass( "ev-modal-open" );
		};

		/**
		 * Close the modal and serialize its contents.
		 *
		 * @param {Object} data The modal serialized data.
		 */
		this.save = function( data ) {
			config.save( data );
			this.close();
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

			var origin = ".ev-modal-container[data-key='" + key + "']";

			$( origin ).remove();

			var html = '<div class="ev-modal-container" data-key="' + key + '">';
				html += '<div class="ev-modal-wrapper">';
					html += '<a class="ev-modal-close" href="#"><i data-icon="ev-modal-close" class="ev-icon ev-component" aria-hidden="true"></i></a>';

					html += '<div class="ev-modal-wrapper-inner">';
					html += '</div>';
				html += '</div>';
			html += '</div>';

			$( html ).appendTo( $( "body" ) );
			$( "body" ).addClass( "ev-modal-open" );

			var save_btn = origin + " .ev-modal-footer .ev-save",
				form = origin + " form";

			namespace += "-form";

			$.evf.delegate( save_btn, "click", namespace, function() {
				$( form ).trigger( "submit." + namespace );

				return false;
			} );

			$.evf.delegate( form, "submit", namespace, function() {
				if ( typeof tinymce !== 'undefined' ) {
					tinymce.triggerSave();
				}

				self.save( $( form ).serializeObject() );

				$.evf.undelegate( "submit", namespace );
				$.evf.undelegate( "click", namespace );

				return false;
			} );

			content(
				$( origin + " .ev-modal-wrapper-inner" ),
				key,
				data
			);
		};
	};
} )( jQuery );