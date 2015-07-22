<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Get contents from a template.
 *
 * @since 0.1.0
 * @param string $file The full path to the template file.
 * @param array $data The data array to be passed to the template.
 * @param boolean $echo True to echo the template part.
 * @return string
 */
function ev_template( $path, $data = array(), $echo = true ) {
	$path = ev_string_ensure_right( $path, '.php' );

	if ( file_exists( $path ) ) {
		extract( $data );

		ob_start();
		include $path;
		$content = ob_get_contents();
		ob_end_clean();

		if ( ! $echo ) {
			return $content;
		}
		else {
			echo $content;
		}
	}

	return '';
}

/**
 * Get contents from a partial template. If we're in a child theme, the
 * function will attempt to look for the resource in the child theme directory
 * first.
 *
 * @since 0.1.0
 * @param string $file The template file.
 * @param array $data The data array to be passed to the template.
 * @param boolean $echo True to echo the template part.
 * @return string
 */
function ev_get_template_part( $file, $data = array(), $echo = true ) {
	$file = ev_string_ensure_right( $file, '.php' );
	$path = locate_template( $file );

	return ev_template( $path, $data, $echo );
}