<?php
$multiple = $field->config( 'multiple' );

if ( $multiple !== false && is_array( $multiple ) ) {

	foreach ( $multiple as $k => $label ) {
		$field_value = $field->value();

		if ( isset( $field_value ) && isset( $field_value[$k] ) ) {
			$field_value = $field_value[$k];
		}

		$field_handle = $field->handle() . '[' . $k . ']';
		$field_label = $label;

		echo '<div class="ev-color-input-wrapper">';
			echo '<span class="ev-sub-label">' . esc_html( $field_label ) . '</span>';
			echo '<input type="text" class="ev-color-input" size="' . esc_attr( $field->config( 'size' ) ) .'" name="' . esc_attr( $field_handle ) . '" value="' . esc_attr( $field_value ) . '">';
		echo '</div>';
	}

}
else {
	echo '<input type="text" class="ev-color-input" size="' . esc_attr( $field->config( 'size' ) ) .'" name="' . esc_attr( $field->handle() ) . '" value="' . esc_attr( $field->value() ) . '">';
}