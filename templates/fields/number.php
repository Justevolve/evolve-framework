<?php
	$full    = $field->config( 'full' );
	$style   = $field->config( 'style' );

	$step    = $field->config( 'step' );
	$min     = $field->config( 'min' );
	$max     = $field->config( 'max' );
	$value   = $field->value();
	$classes = array();

	if ( $full === true ) {
		$classes[] = 'ev-field-input-size-full';
	}

	if ( ! empty( $style ) ) {
		$style_class = 'ev-field-number-style-';

		foreach ( $style as $s ) {
			$classes[] = $style_class . $s;
		}
	}
?>
<input type="number" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" name="<?php echo esc_attr( $field->handle() ); ?>" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" value="<?php echo esc_attr( $value ); ?>">