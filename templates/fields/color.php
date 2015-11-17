<?php

$multiple    = $field->config( 'multiple' );
$size        = $field->config( 'size' );
$palette     = $field->config( 'palette' );
$opacity     = $field->config( 'opacity' );
$value       = $field->value();
$handle      = $field->handle();
$input_type  = 'text';
$input_class = 'ev-color-input';
$has_palette = is_array( $palette );
$opacity_data = '';
$field_opacity = '';

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

		$field_color = isset( $field_value['color'] ) ? $field_value['color'] : '';
		$field_opacity = isset( $field_value['opacity'] ) ? $field_value['opacity'] : '';

		echo '<div class="ev-color-input-wrapper">';
			echo '<span class="ev-sub-label">' . esc_html( $field_label ) . '</span>';

			echo '<div class="ev-color-inner-wrapper">';
				echo ev_color_field_palette_html( $palette, $field_color );

				if ( $opacity ) {
					$opacity_data = 'data-opacity=' . $field_opacity;

					printf( '<input type="hidden" data-input-color-opacity name="%s" value="%s">',
						esc_attr( $field_handle . '[opacity]' ),
						esc_attr( $field_opacity )
					);
				}

				printf( '<input type="%s" class="%s" size="%s" name="%s" value="%s" %s>',
					esc_attr( $input_type ),
					esc_attr( $input_class ),
					esc_attr( $size ),
					esc_attr( $field_handle . '[color]' ),
					esc_attr( $field_color ),
					esc_attr( $opacity_data )
				);
			echo '</div>';
		echo '</div>';
	}

}
else {
	echo '<div class="ev-color-inner-wrapper">';
		echo ev_color_field_palette_html( $palette, $value );

		$field_color = isset( $value['color'] ) ? $value['color'] : '';
		$field_opacity = isset( $value['opacity'] ) ? $value['opacity'] : '';

		if ( $opacity ) {
			$opacity_data = 'data-opacity=' . $field_opacity;

			printf( '<input type="hidden" data-input-color-opacity name="%s" value="%s">',
				esc_attr( $handle . '[opacity]' ),
				esc_attr( $field_opacity )
			);
		}

		printf( '<input type="%s" class="%s" size="%s" name="%s" value="%s" %s>',
			esc_attr( $input_type ),
			esc_attr( $input_class ),
			esc_attr( $size ),
			esc_attr( $handle . '[color]' ),
			esc_attr( $field_color ),
			esc_attr( $opacity_data )
		);
	echo '</div>';
}
