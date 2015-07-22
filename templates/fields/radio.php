<?php
	$data = $field->config( 'data' );
	$value = $field->value();

	$i=0;
	foreach ( $data as $k => $v ) {
		$checked = '';

		if ( $value == $k || ( empty( $value ) && $i === 0 ) ) {
			$checked = 'checked';
		}

		echo '<label class="ev-radio">';
			printf( '<input name="%s" type="radio" value="%s" %s>', esc_attr( $field->handle() ), esc_attr( $k ), esc_attr( $checked ) );
			echo esc_attr( $v );
		echo '</label>';

		$i++;
	}
?>
