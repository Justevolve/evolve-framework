<?php
	$step  = $field->config( 'step' );
	$min   = $field->config( 'min' );
	$max   = $field->config( 'max' );
	$value = $field->value();
?>
<input type="number" name="<?php echo esc_attr( $field->handle() ); ?>" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min ); ?>" max="<?php echo esc_attr( $max ); ?>" value="<?php echo esc_attr( $value ); ?>">