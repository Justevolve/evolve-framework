<?php
	$value = $field->value();
	$checked = '';

	if ( $value == 1 ) {
		$checked = 'checked';
	}
?>
<input name="<?php echo esc_attr( $field->handle() ); ?>" type="hidden" value="0">
<input name="<?php echo esc_attr( $field->handle() ); ?>" type="checkbox" value="1" <?php echo esc_attr( $checked ); ?>>