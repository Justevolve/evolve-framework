/* Highlight */
$( document ).ready(function() {
	hljs.initHighlightingOnLoad();

	$( '.nav-toggle' ).on( 'click', function() {
		$( this ).toggleClass( 'open' );
		$( '.nav-wrapper' ).toggleClass( 'open' );
	});
});