<?php
	$classes = array();
	$size = $field->config( 'size' );
	$full = $field->config( 'full' );

	if ( $full === true ) {
		$classes[] = 'ev-field-input-size-full';
	}
?>

<input type="text" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>" size="<?php echo esc_attr( $size ); ?>" name="<?php echo esc_attr( $field->handle() ); ?>" value="<?php echo esc_attr( $field->value() ); ?>">