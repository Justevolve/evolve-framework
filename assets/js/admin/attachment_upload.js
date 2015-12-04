( function( $ ) {
	"use strict";

	/**
	 * Adding the multiple attachment upload sortable container to the UI building queue.
	 */
	$.evf.ui.add( ".ev-attachment-upload-container[data-multiple][data-sortable]", function() {
		$( this ).sortable( {
			items: "> .ev-attachment-placeholder",
			update: function( event, ui ) {
				var container = $( event.target ),
					input = $( "input[data-id]", container ),
					values = [];

				$( ".ev-attachment-placeholder", container ).each( function() {
					values.push( $( "[data-id]", $( this ) ).attr( "data-id" ) );
				} );

				input.val( values.join( "," ) );
			}
		} );
	} );

	/**
	 * When clicking on an attachment upload remove button, remove its the previously selected image.
	 */
	$.evf.delegate( ".ev-attachment-placeholder .ev-upload-remove", "click", "attachment_upload", function() {
		var upload = $( this ).parents( ".ev-attachment-placeholder" ).first(),
			container = $( this ).parents( ".ev-attachment-upload-container" ).first(),
			multiple = container.attr( "data-multiple" ) !== undefined,
			input = $( "input[data-id]", container );

		upload.remove();

		if ( multiple ) {
			var remaining_placeholders = $( ".ev-attachment-placeholder", container );

			if ( ! remaining_placeholders.length ) {
				container.removeClass( "ev-attachment-uploaded" );
				input.val( "" );
			}
			else {
				var values = [];

				remaining_placeholders.each( function() {
					values.push( $( "[data-id]", $( this ) ).attr( "data-id" ) );
				} );

				input.val( values.join( "," ) );
			}
		}
		else {
			input.val( "" );
			$( "img", container ).attr( "src", "" );
			container.removeClass( "ev-attachment-uploaded" );
		}

		return false;
	} );

	/**
	 * Remove all uploaded attachments.
	 */
	$.evf.delegate( ".ev-attachment-upload-container .ev-remove-all-action", "click", "attachment_upload", function() {
		var container = $( this ).parents( ".ev-attachment-upload-container" ).first(),
			attachments = $( ".ev-attachment-placeholder", container ),
			input = $( "input[data-id]", container );

		attachments.remove();
		container.removeClass( "ev-attachment-uploaded" );
		input.val( "" );

		return false;
	} );

	/**
	 * When clicking on an attachment upload Upload/Edit button, open a Media Library
	 * modal that allows the user to select an attachment to use.
	 */
	$.evf.delegate( ".ev-attachment-upload-container .ev-edit-action, .ev-attachment-upload-container .ev-upload-action", "click", "attachment_upload", function() {
		var container = $( this ).parents( ".ev-attachment-upload-container" ).first(),
			type = container.attr( "data-type" ),
			thumb_size = container.attr( "data-thumb-size" ),
			multiple = container.attr( "data-multiple" ) !== undefined,
			input = $( "input[data-id]", container ).val();

		var media = new window.Ev_MediaSelector( {
			type: type,
			multiple: multiple,
			select: function( selection ) {
				var value = "",
					html = "",
					controls = $( ".ev-attachment-upload-action", container ),
					template = $( "script[type='text/template'][data-template='ev-attachment-placeholder']" );

				$( ".ev-attachment-placeholder", container ).remove();

				if ( multiple ) {
					value = _.pluck( selection, "id" ).join( "," );

					$.each( selection, function() {
						var extension = this.url.split(/[\\/]/).pop() + " (" + this.filesizeHumanReadable + ")",
							type = this.type;

						if ( this.subtype ) {
							type += "-" + this.subtype;
						}

						controls.before( $.evf.template( template, {
							"type": type,
							"id": this.id,
							"title": this.title,
							"extension": extension,
							"url": this.url
						} ) );
					} );
				}
				else {
					value = selection.id;
					var extension = selection.url.split(/[\\/]/).pop() + " (" + selection.filesizeHumanReadable + ")",
						type = selection.type;

					if ( selection.subtype ) {
						type += "-" + selection.subtype;
					}

					controls.before( $.evf.template( template, {
						"type": type,
						"id": value,
						"title": selection.title,
						"extension": extension,
						"url": selection.url
					} ) );
				}

				container.addClass( "ev-attachment-uploaded" );
				$( "input[data-id]", container ).val( value );
			}
		} );

		media.open( input.split( "," ) );

		return false;
	} );

} )( jQuery );