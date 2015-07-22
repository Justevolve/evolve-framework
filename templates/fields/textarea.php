<?php
	$is_rich = $field->config( 'rich' ) !== false;
	$rows = $field->config( 'rows' );
	$cols = $field->config( 'cols' );
	$class = $is_rich ? 'ev-rich' : '';
?>

<textarea class="<?php echo esc_attr( $class ); ?>" rows="<?php echo esc_attr( $rows ); ?>" cols="<?php echo esc_attr( $cols ); ?>" name="<?php echo esc_attr( $field->handle() ); ?>" id="<?php echo esc_attr( $field->handle() ); ?>"><?php echo esc_html( $field->value() ); ?></textarea>