<?php
	$classes = array();
	$size = $field->config( 'size' );

	if ( ! empty( $size ) && ! is_numeric( $size ) ) {
		$classes[] = 'ev-' . $size;
		$size = '';
	}
?>

<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" size="<?php echo esc_attr( $size ); ?>" name="<?php echo esc_attr( $field->handle() ); ?>" value="<?php echo esc_attr( $field->value() ); ?>">