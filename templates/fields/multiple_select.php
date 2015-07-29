<?php
	$class = 'ev-multiple-select-input';

	if ( $field->config( 'vertical' ) === true ) {
		$class .= ' ev-multiple-select-vertical';
	}

	$data = $field->config( 'data' );
	$structured_data = array();

	foreach ( $data as $val => $texts ) {
		$label = is_array( $texts ) && isset( $texts['label'] ) ? $texts['label'] : $texts;
		$spec = is_array( $texts ) && isset( $texts['spec'] ) ? $texts['spec'] : '';

		$structured_data[] = array(
			'val'   => $val,
			'label' => $label,
			'spec'  => $spec,
		);
	}

	$data = json_encode( $structured_data );

	$attrs = array();

	if ( empty( $structured_data ) ) {
		$attrs[] = 'disabled';
	}

	$attrs = array_map( 'esc_attr', $attrs );
?>

<input type="hidden" <?php echo implode( ' ', $attrs ); ?> data-options="<?php echo esc_attr( $data ); ?>" class="<?php echo esc_attr( $class ); ?>" name="<?php echo esc_attr( $field->handle() ); ?>" value="<?php echo esc_attr( $field->value() ); ?>">