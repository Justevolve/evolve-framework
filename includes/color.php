<?php

/**
 * Convert a color from Hex to RGB.
 *
 * @since 0.4.0
 * @param string $hex The hex color code.
 * @return array
 */
function ev_color_hex_to_rgb( $hex ) {
	$hex = substr( $hex, 1 );

	if ( strlen( $hex ) === 3 ) {
		$long_hex = array();

		foreach ( str_split( $hex ) as $val ) {
			$long_hex[] = $val . $val;
		}

		$hex = $long_hex;
	}
	else {
		$hex = str_split( $hex, 2 );
	}

	return array_map( 'hexdec', $hex );
}

/**
 * Get a color YIQ value.
 *
 * @since 0.4.0
 * @param string $hex The hex color code.
 * @return float
 */
function ev_color_get_yiq( $hex ) {
	$rgb = ev_color_hex_to_rgb( $hex );

	return (($rgb[0]*299)+($rgb[1]*587)+($rgb[2]*114))/1000;
}

/**
 * Check if a color is bright.
 *
 * @since 0.4.0
 * @param string $hex The hex color code.
 * @return boolean
 */
function ev_color_is_bright( $hex ) {
	$yiq = ev_color_get_yiq( $hex );
	$threshold = (int) apply_filters( 'ev_color_is_bright_threshold', 204 ); // Based on #ccc

	return $yiq > $threshold;
}

/**
 * Delete a color preset.
 *
 * @since 0.4.0
 */
function ev_color_delete_preset() {
	if ( ! ev_is_post_nonce_valid( 'ev_color_delete_preset' ) ) {
		die();
	}

	$id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : false;

	if ( ! $id ) {
		die();
	}

	$key = 'color_presets';
	$presets = ev_get_option( $key );

	if ( ! isset( $presets['user'] ) ) {
		die();
	}

	foreach ( $presets['user'] as $i => $preset ) {
		if ( isset( $preset['id'] ) && $id == $preset['id'] ) {
			unset( $presets['user'][$i] );
			break;
		}
	}

	ev_update_option( $key, $presets );

	die();
}

add_action( 'wp_ajax_ev_color_delete_preset', 'ev_color_delete_preset' );

/**
 * Save a color preset.
 *
 * @since 0.4.0
 */
function ev_color_save_preset() {
	if ( ! ev_is_post_nonce_valid( 'ev_color_save_preset' ) ) {
		die();
	}

	$hex = isset( $_POST['hex'] ) ? sanitize_text_field( $_POST['hex'] ) : false;
	$id = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : false;
	$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : $hex;

	if ( ! $hex || ! $id ) {
		die();
	}

	$key = 'color_presets';
	$presets = ev_get_option( $key );

	if ( ! isset( $presets['user'] ) ) {
		$presets['user'] = array();
	}

	$presets['user'][] = array(
		'user'  => true,
		'hex'   => $hex,
		'label' => $name,
		'id'    => $id
	);

	ev_update_option( $key, $presets );

	die();
}

add_action( 'wp_ajax_ev_color_save_preset', 'ev_color_save_preset' );

/**
 * Check if there are color presets available.
 *
 * @since 0.4.0
 * @return boolean
 */
function ev_has_color_presets() {
	$presets = ev_get_color_presets();

	if ( empty( $presets ) ) {
		return false;
	}

	$empty = true;

	foreach ( $presets as $preset_key => $preset_colors ) {
		if ( ! empty( $preset_colors ) ) {
			$empty = false;
			break;
		}
	}

	return ! $empty;
}

/**
 * Add a body class to the admin body if we have saved at least one color
 * preset is available.
 *
 * @since 0.4.0
 * @param string $class The body class.
 * @return string
 */
function ev_color_presets_body_class( $class ) {
	if ( ev_has_color_presets() ) {
		$class .= ' ev-has-color-presets';
	}

	return $class;
}

add_filter( 'admin_body_class', 'ev_color_presets_body_class' );

/**
 * Get a list of color presets.
 *
 * A preset has the following form:
 * array(
 * 		'user' => false,
 * 		'hex' => '#ff0000',
 * 		'label' => 'The color label'
 * )
 *
 * @since 0.4.0
 * @return array
 */
function ev_get_color_presets() {
	$key = 'color_presets';
	$presets = ev_get_option( $key );

	if ( ! $presets ) {
		$presets = array();
	}

	$presets = apply_filters( 'ev_color_presets', $presets );

	return $presets;
}

/**
 * Get a list of user-defined color presets.
 *
 * @since 0.4.0
 * @return array
 */
function ev_get_user_color_presets() {
	$presets = ev_get_color_presets();

	return isset( $presets['user'] ) ? $presets['user'] : array();
}

/**
 * Get a list of default color presets.
 *
 * @since 0.4.0
 * @return array
 */
function ev_get_default_color_presets() {
	$presets = ev_get_color_presets();

	if ( isset( $presets['user'] ) ) {
		unset( $presets['user'] );
	}

	return $presets;
}

/**
 * Populate the color presets editing modal.
 *
 * @since 0.4.0
 */
function ev_color_presets_modal_load() {
	if ( ! ev_is_post_nonce_valid( 'ev_color_presets' ) ) {
		die();
	}

	$user_presets = ev_get_user_color_presets();
	$default_presets = ev_get_default_color_presets();

	$content = '';
	$content .= '<div class="ev-color-presets-manager-wrapper">';
		$content .= '<input type="hidden" name="hex" value="" data-hex-value-input>';

		$content .= '<div class="ev-color-presets-wrapper">';

			/* User presets */
			$user_presets_class = '';

			if ( ! empty( $user_presets ) ) {
				$user_presets_class .= 'ev-color-has-user-presets';
			}

			$content .= sprintf( '<div class="ev-color-user-presets %s">', $user_presets_class );
				$content .= sprintf( '<h3>%s</h3>', esc_html( __( 'User-defined presets', 'ev_framework' ) ) );

				if ( ! empty( $user_presets ) ) {
					foreach ( $user_presets as $index => $preset ) {
						$content .= sprintf( '<span data-id="%s" class="ev-color-preset ev-tooltip" data-hex="%s" data-title="%s" style="background-color: %s"><span data-nonce="%s" data-color-delete-preset><span class="screen-reader-text">%s</span></span></span>',
							isset( $preset['id'] ) ? esc_attr( $preset['id'] ) : '',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
							isset( $preset['label'] ) ? esc_attr( $preset['label'] ) : '',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
							esc_attr( wp_create_nonce( 'ev_color_delete_preset' ) ),
							esc_html( __( 'Delete preset', 'ev_framework' ) )
						);
					}
				}

				$content .= '<p class="ev-no-user-color-presets-warning">' . __( "You haven't saved any color presets yet!", 'ev_framework' ) . '</p>';
			$content .= '</div>';

			/* Default presets */
			$content .= '<div class="ev-color-default-presets">';
				$content .= sprintf( '<h3>%s</h3>', esc_html( __( 'Default presets', 'ev_framework' ) ) );

				if ( ! empty( $default_presets ) ) {
					foreach ( $default_presets as $set ) {
						$content .= sprintf( '<h4>%s</h4>', esc_html( $set['label'] ) );

						foreach ( $set['presets'] as $preset ) {
							$content .= sprintf( '<span class="ev-color-preset ev-tooltip" data-hex="%s" data-title="%s" style="background-color: %s"></span>',
								isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
								isset( $preset['label'] ) ? esc_attr( $preset['label'] ) : '',
								isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : ''
							);
						}
					}
				}
			$content .= '</div>';

		$content .= '</div>';
	$content .= '</div>';

	$m = new Ev_SimpleModal( 'ev-color-presets', array( 'title' => __( 'Color presets', 'ev_framework' ) ) );
	$m->render( $content );

	die();
}

add_action( 'wp_ajax_ev_color_presets_modal_load', 'ev_color_presets_modal_load' );

/**
 * Add custom colors to the TinyMCE interface.
 *
 * @since 0.4.0
 * @param array $init The TinyMCE initialization array.
 * @return array
 */
function ev_tiny_mce_custom_colors( $init ) {
	$presets = ev_get_user_color_presets();

	if ( empty( $presets ) ) {
		return $init;
	}

  	$default_colours = '"000000", "Black",
                      "993300", "Burnt orange",
                      "333300", "Dark olive",
                      "003300", "Dark green",
                      "003366", "Dark azure",
                      "000080", "Navy Blue",
                      "333399", "Indigo",
                      "333333", "Very dark gray",
                      "800000", "Maroon",
                      "FF6600", "Orange",
                      "808000", "Olive",
                      "008000", "Green",
                      "008080", "Teal",
                      "0000FF", "Blue",
                      "666699", "Grayish blue",
                      "808080", "Gray",
                      "FF0000", "Red",
                      "FF9900", "Amber",
                      "99CC00", "Yellow green",
                      "339966", "Sea green",
                      "33CCCC", "Turquoise",
                      "3366FF", "Royal blue",
                      "800080", "Purple",
                      "999999", "Medium gray",
                      "FF00FF", "Magenta",
                      "FFCC00", "Gold",
                      "FFFF00", "Yellow",
                      "00FF00", "Lime",
                      "00FFFF", "Aqua",
                      "00CCFF", "Sky blue",
                      "993366", "Red violet",
                      "FFFFFF", "White",
                      "FF99CC", "Pink",
                      "FFCC99", "Peach",
                      "FFFF99", "Light yellow",
                      "CCFFCC", "Pale green",
                      "CCFFFF", "Pale cyan",
                      "99CCFF", "Light sky blue",
                      "CC99FF", "Plum"';

    $custom_colours = '';

    foreach ( $presets as $preset ) {
    	$hex = str_replace( '#', '', $preset['hex'] );

    	$custom_colours .= sprintf( '"%s", "%s",', $hex, $preset['label'] );
    }

    $custom_colours = trim( $custom_colours, ',' );

    $rows = 5 + ( count( $presets ) % 6 ) + 1;

  // build colour grid default+custom colors
  $init['textcolor_map'] = '['.$default_colours.','.$custom_colours.']';

  // enable 6th row for custom colours in grid
  $init['textcolor_rows'] = $rows;

  return $init;
}

add_filter( 'tiny_mce_before_init', 'ev_tiny_mce_custom_colors' );