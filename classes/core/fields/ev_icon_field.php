<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Icon field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_IconField extends Ev_Field {

	/**
	 * Constructor for the icon field class.
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

		// $data['config'] = wp_parse_args( $data['config'], array(
		// 	'size' => ''
		// ) );

		parent::__construct( $data );
	}
}

/**
 * Add the icon field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_icon_field_type( $types ) {
	$types['icon'] = 'Ev_IconField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_icon_field_type' );

/**
 * Localize the icon field.
 *
 * @since 0.1.0
 */
function ev_icon_field_i18n() {
	wp_localize_script( 'jquery', 'ev_icon_field', array(
		'0' => _x( 'Nothing found', 'no icons found', 'ev_framework' ),
		'1' => _x( '%s found', 'one icon found', 'ev_framework' ),
		'2' => _x( '%s found', 'multiple icons found', 'ev_framework' ),
	) );
}

add_action( 'admin_enqueue_scripts', 'ev_icon_field_i18n' );