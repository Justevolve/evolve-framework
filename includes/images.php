<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Allow SVG uploads.
 *
 * @since 0.4.0
 * @param array $mimes An array of MIME types.
 * @return array
 */
function ev_allow_svg_uploads( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';

	return $mimes;
}

add_filter( 'upload_mimes', 'ev_allow_svg_uploads' );

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
	return ev_get_image( $id, $size );
}

/**
 * Return the image size from its attachment ID.
 *
 * @since 0.4.0
 * @param integer|string $id The attachment ID.
 * @param string $size The desired image size; defaults to 'full'.
 * @return string The attachment ID image size URL.
 */
function ev_get_image( $id, $size = 'full' ) {
	if ( empty( $id ) ) {
		return '';
	}

	$image = wp_get_attachment_image_src( $id, $size );

	if ( ! is_array( $image ) ) {
		return '';
	}

	$image = trim( current( $image ) );

	return $image;
}

/**
 * Get the current post's featured image attachment ID.
 *
 * @param  int $post_id The post ID.
 * @return integer The attachment ID.
 */
function ev_get_featured_image_id( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return get_post_thumbnail_id( $post_id );
}

/**
 * Get the current post's featured image URL.
 *
 * @param  string $size The image size.
 * @param  int $post_id The post ID.
 * @return string
 */
function ev_get_featured_image( $size = 'full', $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	return ev_get_image( ev_get_featured_image_id( $post_id ), $size );
}

/**
 * Get the caption of an attachment in the Media Library.
 *
 * @since 1.0.0
 * @param integer $attachment_id The attachment ID.
 * @return string
 */
function ev_get_image_caption( $attachment_id ) {
	$image_data = get_post( $attachment_id );

	if ( $image_data ) {
		return $image_data->post_excerpt;
	}
	else {
		return '';
	}
}

/**
 * Get the description of an attachment in the Media Library.
 *
 * @since 1.0.0
 * @param integer $attachment_id The attachment ID.
 * @return string
 */
function ev_get_image_description( $attachment_id ) {
	$image_data = get_post( $attachment_id );

	if ( $image_data ) {
		return $image_data->post_content;
	}
	else {
		return '';
	}
}

/**
 * Return the markup for an image specifying a different srcset parameter according
 * to the available densities.
 *
 * @since 1.0.0
 * @param array $data The image data.
 * @param string $type The image size.
 * @param string $breakpoint The breakpoint name.
 * @return string
 */
function ev_get_density_image( $data, $size = 'full', $breakpoint = 'desktop' ) {
	if ( empty( $data[$breakpoint]['1']['id'] ) ) {
		return '';
	}

	$src = wp_get_attachment_image_url( $data[$breakpoint]['1']['id'], $size );

	if ( empty( $src ) ) {
		return '';
	}

	$srcset = array();
	$densities = ev_get_densities();

	array_shift( $densities );

	foreach ( $densities as $density => $label ) {
		$density_src = wp_get_attachment_image_url( $data[$breakpoint][$density]['id'], $size );

		if ( ! empty( $density_src ) ) {
			$srcset[] = $density_src . ' ' . $density . 'x';
		}
	}

	$args = array();

	if ( ! empty( $srcset ) ) {
		$args['srcset'] = esc_attr( implode( ', ', $srcset ) );
	}


	return wp_get_attachment_image( $data[$breakpoint]['1']['id'], $size, false, $args );
}

/**
 * Display the markup for an image specifying a different srcset parameter according
 * to the available densities.
 *
 * @since 1.0.0
 * @param array $data The image data.
 * @param string $type The image size.
 * @param string $breakpoint The breakpoint name.
 * @return string
 */
function ev_density_image( $data, $size = 'full', $breakpoint = 'desktop' ) {
	echo ev_get_density_image( $data, $size, $breakpoint );
}