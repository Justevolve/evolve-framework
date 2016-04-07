( function( $ ) {
	"use strict";

	/**
	 * Adding the multiple image upload sortable container to the UI building queue.
	 */
	$.evf.ui.add( ".ev-image-upload[data-multiple][data-sortable] .ev-image-placeholder-container", function() {
		$( this ).sortable( {
			items: "> .ev-image-placeholder",
			update: function( event, ui ) {
				var upload = $( event.target ).parents( ".ev-image-upload" ).first(),
					input = $( "input[data-id]", upload ),
					values = [];

				$( ".ev-image-placeholder", upload ).each( function() {
					values.push( $( "img[data-id]", $( this ) ).attr( "data-id" ) );
				} );

				input.val( values.join( "," ) );
			}
		} );
	} );

	/**
	 * When clicking on an image upload remove button, remove its the previously selected image.
	 */
	$.evf.delegate( ".ev-image-upload .ev-upload-remove", "click", "image_upload", function() {
		var upload = $( this ).parents( ".ev-image-upload" ).first(),
			container = $( ".ev-image-placeholder-container", upload ),
			multiple = upload.attr( "data-multiple" ) !== undefined,
			input = $( "input[data-id]", upload );

		if ( multiple ) {
			$( this ).parents( ".ev-image-placeholder" ).first().remove();

			var remaining_placeholders = $( ".ev-image-placeholder", upload );

			if ( ! remaining_placeholders.length ) {
				upload.removeClass( "ev-image-uploaded" );
				input.val( "" );

				var template = $( "script[type='text/template'][data-template='ev-image-placeholder']" );
				container.append( $.evf.template( template, {
					"url": "",
					"id": ""
				} ) );
			}
			else {
				var values = [];

				remaining_placeholders.each( function() {
					values.push( $( "img[data-id]", $( this ) ).attr( "data-id" ) );
				} );

				input.val( values.join( "," ) );
			}
		}
		else {
			input.val( "" );
			$( "img", upload ).attr( "src", "" );
			upload.removeClass( "ev-image-uploaded" );
		}

		return false;
	} );

	/**
	 * Remove all uploaded attachments.
	 */
	$.evf.delegate( ".ev-image-upload .ev-remove-all-action", "click", "image_upload", function() {
		var container = $( this ).parents( ".ev-image-upload" ).first(),
			images = $( ".ev-image-placeholder", container ),
			input = $( "input[data-id]", container );

		images.remove();
		container.removeClass( "ev-image-uploaded" );
		input.val( "" );

		return false;
	} );

	/**
	 * When clicking on an image upload Upload/Edit button, open a Media Library
	 * modal that allows the user to select an image to use.
	 */
	$.evf.delegate( ".ev-image-upload .ev-edit-action, .ev-image-upload .ev-upload-action", "click", "image_upload", function() {
		var upload = $( this ).parents( ".ev-image-upload" ).first(),
			container = $( ".ev-image-placeholder-container", upload ),
			thumb_size = upload.attr( "data-thumb-size" ),
			multiple = upload.attr( "data-multiple" ) !== undefined,
			input = $( "input[data-id]", upload ).val();

		var media = new window.Ev_MediaSelector( {
			type: "image",
			multiple: multiple,
			select: function( selection ) {
				var template = $( "script[type='text/template'][data-template='ev-image-placeholder']" ),
					value = "",
					html = "";

				container.html( "" );

				if ( multiple ) {
					value = _.pluck( selection, "id" ).join( "," );

					$.each( selection, function() {
						var image_url = "";

						if ( this.sizes && this.sizes.full ) {
							image_url = this.sizes.full.url;
						}

						if ( this.sizes && this.sizes[thumb_size] ) {
							image_url = this.sizes[thumb_size].url;
						}

						container.append( $.evf.template( template, {
							"url": image_url,
							"id": this.id
						} ) );
					} );
				}
				else {
					var image_url = "";

					value = selection.id;

					if ( selection.sizes && selection.sizes.full ) {
						image_url = selection.sizes.full.url;
					}
					else {
						image_url = selection.url;
					}

					if ( selection.sizes && selection.sizes[thumb_size] ) {
						image_url = selection.sizes[thumb_size].url;
					}

					container.append( $.evf.template( template, {
						"url": image_url,
						"id": value
					} ) );
				}

				upload.addClass( "ev-image-uploaded" );
				$( "input[data-id]", upload ).val( value );
			}
		} );

		media.open( input.split( "," ) );

		return false;
	} );

} )( jQuery );