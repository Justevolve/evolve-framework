<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Frontend interface class. This class is entitled to handle systemic
 * interactions with the frontend.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_FrontendInterface {

	/**
	 * Inline styles to be added to the <head> of the document.
	 *
	 * @var array
	 */
	protected $_inline_styles = array();

	/**
	 * Contructor for the frontend interface class.
	 *
	 * @since 0.1.0
	 */
	public function __construct()
	{
		/* Bind the display of inline styles. */
		add_action( 'wp_head', array( $this, 'inline_styles' ), 100 );
	}

	/**
	 * Append inline style to the <head> of the document.
	 *
	 * @since 0.1.0
	 * @param string $style The CSS to be added.
	 */
	public function add_inline_style( $style )
	{
		$this->_inline_styles[] = $style;
	}

	/**
	 * Display inline styles in the <head> of the document.
	 *
	 * @since 0.1.0
	 */
	public function inline_styles()
	{
		if ( empty( $this->_inline_styles ) ) {
			return;
		}

		echo '<style id="ev-fw-custom-css-css" type="text/css">';

			foreach ( $this->_inline_styles as $style ) {
				echo $style;
			}

		echo '</style>';
	}

}