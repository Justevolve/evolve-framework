<?php

$multiple = $field->config( 'multiple' );
$size = $field->config( 'size' );
$value = $field->value();
$handle = $field->handle();

if ( $multiple !== false && is_array( $multiple ) ) {

	foreach ( $multiple as $k => $field_label ) {
		$field_value = $value;

		if ( is_array( $field_value ) && isset( $field_value[$k] ) ) {
			$field_value = $field_value[$k];
		}

		$field_handle = $handle . '[' . $k . ']';

		echo '<div class="ev-color-input-wrapper">';
			echo '<span class="ev-sub-label">' . esc_html( $field_label ) . '</span>';
			echo '<input type="text" class="ev-color-input" size="' . esc_attr( $size ) .'" name="' . esc_attr( $field_handle ) . '" value="' . esc_attr( $field_value ) . '">';
		echo '</div>';
	}

}
else {
	echo '<input type="text" class="ev-color-input" size="' . esc_attr( $size ) .'" name="' . esc_attr( $handle ) . '" value="' . esc_attr( $value ) . '">';
}