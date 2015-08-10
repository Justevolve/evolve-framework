<?php
	$data = $field->config( 'data' );
	$graphic = $field->config( 'graphic' );
	$value = $field->value();

	$i=0;
	foreach ( $data as $k => $v ) {
		$checked = '';
		$class = '';

		if ( $value == $k || ( empty( $value ) && $i === 0 ) ) {
			$checked = 'checked';
		}

		if ( $graphic ) {
			$class = 'ev-graphic-radio';
		}

		printf( '<label class="ev-radio %s">', esc_attr( $class ) );
			printf( '<input name="%s" type="radio" value="%s" %s>', esc_attr( $field->handle() ), esc_attr( $k ), esc_attr( $checked ) );

			if ( $graphic ) {
				$image = $v;
				$label = '';

				if ( is_array( $v ) ) {
					$image = isset( $v['image'] ) && ! empty( $v['image'] ) ? $v['image'] : '';
					$label = isset( $v['label'] ) && ! empty( $v['label'] ) ? $v['label'] : '';
				}

				printf( '<img src="%s" title="%s">', esc_attr( $image ), esc_attr( $label ) );
			}
			else {
				echo esc_html( $v );
			}
		echo '</label>';

		$i++;
	}
?>
