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

/**
 * Find a specific subkey in a multidimensional array.
 *
 * @since 0.4.0
 * @param array $array The haystack array.
 * @param string $path The search path.
 * @param mixed $default Default value when no results are found.
 * @param string $return Either 'value' or 'ids'.
 * @return mixed
 */
function ev_array_find( $array, $path = '', $default = null, $return = 'value' ) {
	$value = $default;
	$element = $array;
	$ids = array();

	$delimiter = '/';
	$comparison = ':';

	$path = trim( $path, $delimiter );

	if ( $path === '' ) {
		return $element;
	}

	$segments = explode( $delimiter, $path );
	$path_found = true;

	foreach ( $segments as $segment ) {
		$found = false;
		$search = strrpos( $segment, $comparison ) !== false;

		if ( $search ) {
			list( $sk, $sv ) = explode( $comparison, $segment );

			if ( array_key_exists( $sk, $element ) ) {
				if ( $sv == $element[$sk] ) {
					$found = true;
					$value = $sv;
					$element = $element[$sk];
					$ids[] = $sk;
				}
			}
			else {
				foreach ( $element as $ek => $ev ) {
					if ( is_array( $ev ) && array_key_exists( $sk, $ev ) ) {
						if ( $sv == $ev[$sk] ) {
							$found = true;
							$value = $ev;
							$element = $ev;
							$ids[] = $ek;
						}
					}
				}
			}
		}
		elseif ( array_key_exists( $segment, $element ) ) {
			$found = true;
			$value = $element[$segment];
			$element = $element[$segment];
			$ids[] = $segment;
		}
		else {
			$found = false;
		}

		$path_found = $path_found && $found;
	}

	if ( $return == 'value' ) {
		if ( $path_found ) {
			return $value;
		}
		else {
			return $default;
		}
	}

	return $ids;
}

/**
 * Add a value to a specific subkey in a multidimensional array.
 *
 * @since 0.4.0
 * @param array &$array The haystack array.
 * @param string $path The search path.
 * @param mixed $add The value to add.
 */
function ev_array_add( &$array, $path = '', $add = null ) {
	$value = null;
	$element = $array;

	$delimiter = '/';
	$comparison = ':';

	$find = ev_array_find( $array, $path, null, 'ids' );

	if ( ! empty( $find ) ) {
		$path = trim( $path, $delimiter );

		if ( $path === '' ) {
			return $element;
		}

		$path = array_shift( $find );

		if ( is_string( $path ) || is_integer( $path ) && array_key_exists( $path, $array ) ) {
			ev_array_add( $array[$path], implode( $delimiter, $find ), $add );

			if ( count( $find ) === 0 ) {
				if ( is_array( $add ) ) {
					foreach ( $add as $a ) {
						$array[$path][] = $a;
					}
				}
				else {
					$array[$path][] = $add;
				}
			}
		}
	}
}

/**
 * Update a value in a specific subkey in a multidimensional array.
 *
 * @since 0.4.0
 * @param array &$array The haystack array.
 * @param string $path The search path.
 * @param mixed $replacement The value to replace.
 */
function ev_array_update( &$array, $path = '', $replacement = null ) {
	$value = null;
	$element = $array;

	$delimiter = '/';
	$comparison = ':';

	$find = ev_array_find( $array, $path, null, 'ids' );

	if ( ! empty( $find ) ) {
		$path = trim( $path, $delimiter );

		if ( $path === '' ) {
			return $element;
		}

		$path = array_shift( $find );

		if ( is_string( $path ) || is_integer( $path ) && array_key_exists( $path, $array ) ) {
			ev_array_update( $array[$path], implode( $delimiter, $find ), $replacement );

			if ( count( $find ) === 0 ) {
				$array[$path] = $replacement;
			}
		}
	}
}

/**
 * Remove a specific subkey in a multidimensional array.
 *
 * @since 0.4.0
 * @param array &$array The haystack array.
 * @param string $path The search path.
 */
function ev_array_remove( &$array, $path = '' ) {
	$value = null;
	$element = $array;

	$delimiter = '/';
	$comparison = ':';

	$find = ev_array_find( $array, $path, null, 'ids' );

	if ( ! empty( $find ) ) {
		$path = trim( $path, $delimiter );

		if ( $path === '' ) {
			return $element;
		}

		$path = array_shift( $find );

		if ( is_string( $path ) || is_integer( $path ) && array_key_exists( $path, $array ) ) {
			ev_array_remove( $array[$path], implode( $delimiter, $find ) );

			if ( count( $find ) === 0 ) {
				unset( $array[$path] );
			}
		}
	}
}

/**
 * Check if a multi-dimensional array contains a particular subkey.
 *
 * @since 0.4.0
 * @param array $array The haystack array.
 * @param string $key The key to search for.
 * @return boolean
 */
function ev_check_multi_key_exists( $array, $key ) {
	if ( array_key_exists( $key, $array ) ) {
		return true;
	}

	foreach ( $array as $element ) {
		if ( is_array( $element ) && ev_check_multi_key_exists( $element, $key ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Escapes slashes in a multi-dimensional array.
 *
 * @since 0.4.0
 * @param array $arr The multi-dimensional array.
 * @return array
 */
function ev_escape_slashes_deep( $arr ) {
	foreach ( $arr as $k => $v ) {
		if ( ! is_array( $arr[$k] ) ) {
			$arr[$k] = str_replace( '\\', '\\\\', $arr[$k] );
		}
		else {
			$arr[$k] = ev_escape_slashes_deep( $arr[$k] );
		}
	}

	return $arr;
}