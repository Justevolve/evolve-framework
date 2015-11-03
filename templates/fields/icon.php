<?php
	$value  = $field->value();
	$set    = isset( $value['set'] ) ? $value['set'] : '';
	$icon   = isset( $value['icon'] ) ? $value['icon'] : '';
	$prefix = isset( $value['prefix'] ) ? $value['prefix'] : '';

	$wrapper_class = '';

	if ( empty( $icon ) ) {
		$wrapper_class .= ' ev-empty';
	}
?>

<div class="ev-selected-icon-wrapper <?php echo esc_attr( $wrapper_class ); ?>">
	<i data-preview class="<?php echo esc_attr( $prefix ); ?> <?php echo esc_attr( $icon ); ?> ev-icon ev-component" aria-hidden="true"></i>
	<span class="ev-remove ev-icon-remove"></span>
</div>

<input type="hidden" data-prefix name="<?php echo esc_attr( $field->handle() ); ?>[prefix]" value="<?php echo esc_attr( $prefix ); ?>">
<input type="hidden" data-set name="<?php echo esc_attr( $field->handle() ); ?>[set]" value="<?php echo esc_attr( $set ); ?>">
<input type="hidden" data-icon name="<?php echo esc_attr( $field->handle() ); ?>[icon]" value="<?php echo esc_attr( $icon ); ?>">