<?php

$multiple = $field->config( 'multiple' );
$opacity  = $field->config( 'opacity' );
$value    = $field->value();
$handle   = $field->handle();

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
				// echo ev_color_field_palette_html( $palette, $field_color );

				ev_color( $field_handle, $field_value, $opacity );
			echo '</div>';
		echo '</div>';
	}

}
else {
	echo '<div class="ev-color-inner-wrapper">';
		// echo ev_color_field_palette_html( $palette, $value );

		ev_color( $handle, $value, $opacity );
	echo '</div>';
}
