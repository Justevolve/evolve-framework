( function( $ ) {
	"use strict";

	/**
	 * Inview controller.
	 */
	$.evf.inview = new function() {

		var self = this;

		/* The class associated to elements entering the viewport. */
		this.inview_class = "ev-inview";

		/**
		 * Register a selector to perform an action when entering the viewport.
		 */
		this.register = function( selector, callback, toggle ) {
			$( selector ).on( "inview", function( event, isInView, visiblePartX, visiblePartY ) {
				if ( isInView ) {
					if ( ! $( this ).hasClass( self.inview_class ) ) {
						$( this ).addClass( self.inview_class );

						if ( callback ) {
							callback( $( this ) );
						}
					}
				}
				else if ( toggle ) {
					$( this ).removeClass( self.inview_class );
				}
			} );
		};

	};
} )( jQuery );