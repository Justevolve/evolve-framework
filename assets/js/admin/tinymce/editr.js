( function( $ ) {
	"use strict";

	if ( ! ev_editr.length ) {
		return;
	}

	tinymce.PluginManager.add( 'ev_editr', function( editor, url ) {
		function editr( klass ) {
			var block = [ "p", "div", "blockquote" ],
				text = editor.selection.getContent( {
					'format': 'html'
				} ),
				node = editor.selection.getNode();

			if ( text.length ) {
				if ( node.nodeName.toLowerCase() == "body" ) {
					// Check for random nbsps
					editor.selection.setContent( '<div class="' + klass + '">' + text + '</div>' );
				}
				else {
					if ( block.indexOf( node.nodeName.toLowerCase() ) !== -1 && node.innerHTML == text ) {
						editor.dom.toggleClass( node, klass );
					}
					else {
						if ( node.classList.contains( klass ) ) {
							editor.dom.removeClass( node, klass );
						}
						else {
							editor.selection.setContent( '<span class="' + klass + '">' + text + '</span>' );
						}
					}
				}
			}
			else {
				editor.dom.toggleClass( node, klass );
			}
		}

		var menuItems = [];

		$.each( ev_editr, function() {
			var format = this;

			menuItems.push( {
				text: format.text,
				onclick: function() {
					editr( format.class );
				}
			} );
		} );

		editor.addButton( "ev_editr", {
			type: "menubutton",
			title: "Editr",
			menu: menuItems,
			icon: "code"
		} );

		editor.addMenuItem( 'ev_editrDropDownMenu', {
			menu: menuItems,
		} );
	} );
} )( jQuery );