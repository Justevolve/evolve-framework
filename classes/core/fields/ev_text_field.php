<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Text field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_TextField extends Ev_Field {

	/**
	 * Constructor for the text field class.
	 *
	 * @since 0.1.0
	 * @param array $data The field data structure.
	 */
	public function __construct( $data )
	{
		if ( ! isset( $data['default'] ) ) {
			$data['default'] = '';
		}

		if ( ! isset( $data['config'] ) ) {
			$data['config'] = array();
		}

		$data['config'] = wp_parse_args( $data['config'], array(
			'style' => '',
			'size' => '',
			'full' => false,
			'link' => false
		) );

		parent::__construct( $data );
	}
}

/**
 * Add the text field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_text_field_type( $types ) {
	$types['text'] = 'Ev_TextField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_text_field_type' );
