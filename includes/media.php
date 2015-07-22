<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Add a breakpoint definition.
 *
 * @since 0.1.0
 * @param int $order The breakpoint order.
 * @param string $key The breakpoint key.
 * @param string $label The breakpoint label.
 * @param string $context The breakpoint context (eg. 'mobile', 'tablet' or 'desktop').
 * @param string $media_query The breakpoint associated CSS media query.
 */
function ev_add_breakpoint( $order, $key, $label, $context, $media_query = '' ) {
	ev_fw()->media()->add_breakpoint( $order, $key, $label, $context, $media_query );
}

/**
 * Return a list of the defined breakpoints.
 *
 * @since 0.1.0
 * @return array
 */
function ev_get_breakpoints() {
	return ev_fw()->media()->get_breakpoints();
}

/**
 * Return a single defined breakpoint label.
 *
 * @since 0.1.0
 * @param string $breakpoint The breakpoint key.
 * @return string
 */
function ev_get_breakpoint_label( $key ) {
	$breakpoints = ev_get_breakpoints();

	if ( isset( $breakpoints[$key] ) && isset( $breakpoints[$key]['label'] ) ) {
		return $breakpoints[$key]['label'];
	}

	return $key;
}

/**
 * Add a density definition.
 *
 * @since 0.1.0
 * @param int|float $density The density coefficient.
 * @param string $label The density label.
 */
function ev_add_density( $density, $label = '' ) {
	ev_fw()->media()->add_density( $density, $label );
}

/**
 * Return a list of the defined densities.
 *
 * @since 0.1.0
 * @return array
 */
function ev_get_densities() {
	return ev_fw()->media()->get_densities();
}

/**
 * Return a single defined density label.
 *
 * @since 0.1.0
 * @param string $density The density value.
 * @return string
 */
function ev_get_density_label( $density ) {
	$densities = ev_get_densities();

	if ( isset( $densities[$density] ) ) {
		return $densities[$density];
	}

	return false;
}