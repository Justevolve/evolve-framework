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

	/* Remove duplicate icon families. */
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

/**
 * Contents for the icon selection modal.
 *
 * @since 0.4.0
 */
function ev_icon_modal_load() {
	if ( ! ev_is_post_nonce_valid( 'ev_icon' ) ) {
		die();
	}

	if ( ! isset( $_POST['data'] ) ) {
		die();
	}

	$data = $_POST['data'];

	$prefix = isset( $data['prefix'] ) ? $data['prefix'] : '';
	$set    = isset( $data['set'] ) ? $data['set'] : '';
	$icon   = isset( $data['icon'] ) ? $data['icon'] : '';
	$color  = isset( $data['color'] ) ? $data['color'] : '';
	$size   = isset( $data['size'] ) ? $data['size'] : '';

	$icon_fonts = ev_get_icon_fonts();

	$content = '<div class="ev-icon-sets-external-wrapper ev-active">';

		$content .= '<div class="ev-icon-search-wrapper">';
			$content .= sprintf( '<input type="text" placeholder="%s" data-icon-search>', esc_attr( _x( 'Search&hellip;', 'icon search', 'ev_framework' ) ) );
			$content .= '<p class="ev-icon-search-results"></p>';
		$content .= '</div>';

		$content .= '<div class="ev-icon-sets-controls-external-wrapper">';

			$content .= '<div class="ev-icon-sets-controls-wrapper">';
				$content .= '<div class="ev-icon-sets-controls-field-wrapper">';
					$content .= sprintf( '<label>%s</label>', esc_html( __( 'Color', 'ev_framework' ) ) );
					$content .= ev_color( 'color', $color, false, false, false );
				$content .= '</div>';

				$content .= '<div class="ev-icon-sets-controls-field-wrapper">';
					$content .= sprintf( '<label>%s</label>', esc_html( __( 'Size', 'ev_framework' ) ) );
					$content .= sprintf( '<input type="text" name="size" value="%s" data-icon-size>', esc_attr( $size ) );
				$content .= '</div>';

				$content .= sprintf( '<input type="hidden" name="prefix" value="%s" data-icon-prefix>', esc_attr( $prefix ) );
				$content .= sprintf( '<input type="hidden" name="set" value="%s" data-icon-set>', esc_attr( $set ) );
				$content .= sprintf( '<input type="hidden" name="icon" value="%s" data-icon-name>', esc_attr( $icon ) );
			$content .= '</div>';

			$content .= '<div class="ev-icon-sets-preview-wrapper">';
				$content .= sprintf( '<span class="ev-icon-sets-preview-label">%s</span>', esc_html( __( 'Preview', 'ev_framework' ) ) );
				$content .= sprintf( '<span class="ev-selected-icon-preview ev-icon ev-component %s %s" style="color: %s;font-size: %s"></span>',
					esc_attr( $prefix ),
					esc_attr( $icon ),
					esc_attr( $color ),
					esc_attr( $size )
				);
			$content .= '</div>';

		$content .= '</div>';

		$content .= '<div class="ev-icon-sets-inner-wrapper">';
			$content .= '<div class="ev-icon-sets">';

				foreach ( $icon_fonts as $index => $font ) {
					$set_class = 'ev-on ev-icon-set-' . $font['name'];

					$content .= sprintf( '<div class="%s">', esc_attr( $set_class ) );
						$content .= sprintf( '<h2>%s</h2>', esc_html( $font['label'] ) );

						foreach ( $font['mapping'] as $set_icon ) {
							$icon_class = $font['prefix'] . ' ' . $set_icon . ' ev-icon ev-component';

							if ( $font['name'] == $set && $font['prefix'] == $prefix && $set_icon == $icon ) {
								$icon_class .= ' ev-selected';
							}

							$set_icon_stripped = strstr( $set_icon, '-' );

							$content .= sprintf( '<i data-prefix="%s" data-set="%s" data-icon-name="%s" data-icon-stripped="%s" class="%s" aria-hidden="true"></i>',
								esc_attr( $font['prefix'] ),
								esc_attr( $font['name'] ),
								esc_attr( $set_icon ),
								esc_attr( $set_icon_stripped ),
								esc_attr( $icon_class )
							);
						}
					$content .= '</div>';
				}

			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';

	$m = new Ev_SimpleModal( 'ev-icon', array( 'title' => __( 'Icon', 'ev_framework' ) ) );
	$m->render( $content );

	die();
}

add_action( 'wp_ajax_ev_icon_modal_load', 'ev_icon_modal_load' );