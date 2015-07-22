<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Output an image upload control.
 *
 * @since 0.1.0
 * @param string $handle The image upload control name.
 * @param string $id ID of the selected attachment.
 * @param array $args An array of arguments for the control.
 */
function ev_image_upload( $handle, $id, $args = array() ) {
	$args = wp_parse_args( $args, array(
		'thumb_size'  => '',
		'density'     => '1',
		'breakpoint'  => 'desktop',
		'multiple'    => false,
		'sortable'    => false,
		'densities'   => array(),
		'breakpoints' => array(),
	) );

	$thumb_size  = $args['thumb_size'];
	$density     = $args['density'];
	$breakpoint  = $args['breakpoint'];
	$multiple    = (bool) $args['multiple'];
	$sortable    = (bool) $args['sortable'];
	$densities   = $args['densities'];
	$breakpoints = $args['breakpoints'];

	ev_template( EV_FRAMEWORK_TEMPLATES_FOLDER . 'fields/partials/image_upload', array(
		'id'          => $id,
		'handle'      => $handle,
		'density'     => $density,
		'breakpoint'  => $breakpoint,
		'thumb_size'  => $thumb_size,
		'multiple'    => $multiple,
		'sortable'    => $sortable,
		'densities'   => $densities,
		'breakpoints' => $breakpoints
	) );
}

/**
 * Output an HTML select control.
 *
 * @since 0.1.0
 * @param string $name The select control name attribute.
 * @param array $options An array containing the select options.
 * @param string $selected The select selected value.
 */
function ev_select( $name, $options, $selected = '' ) {
	printf( '<select name="%s">', esc_attr( $name ) );
		foreach ( $options as $index => $option ) {
			if ( ! is_array( $option ) ) {
				$selected_attr = $index == $selected ? 'selected' : '';
				$value = $index;
				$label = $option;

				printf( '<option %s value="%s">%s</option>', esc_attr( $selected_attr ), esc_attr( $value ), esc_html( $label ) );
			}
			else {
				printf( '<optgroup label="%s">',  esc_attr( $index ) );
					foreach ( $option as $o_k => $o_v ) {
						$selected_attr = $o_k == $selected ? 'selected' : '';

						printf( '<option value="%s" %s>%s</option>', esc_attr( $o_k ), esc_attr( $selected_attr ), esc_attr( $o_v ) );

					}
				echo '</optgroup>';
			}
		}
	echo '</select>';
}