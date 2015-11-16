<?php
	$classes = array();
	$size = $field->config( 'size' );
	$full = $field->config( 'full' );
	$link = $field->config( 'link' );
	$value = $field->value();
	$handle = $field->handle();

	if ( $full === true ) {
		$classes[] = 'ev-field-input-size-full';
	}
?>

<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" size="<?php echo esc_attr( $size ); ?>" name="<?php echo esc_attr( $handle ); ?>" value="<?php echo esc_attr( $value ); ?>">

<?php

if ( $link !== false ) {
	$link_value = isset( $value['link'] ) ? $value['link'] : array();

	ev_link_partial( $handle, $link_value );
}
