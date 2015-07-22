<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Color field class.
 *
 * @package   EvolveFramework
 * @since 	  1.0.0
 * @version   1.0.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_ColorField extends Ev_Field {

	/**
	 * Constructor for the color field class.
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

		parent::__construct( $data );
	}
}

/**
 * Add the color field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_color_field_type( $types ) {
	$types['color'] = 'Ev_ColorField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_color_field_type' );