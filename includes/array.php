<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Return a formatted string depending on an array of markers.
 *
 * @since 0.1.0
 * @param string $format The format string.
 * @param array $markers The array of markers to be replaced.
 * @return string
 */
function ev_sprintf_array( $format, $markers ) {
    return call_user_func_array( 'sprintf', array_merge( (array) $format, $markers ) );
}