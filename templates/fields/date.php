<?php
	$classes = array(
		'ev-date-input'
	);

	$size    = $field->config( 'size' );
	$value   = $field->value();
	$handle  = $field->handle();
	$style   = $field->config( 'style' );
	$format  = $field->config( 'format' );
	$style   = (array) $style;

	if ( ! empty( $style ) ) {
		$style_class = 'ev-field-date-style-';

		foreach ( $style as $s ) {
			$classes[] = $style_class . $s;
		}
	}

?>

<input type="text" data-format="<?php echo esc_attr( $format ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" size="<?php echo esc_attr( $size ); ?>" name="<?php echo esc_attr( $handle ); ?>" value="<?php echo esc_attr( $value ); ?>">