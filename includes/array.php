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

/**
 * Search a subvalue in an array based on a collection of parameters.
 *
 * @since 0.4.0
 * @param $array array The array.
 * @param $args array The search parameters.
 * @return integer The index of the element. Return -1 if nothing was found.
 */
function ev_array_search_index( $array, $args = array() ) {
	if ( empty( $args ) ) {
		return -1;
	}

	foreach ( $array as $k => $v ) {
		$found = false;

		if ( is_array( $v ) ) {
			$found = count( array_intersect_assoc( $v, $args ) ) > 0;

			if ( $found ) {
				return (int) $k;
			}
		}
	}

	return -1;
}