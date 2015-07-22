<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Register a new image size.
 *
 * @since 0.1.0
 * @param string $label Image size label.
 * @param string     $handle   Image size identifier.
 * @param int        $width  Image width in pixels.
 * @param int        $height Image height in pixels.
 * @param bool|array $crop   Optional. Whether to crop images to specified height and width or resize.
 *                           An array can specify positioning of the crop area. Default false.
 */
function ev_add_image_size( $label, $handle, $width = null, $height = null, $crop = false ) {
	ev_fw()->media()->add_image_size( $label, $handle, $width, $height, $crop );
}

/**
 * Get a list of all the defined image sizes and their data.
 *
 * @since 0.1.0
 * @return array The list of all the defined image sizes and their data.
 */
function ev_get_image_sizes() {
	return ev_fw()->media()->get_image_sizes();
}

/**
 * Get a list of all the defined image sizes to be used in an HTML select
 * control.
 *
 * @since 0.1.0
 * @return array The list of all the defined image sizes in a key/value format.
 */
function ev_get_image_sizes_for_select() {
	$image_sizes = array();

	foreach ( ev_get_image_sizes() as $image_size => $data ) {
		$image_sizes[$image_size] = $data['label'];
	}

	return $image_sizes;
}

/**
 * Return the image size from its attachment ID.
 *
 * @since 0.1.0
 * @param integer|string $id The attachment ID.
 * @param string $size The desired image size; defaults to 'full'.
 * @return string The attachment ID image size URL.
 **/
function ev_fw_get_image( $id, $size = 'full' ) {
	if ( ! empty( $id ) && is_array( $image = wp_get_attachment_image_src( $id, $size ) ) ) {
		return esc_url( current( $image ) );
	}

	return '';
}