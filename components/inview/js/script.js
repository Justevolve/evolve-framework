( function( $ ) {
	"use strict";

	$.evf.inview = new function() {

		var self = this;

		this.inview_class = "ev-inview";

		this.selectors = [];

		this.register = function( selector, callback ) {
			$( selector ).on( "inview", function( event, isInView, visiblePartX, visiblePartY ) {
				if ( isInView ) {
					$( this ).addClass( self.inview_class );

					if ( callback ) {
						callback( $( this ) );
					}
				}
			} );
		};

	};
} )( jQuery );