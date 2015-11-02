<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Load the imported icon fonts.
 *
 * @since 0.1.0
 */
function ev_load_icon_fonts() {
	foreach ( ev_get_icon_fonts() as $icon_font ) {
		$icon_font_name = 'ev-icon-font-' . $icon_font['name'];

		ev_fw()->admin()->add_style(
			$icon_font_name,
			$icon_font['url']
		);

		$add_to_frontend = apply_filters( 'ev_autoload_icon_fonts', false );

		if ( $add_to_frontend ) {
			ev_fw()->frontend()->add_style(
				$icon_font_name,
				$icon_font['url']
			);
		}
		else {
			ev_fw()->frontend()->register_style(
				$icon_font_name,
				$icon_font['url']
			);
		}
	}
}

add_action( 'init', 'ev_load_icon_fonts' );

/**
 * Return a list of the imported icon fonts to be used with the icon picker
 * field.
 *
 * To add a new icon font, hook into the `ev_get_icon_fonts` filter and append
 * the following array:
 *
 * $fonts[] = array(
 * 	 'name'    => Library name,
 *   'label'   => Library label,
 *   'url'     => URL TO THE ICON FONT CSS FILE,
 *   'prefix'  => '',  // Library CSS prefix, optional
 *   'mapping' => array(
 *   	'fa-envelope-o',
 *    	'fa-heart',
 *     	// other mappings
 * );
 *
 * @since 0.1.0
 * @return array
 */
function ev_get_icon_fonts() {
	$icon_fonts = apply_filters( 'ev_get_icon_fonts', array() );

	foreach ( $icon_fonts as $index => $icon_font ) {
		foreach ( $icon_fonts as $_i => $_if ) {
			if ( $icon_font['name'] == $_if['name'] && $_i !== $index ) {
				unset( $icon_fonts[$index] );
			}
		}
	}

	return $icon_fonts;
}

/**
 * Return the markup to display an icon.
 *
 * @since 0.4.0
 * @param string $icon The icon name.
 * @param array $attrs The icon attributes.
 * @return string
 */
function ev_get_icon( $icon, $attrs = array() ) {
	if ( empty( $icon ) ) {
		return;
	}

	$icon_fonts = ev_get_icon_fonts();
	$icon_classes = array(
		'ev-icon',
		$icon
	);

	foreach ( $icon_fonts as $index => $icon_font ) {
		if ( in_array( $icon, $icon_font['mapping'] ) ) {
			$icon_classes[] = $icon_font['prefix'];
			break;
		}
	}

	$icon_classes = array_map( 'esc_attr', $icon_classes );

	$attrs = wp_parse_args( $attrs, array(
		'class' => implode( ' ', $icon_classes )
	) );

	$attrs_html = '';

	foreach ( $attrs as $attr_key => $attr_value ) {
		$attrs_html .= ' ' . $attr_key . '="' . esc_attr( $attr_value ) . '"';
	}

	return sprintf( '<i %s></i>', $attrs_html );
}

/**
 * Display an icon.
 *
 * @since 0.4.0
 * @param string $icon The icon name.
 * @param array $attrs The icon attributes.
 */
function ev_icon( $icon, $attrs = array() ) {
	echo ev_get_icon( $icon, $attrs );
}