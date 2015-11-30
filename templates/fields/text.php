<?php
	$classes = array();
	$size = $field->config( 'size' );
	$full = $field->config( 'full' );
	$link = $field->config( 'link' );
	$value = $field->value();
	$handle = $field->handle();
	$style = $field->config( 'style' );
	$style = (array) $style;

	if ( $full === true ) {
		$classes[] = 'ev-field-input-size-full';
	}

	if ( ! empty( $style ) ) {
		$style_class = 'ev-field-text-style-';

		foreach ( $style as $s ) {
			$classes[] = $style_class . $s;
		}
	}

	$text_value = $value;
	$handle_suffix = '';

	if ( is_array( $value ) ) {
		$text_value = isset( $value['text'] ) ? $value['text'] : '';
		$handle_suffix = '[text]';
	}
?>

<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" size="<?php echo esc_attr( $size ); ?>" name="<?php echo esc_attr( $handle . $handle_suffix ); ?>" value="<?php echo esc_attr( $text_value ); ?>">

<?php

if ( $link !== false ) {
	ev_link_partial( $handle, $value );
}
