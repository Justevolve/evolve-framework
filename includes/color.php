<?php

/**
 * Delete a color preset.
 *
 * @since 0.4.0
 */
function ev_color_delete_preset() {
	if ( ! ev_is_post_nonce_valid( 'ev_color_delete_preset' ) ) {
		die();
	}

	$hex = isset( $_POST['hex'] ) ? sanitize_text_field( $_POST['hex'] ) : false;

	if ( ! $hex ) {
		die();
	}

	$key = 'color_presets';
	$presets = ev_get_option( $key );

	foreach ( $presets as $i => $preset ) {
		if ( isset( $preset['user'] ) && $preset['user'] == true && $preset['hex'] == $hex ) {
			unset( $presets[$i] );
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
	$name = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : $hex;

	if ( ! $hex ) {
		die();
	}

	$key = 'color_presets';
	$presets = ev_get_option( $key );

	$presets[] = array(
		'user'  => true,
		'hex'   => $hex,
		'label' => $name
	);

	ev_update_option( $key, $presets );

	die();
}

add_action( 'wp_ajax_ev_color_save_preset', 'ev_color_save_preset' );

/**
 * Add a body class to the admin body if we have saved at least one color
 * preset is available.
 *
 * @since 0.4.0
 * @param string $class The body class.
 * @return string
 */
function ev_color_presets_body_class( $class ) {
	$presets = ev_get_color_presets();

	if ( ! empty( $presets ) ) {
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

	return wp_list_filter( $presets, array( 'user' => true ) );
}

/**
 * Get a list of default color presets.
 *
 * @since 0.4.0
 * @return array
 */
function ev_get_default_color_presets() {
	$presets = ev_get_color_presets();

	return wp_list_filter( $presets, array( 'user' => true ), 'NOT' );
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
			$content .= '<div class="ev-color-user-presets">';
				$content .= sprintf( '<h3>%s</h3>', esc_html( __( 'User-defined presets', 'ev_framework' ) ) );

				if ( ! empty( $user_presets ) ) {
					foreach ( $user_presets as $preset ) {
						$content .= sprintf( '<span class="ev-color-preset ev-tooltip" data-hex="%s" data-title="%s" style="background-color: %s"><span data-nonce="%s" data-color-delete-preset>%s</span></span>',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
							isset( $preset['label'] ) ? esc_attr( $preset['label'] ) : '',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
							esc_attr( wp_create_nonce( 'ev_color_delete_preset' ) ),
							esc_html( __( 'Delete preset', 'ev_framework' ) )
						);
					}
				}
				else {
					$content .= __( "You haven't saved any color presets yet!", 'ev_framework' );
				}
			$content .= '</div>';

			/* Default presets */
			$content .= '<div class="ev-color-default-presets">';
				$content .= sprintf( '<h3>%s</h3>', esc_html( __( 'Default presets', 'ev_framework' ) ) );

				if ( ! empty( $default_presets ) ) {
					foreach ( $default_presets as $preset ) {
						$content .= sprintf( '<span class="ev-color-preset ev-tooltip" data-hex="%s" data-title="%s" style="background-color: %s"></span>',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : '',
							isset( $preset['label'] ) ? esc_attr( $preset['label'] ) : '',
							isset( $preset['hex'] ) ? esc_attr( $preset['hex'] ) : ''
						);
					}
				}
			$content .= '</div>';

		$content .= '</div>';
	$content .= '</div>';

	$m = new Ev_SimpleModal( 'ev-color-presets' );
	$m->render( $content );

	die();
}

add_action( 'wp_ajax_ev_color_presets_modal_load', 'ev_color_presets_modal_load' );






function ev_flat_ui_color_presets( $presets ) {
	$presets[] = array(
		'hex' => '#1abc9c',
		'label' => 'Turquoise'
	);

	$presets[] = array(
		'hex' => '#2ecc71',
		'label' => 'Emerald'
	);

	$presets[] = array(
		'hex' => '#3498db',
		'label' => 'Peter River'
	);

	return $presets;
}

add_filter( 'ev_color_presets', 'ev_flat_ui_color_presets' );