<?php
	$value  = $field->value();
	$handle = $field->handle();
	$set    = isset( $value['set'] ) ? $value['set'] : '';
	$icon   = isset( $value['icon'] ) ? $value['icon'] : '';
	$prefix = isset( $value['prefix'] ) ? $value['prefix'] : '';
	$color  = isset( $value['color'] ) ? $value['color'] : '';
	$size   = isset( $value['size'] ) ? $value['size'] : '';

	$wrapper_class = '';

	if ( empty( $icon ) ) {
		$wrapper_class .= ' ev-empty';
	}
?>

<div class="ev-selected-icon-wrapper <?php echo esc_attr( $wrapper_class ); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'ev_icon' ) ); ?>">
	<i data-preview style="color: <?php echo esc_attr( $color ); ?>" class="<?php echo esc_attr( $prefix ); ?> <?php echo esc_attr( $icon ); ?> ev-icon ev-component" aria-hidden="true"></i>
	<span class="ev-remove ev-icon-remove"></span>
</div>

<input type="hidden" data-prefix name="<?php echo esc_attr( $handle ); ?>[prefix]" value="<?php echo esc_attr( $prefix ); ?>">
<input type="hidden" data-set name="<?php echo esc_attr( $handle ); ?>[set]" value="<?php echo esc_attr( $set ); ?>">
<input type="hidden" data-icon name="<?php echo esc_attr( $handle ); ?>[icon]" value="<?php echo esc_attr( $icon ); ?>">
<input type="hidden" data-color name="<?php echo esc_attr( $handle ); ?>[color]" value="<?php echo esc_attr( $color ); ?>">
<input type="hidden" data-size name="<?php echo esc_attr( $handle ); ?>[size]" value="<?php echo esc_attr( $size ); ?>">