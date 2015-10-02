<?php

$multiple = $field->config( 'multiple' );
$size = $field->config( 'size' );
$palette = $field->config( 'palette' );
$value = $field->value();
$handle = $field->handle();
$input_type = 'text';
$input_class = 'ev-color-input';
$has_palette = is_array( $palette );

if ( $has_palette ) {
	$input_type = 'hidden';
	$input_class = '';
}

if ( $multiple !== false && is_array( $multiple ) ) {

	foreach ( $multiple as $k => $field_label ) {
		$field_value = $value;

		if ( is_array( $field_value ) && isset( $field_value[$k] ) ) {
			$field_value = $field_value[$k];
		}

		$field_handle = $handle . '[' . $k . ']';

		echo '<div class="ev-color-input-wrapper">';
			echo '<span class="ev-sub-label">' . esc_html( $field_label ) . '</span>';

			echo '<div class="ev-color-inner-wrapper">';
				echo ev_color_field_palette_html( $palette, $field_value );

				printf( '<input type="%s" class="%s" size="%s" name="%s" value="%s">',
					esc_attr( $input_type ),
					esc_attr( $input_class ),
					esc_attr( $size ),
					esc_attr( $field_handle ),
					esc_attr( $field_value )
				);
			echo '</div>';
		echo '</div>';
	}

}
else {
	echo '<div class="ev-color-inner-wrapper">';
		echo ev_color_field_palette_html( $palette, $value );

		printf( '<input type="%s" class="%s" size="%s" name="%s" value="%s">',
			esc_attr( $input_type ),
			esc_attr( $input_class ),
			esc_attr( $size ),
			esc_attr( $handle ),
			esc_attr( $value )
		);
	echo '</div>';
}
