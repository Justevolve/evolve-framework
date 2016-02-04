<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Media manager class.
 *
 * This class manages data concerning media, such as image sizes, both declared
 * by WordPress core and plugins or themes, screen densities, etc.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_MediaManager {

	/**
	 * An array containing information regarding the breakpoints that the theme or
	 * plugin manages.
	 *
	 * @var array
	 */
	private $_breakpoints = array();

	/**
	 * An array containing information regarding the densities that the theme or
	 * plugin manages.
	 *
	 * @var array
	 */
	private $_densities = array();

	/**
	 * An array containing meta data about the custom created image sizes.
	 *
	 * @var array
	 */
	private $_image_sizes_glossary = array();

	/**
	 * Contructor for the media manager controller class. This method declares
	 * the existence of a default screen density.
	 *
	 * @since 0.1.0
	 */
	public function __construct()
	{
		/* Adding a default screen density. */
		$this->add_density( '1', _x( 'Standard', 'standard screen density', 'ev_framework' ) );

		/* Adding a default screen width. */
		$this->add_breakpoint( 100, 'desktop', _x( 'Desktop', 'standard desktop media query', 'ev_framework' ), 'desktop' );
	}

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
	public function add_image_size( $label, $handle, $width = null, $height = null, $crop = false )
	{
		$width  = apply_filters( "ev_image_size_width[size:$handle]", $width );
		$height = apply_filters( "ev_image_size_height[size:$handle]", $height );
		$crop   = apply_filters( "ev_image_size_crop[size:$handle]", $crop );

		$this->_image_sizes_glossary[$handle] = array(
			'label' => $label
		);

		add_image_size( $handle, $width, $height, $crop );
	}

	/**
	 * Get a list of all the defined image sizes and their data.
	 *
	 * @since 0.1.0
	 * @return array The list of all the defined image sizes and their data.
	 */
	public function get_image_sizes()
	{
		$sizes = array();
		global $_wp_additional_image_sizes;

		$sizes = array(
			'full' => array(
				'width'  => true,
				'height' => true,
				'crop'   => false,
				'label' => __( 'Full size', 'ev_framework' )
			),
			'large' => array(
				'width'  => intval( get_option( 'large_size_w' ) ),
				'height' => intval( get_option( 'large_size_h' ) ),
				'crop'   => false,
				'label' => __( 'Large', 'ev_framework' )
			),
			'medium' => array(
				'width'  => intval( get_option( 'medium_size_w' ) ),
				'height' => intval( get_option( 'medium_size_h' ) ),
				'crop'   => false,
				'label' => __( 'Medium', 'ev_framework' )
			),
			'thumbnail' => array(
				'width'  => intval( get_option( 'thumbnail_size_w' ) ),
				'height' => intval( get_option( 'thumbnail_size_h' ) ),
				'crop'   => (bool) get_option( 'thumbnail_crop' ),
				'label' => __( 'Thumbnail', 'ev_framework' )
			),
		);

		if ( $_wp_additional_image_sizes ) {
			foreach ( $_wp_additional_image_sizes as $handle => $size ) {
				if ( isset( $this->_image_sizes_glossary[$handle] ) ) {
					$size = wp_parse_args( $this->_image_sizes_glossary[$handle], $size );
				}
				else {
					$size['label'] = $handle;
				}

				$sizes[$handle] = $size;
			}
		}

		return $sizes;
	}

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
	public function add_breakpoint( $order, $key, $label, $context, $media_query = '' )
	{
		$this->_breakpoints[$key] = array(
			'order'       => $order,
			'label'       => $label,
			'context'     => $context,
			'media_query' => $media_query,
		);
	}

	/**
	 * Return a list of the defined breakpoints.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_breakpoints()
	{
		$breakpoints = $this->_breakpoints;
		uasort( $breakpoints, array( $this, 'sort_breakpoints' ) );

		return $breakpoints;
	}

	/**
	 * Sort the defined breakpoints depending on their order.
	 *
	 * @since 0.1.0
	 * @param array $a The first breakpoint.
	 * @param array $b The second breakpoint.
	 * @return boolean
	 */
	private function sort_breakpoints( $a, $b )
	{
		if ( $a['order'] == $b['order'] ) {
			return 1;
		}

		return ( $a['order'] > $b['order'] ) ? -1 : 1;
	}

	/**
	 * Add a density definition.
	 *
	 * @since 0.1.0
	 * @param int|float $density The density coefficient.
	 * @param string $label The density label.
	 */
	public function add_density( $density, $label = '' )
	{
		if ( ! is_numeric( $density ) ) {
			return;
		}

		$density = (string) $density;

		if ( empty( $label ) ) {
			$label = $density . 'x';
		}

		$this->_densities[$density] = $label;
	}

	/**
	 * Return a list of the defined densities.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function get_densities()
	{
		$densities = $this->_densities;
		ksort( $densities );

		return $densities;
	}

}