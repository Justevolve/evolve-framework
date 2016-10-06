<?php

$value       = $field->value();
$densities   = ev_get_densities();
$breakpoints = ev_get_breakpoints();
$thumb_size  = $field->config( 'thumb_size' );
$multiple    = $field->config( 'multiple' );
$sortable    = $field->config( 'sortable' );

$config_density     = $field->config( 'density' );
$config_breakpoints = $field->config( 'breakpoints' );
$manage_densities   = $config_density !== false;
$manage_breakpoints = $config_breakpoints !== false;
$image_densities    = array();
$image_breakpoints  = array();

if ( $manage_breakpoints ) {
	if ( is_array( $config_breakpoints ) ) {
		foreach ( $config_breakpoints as $breakpoint ) {
			if ( isset( $breakpoints[$breakpoint] ) ) {
				$image_breakpoints[$breakpoint] = $breakpoints[$breakpoint];
			}
		}
	}
	else {
		$image_breakpoints = $breakpoints;
	}
}
else {
	$image_breakpoints = array(
		'desktop' => $breakpoints['desktop']
	);
}

if ( $manage_densities ) {
	if ( is_array( $config_density ) ) {
		foreach ( $config_density as $density ) {
			if ( isset( $densities[$density] ) ) {
				$image_densities[$density] = $densities[$density];
			}
		}
	}
	else {
		$image_densities = $densities;
	}
}
else {
	$image_densities = array(
		'1' => $densities['1']
	);
}

if ( $multiple ) {
	$id = '';
	$density = '1';
	$breakpoint = 'desktop';

	if ( isset( $value[$breakpoint] ) && isset( $value[$breakpoint][$density] ) && isset( $value[$breakpoint][$density]['id'] ) ) {
		$id = $value[$breakpoint][$density]['id'];
	}

	/* If the field allows for multiple uploads, do not manage more than one density. */
	$args = array(
		'thumb_size' => $thumb_size,
		'multiple'   => true,
		'sortable'	 => $sortable
	);

	echo '<div class="ev-image-upload-container">';
		ev_image_upload( $field->handle(), $id, $args );
	echo '</div>';
}
else {
	foreach ( $image_breakpoints as $breakpoint_key => $breakpoint ) {
		foreach ( $image_densities as $density => $label ) {
			$id = '';

			if ( isset( $value[$breakpoint_key] ) && isset( $value[$breakpoint_key][$density] ) && isset( $value[$breakpoint_key][$density]['id'] ) ) {
				$id = $value[$breakpoint_key][$density]['id'];
			}

			$args = array(
				'thumb_size'  => $thumb_size,
				'multiple'    => false,
				'density'     => $density,
				'breakpoint'  => $breakpoint_key,
				'densities'   => $image_densities,
				'breakpoints' => $image_breakpoints,
			);

			echo '<div class="ev-image-upload-container">';

				ev_image_upload( $field->handle(), $id, $args );

				if ( count( $image_densities ) === 1 && $field->config( 'image_size' ) === true ) {
					echo '<p class="ev-image-upload-image-size-selection">';

						echo '<span>' . esc_html( __( 'Image size', 'ev_framework' ) ) . '</span>';

						$is_value = isset( $value[$breakpoint_key] ) && isset( $value[$breakpoint_key][1] ) && isset( $value[$breakpoint_key][1]['image_size'] ) ? $value[$breakpoint_key][1]['image_size'] : '';

						ev_select(
							$field->handle() . "[$breakpoint_key]" . '[1][image_size]',
							ev_get_image_sizes_for_select(),
							$is_value,
							'small'
						);

					echo '</p>';
				}

			echo '</div>';
		}
	}
}