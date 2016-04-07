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
 * @param boolean $echo Set to true to echo the select control.
 */
function ev_select( $name, $options, $selected = '', $style = '', $echo = true ) {
	$html = '';
	$style = (array) $style;
	$new_style = array();

	if ( ! empty( $style ) ) {
		$style_class = 'ev-select-style-';

		foreach ( $style as $s ) {
			$new_style[] = $style_class . $s;
		}
	}

	$new_style = implode( ' ', $new_style );

	$selected = strval( $selected );

	$html .= sprintf( '<span class="ev-select-wrapper %s">', esc_attr( $new_style ) );
		$html .= sprintf( '<select name="%s">', esc_attr( $name ) );
			foreach ( $options as $index => $option ) {
				$index = strval( $index );

				if ( ! is_array( $option ) ) {
					$selected_attr = $index == $selected ? 'selected' : '';
					$value = $index;
					$label = $option;

					$html .= sprintf( '<option %s value="%s">%s</option>', esc_attr( $selected_attr ), esc_attr( $value ), esc_html( $label ) );
				}
				else {
					$html .= sprintf( '<optgroup label="%s">',  esc_attr( $index ) );
						foreach ( $option as $o_k => $o_v ) {
							$selected_attr = $o_k == $selected ? 'selected' : '';

							$html .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $o_k ), esc_attr( $selected_attr ), esc_attr( $o_v ) );

						}
					$html .= '</optgroup>';
				}
			}
		$html .= '</select>';
	$html .= '</span>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Output an HTML multiple select control whose data is populated via an AJAX request.
 *
 * @since 0.4.0
 * @param string $name The multiple select control name attribute.
 * @param string $action The name of the action called to retrieve the data.
 * @param string $selected The multiple select selected values.
 * @param array $args The multiple select arguments.
 */
function ev_multiple_select_ajax( $name, $action, $selected = '', $args = array() ) {
	$class = 'ev-multiple-select ev-multiple-select-input-ajax';
	$value_field = isset( $args['value_field'] ) && ! empty( $args['value_field'] ) ? $args['value_field'] : 'id';
	$label_field = isset( $args['label_field'] ) && ! empty( $args['label_field'] ) ? $args['label_field'] : 'text';
	$search_field = isset( $args['search_field'] ) && ! empty( $args['search_field'] ) ? $args['search_field'] : 'text';

	$attrs = array(
		'data-action=' . $action,
		'data-value-field=' . $value_field,
		'data-label-field=' . $label_field,
		'data-search-field=' . $search_field,
		'data-nonce=' . wp_create_nonce( 'ev_multiple_select_ajax' )
	);

	if ( isset( $args['max'] ) && is_numeric( $args['max'] ) ) {
		$attrs[] = 'data-max=' . $args['max'];
	}

	if ( isset( $args['create'] ) && is_numeric( $args['create'] ) ) {
		$attrs[] = 'data-create=' . (int) $args['create'];
	}

	printf( '<select %s class="%s" name="%s">',
		implode( ' ', array_map( 'esc_attr', $attrs ) ),
		esc_attr( $class ),
		esc_attr( $name )
	);

	if ( $selected ) {
		$selected_data = $args['data_callback']( $selected );

		printf( '<option selected="selected" data-data="%s" value="%s"></option>',
			htmlspecialchars( json_encode( $selected_data ), ENT_QUOTES, 'UTF-8' ),
			esc_attr( $selected )
		);
	}

	echo '</select>';
}

/**
 * Output an HTML multiple select control.
 *
 * @since 0.2.0
 * @param string $name The multiple select control name attribute.
 * @param array $options An array containing the select options.
 * @param string $selected The multiple select selected values.
 * @param array $args The multiple select arguments.
 */
function ev_multiple_select( $name, $data, $selected = '', $args = array() ) {
	$class = 'ev-multiple-select ev-multiple-select-input';

	if ( isset( $args['vertical'] ) && $args['vertical'] === true ) {
		$class .= ' ev-multiple-select-vertical';
	}

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

	if ( isset( $args['max'] ) && is_numeric( $args['max'] ) ) {
		$attrs[] = 'data-max=' . $args['max'];
	}

	printf( '<input type="hidden" %s data-options="%s" class="%s" name="%s" value="%s">',
		implode( ' ', array_map( 'esc_attr', $attrs ) ),
		esc_attr( $data ),
		esc_attr( $class ),
		esc_attr( $name ),
		esc_attr( $selected )
	);
}

/**
 * Output an HTML radio control.
 *
 * @since 0.4.0
 * @param string $name The radio control name attribute.
 * @param array $data An array containing the radio options.
 * @param string $value The radio selected value.
 * @param boolean $style The radio control style.
 * @param boolean $echo Set to true to print the control.
 * @return string
 */
function ev_radio( $name, $data, $value = '', $style = '', $echo = true ) {
	$i=0;
	$html = '';
	$style = (array) $style;
	$new_style = array();

	if ( ! empty( $style ) ) {
		$style_class = 'ev-radio-style-';

		foreach ( $style as $s ) {
			$new_style[] = $style_class . $s;
		}
	}

	$new_style = implode( ' ', $new_style );

	$graphic = in_array( 'graphic', $style );

	$html .= sprintf( '<span class="ev-radio-wrapper %s">', esc_attr( $new_style ) );

	foreach ( $data as $k => $v ) {
		$checked = '';
		$class = '';

		if ( $value == $k || ( empty( $value ) && $i === 0 ) ) {
			$checked = 'checked';
		}

		if ( $graphic ) {
			$class = 'ev-graphic-radio';
		}

		$html .= sprintf( '<input id="%s" name="%s" type="radio" value="%s" %s>', esc_attr( $name ) . '-' . esc_attr( $k ), esc_attr( $name ), esc_attr( $k ), esc_attr( $checked ) );

		$html .= sprintf( '<label for="%s" class="ev-radio %s">', esc_attr( $name ) . '-' . esc_attr( $k ), esc_attr( $class ) );
			if ( $graphic ) {
				$image = $v;
				$label = '';

				if ( is_array( $v ) ) {
					$image = isset( $v['image'] ) && ! empty( $v['image'] ) ? $v['image'] : '';
					$label = isset( $v['label'] ) && ! empty( $v['label'] ) ? $v['label'] : '';
				}

				$html .= sprintf( '<img src="%s" title="%s">', esc_attr( $image ), esc_attr( $label ) );
			}
			else {
				$html .= sprintf( '<span>%s</span>', esc_html( $v ) );
			}
		$html .= '</label>';

		$i++;
	}

	$html .= '</span>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Output an HTML checkbox control.
 *
 * @since 0.4.0
 * @param string $name The checkbox control name attribute.
 * @param string $value The checkbox selected value.
 * @param string $style The checkbox control style.
 * @param boolean $echo Set to true to print the control.
 * @return string
 */
function ev_checkbox( $name, $value, $style = '', $args = array(), $echo = true ) {
	$checked = '';
	$html = '';
	$style = (array) $style;
	$new_style = array();

	if ( ! empty( $style ) ) {
		$style_class = 'ev-checkbox-style-';

		foreach ( $style as $s ) {
			$new_style[] = $style_class . $s;
		}
	}

	$new_style = implode( ' ', $new_style );

	if ( $value == 1 ) {
		$checked = 'checked';
	}

	$html .= sprintf( '<span class="ev-checkbox-wrapper %s">', esc_attr( $new_style ) );
		$html .= sprintf( '<input name="%s" type="hidden" value="0">', esc_attr( $name ) );
		$html .= sprintf( '<input %s id="%s" name="%s" type="checkbox" value="1" %s>',
			implode( ' ', array_map( 'esc_attr', $args ) ),
			esc_attr( $name ),
			esc_attr( $name ),
			esc_attr( $checked )
		);
		$html .= sprintf( '<label for="%s"></label>', esc_attr( $name ) );
	$html .= '</span>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}

/**
 * Output an HTML color control.
 *
 * @since 0.4.0
 * @param string $name The color control name attribute.
 * @param string $value The color selected value.
 * @param boolean $opacity The color control opacity value.
 * @param boolean $echo Set to true to print the control.
 * @return string
 */
function ev_color( $name, $value, $opacity = false, $style = '', $echo = true ) {
	$html          = '';
	$attrs         = '';
	$field_color   = $value;
	$field_opacity = '1';
	$style         = (array) $style;
	$new_style     = array();

	if ( ! empty( $style ) ) {
		$style_class = 'ev-color-style-';

		foreach ( $style as $s ) {
			$new_style[] = $style_class . $s;
		}
	}

	if ( is_array( $value ) ) {
		$field_color   = isset( $value['color'] ) ? $value['color'] : '';
		$field_opacity = isset( $value['opacity'] ) ? $value['opacity'] : '';
	}

	if ( ! empty( $field_color ) ) {
		$new_style[] = 'ev-color-can-be-saved';
	}

	$new_style = implode( ' ', $new_style );

	$html .= sprintf( '<span class="ev-color-wrapper %s">', esc_attr( $new_style ) );

		if ( $opacity ) {
			$attrs .= 'data-opacity=' . $field_opacity;

			$html .= sprintf( '<input type="hidden" data-input-color-opacity name="%s" value="%s">',
				esc_attr( $name . '[opacity]' ),
				esc_attr( $field_opacity )
			);
		}

		if ( $field_color ) {
			$attrs .= ' style=border-color:' . $field_color;
		}

		$html .= sprintf( '<input type="text" class="ev-color-input" name="%s" value="%s" %s>',
			esc_attr( $name . '[color]' ),
			esc_attr( $field_color ),
			esc_attr( $attrs )
		);

		$html .= '<div class="ev-color-controls-wrapper">';

			$html .= ev_btn(
				__( 'Presets', 'ev_framework' ),
				'action',
				array(
					'attrs' => array(
						'data-color-presets' => '',
						'data-nonce' => wp_create_nonce( 'ev_color_presets' ),
					),
					'style'     => 'text',
					'size' => 'medium',
					'echo'	=> false
				)
			);

			$html .= ev_btn(
				_x( 'Save', 'save color preset', 'ev_framework' ),
				'save',
				array(
					'attrs' => array(
						'data-color-save-preset' => '',
						'data-nonce' => wp_create_nonce( 'ev_color_save_preset' ),
					),
					'style'     => 'text',
					'size' => 'medium',
					'echo'	=> false
				)
			);

			// $html .= sprintf( '<a href="#" data-color-presets data-nonce="%s">%s</a>',
			// 	esc_attr( wp_create_nonce( 'ev_color_presets' ) ),
			// 	esc_html( __( 'Presets', 'ev_framework' ) )
			// );

			// $html .= sprintf( '<a href="#" data-color-save-preset data-nonce="%s">%s</a>',
			// 	esc_attr( wp_create_nonce( 'ev_color_save_preset' ) ),
			// 	esc_html( _x( 'Save', 'save color preset', 'ev_framework' ) )
			// );

		$html .= '</div>';

	$html .= '</span>';

	if ( $echo ) {
		echo $html;
	}

	return $html;
}