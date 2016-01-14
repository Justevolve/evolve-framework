<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Generic controller class. A controller is an object that is entitled to
 * handle the loading of page external resources as well as routing operations.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
abstract class Ev_Controller {

	/**
	 * Registered scripts that will be enqueued and included in the page.
	 *
	 * @var array
	 */
	protected $_scripts = array();

	/**
	 * Registered styles that will be enqueued and included in the page.
	 *
	 * @var array
	 */
	protected $_styles = array();

	/**
	 * Registered scripts that will be removed from the page.
	 *
	 * @var array
	 */
	protected $_removed_scripts = array();

	/**
	 * Registered styles that will be removed from the page.
	 *
	 * @var array
	 */
	protected $_removed_styles = array();

	/**
	 * Add a script to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_script' and 'wp_enqueue_script', with the only differences
	 * being that the $src parameter isn't required and scripts are added in the
	 * footer by default; as such, the method accepts the very same set of
	 * parameters.
	 *
	 * If only the handle is provided, the script won't be registered as the
	 * controller is assuming it has already be previously by WordPress core or
	 * custom components.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_script
	 * @param string 	$handle 	Name of the script. Should be unique.
	 * @param string 	$src 		URL to the script.
	 * @param array 	$deps 		Array of the handles of all the registered scripts that this script depends on, that is, the scripts that must be loaded before this script.
	 * @param string 	$ver 		String specifying the script version number, if it has one.
	 * @param bool 		$in_footer 	If this parameter is true the script is placed at the bottom of the <body>.
	 */
	public function add_script( $handle, $src = null, $deps = array(), $ver = '', $in_footer = true )
	{
		$script_data = false;

		if ( $src ) {
			$deps[] = 'jquery';

			$script_data = array(
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
			);
		}

		$this->_scripts[$handle] = $script_data;
	}

	/**
	 * Add a stylesheet to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_style' and 'wp_enqueue_style', with the only differences
	 * being that the $src parameter isn't required; as such, the method accepts
	 * the very same set of parameters.
	 *
	 * If only the handle is provided, the stylesheet won't be registered as the
	 * controller is assuming it has already be previously by WordPress core or
	 * custom components.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @param string 	$handle 	Name of the stylesheet. Should be unique.
	 * @param string 	$src 		URL to the stylesheet.
	 * @param array 	$deps 		Array of the handles of all the registered stylesheets that this stylesheet depends on, that is, the stylesheets that must be loaded before this stylesheet.
	 * @param string 	$ver 		String specifying the stylesheet version number, if it has one.
	 * @param bool 		$media 		String specifying the media for which this stylesheet has been defined.
	 */
	public function add_style( $handle, $src = null, $deps = array(), $ver = '', $media = 'all' )
	{
		$style_data = false;

		if ( $src ) {
			$style_data = array(
				'src'   => $src,
				'deps'  => $deps,
				'ver'   => $ver,
				'media' => $media,
			);
		}

		$this->_styles[$handle] = $style_data;
	}

	/**
	 * Prevent a script from being registered or enqueued and included in the
	 * page.
	 *
	 * @since 0.1.0
	 * @param string 	$handle 	Name of the script. Should be unique.
	 */
	public function remove_script( $handle )
	{
		$this->_removed_scripts[] = $handle;
	}

	/**
	 * Prevent a style from being registered or enqueued and included in the
	 * page.
	 *
	 * @since 0.1.0
	 * @param string 	$handle 	Name of the style. Should be unique.
	 */
	public function remove_style( $handle )
	{
		$this->_removed_styles[] = $handle;
	}

	/**
	 * Register and enqueue or remove scripts in the page.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts()
	{
		/* Deregister scripts, if needed. */
		foreach ( $this->_removed_scripts as $handle ) {
			wp_deregister_script( $handle );
		}

		/* Deregister stylesheets, if needed. */
		foreach ( $this->_removed_styles as $handle ) {
			wp_deregister_style( $handle );
		}

		/* Register and enqueuing scripts in the page. */
		foreach ( $this->_scripts as $handle => $script_data ) {
			if ( $script_data['src'] ) {
				wp_register_script( $handle, $script_data['src'], $script_data['deps'], $script_data['ver'], $script_data['in_footer'] );
			}

			wp_enqueue_script( $handle );
		}

		/* Register and enqueuing stylesheets in the page. */
		foreach ( $this->_styles as $handle => $style_data ) {
			if ( $style_data['src'] ) {
				wp_register_style( $handle, $style_data['src'], $style_data['deps'], $style_data['ver'], $style_data['media'] );
			}

			wp_enqueue_style( $handle );
		}
	}

}