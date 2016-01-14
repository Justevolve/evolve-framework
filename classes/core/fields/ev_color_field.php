<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Color field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
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

		$data['config'] = wp_parse_args( $data['config'], array(
			'style' => '',

			/* Allows for multiple colors properties. */
			'multiple' => false,

			/* Add opacity control */
			'opacity' => false,

			/* A palette of colors to choose from. */
			'palette' => false
		) );

		parent::__construct( $data );
	}

	/**
	 * Validate the field declaration structure.
	 *
	 * @static
	 * @since 0.2.0
	 * @param array $field The field declaration structure.
	 * @return boolean
	 */
	public static function validate_structure( $field )
	{
		$messages = array();

		if ( array_key_exists( 'config', $field ) && array_key_exists( 'multiple', $field['config'] ) ) {
			if ( ! is_array( $field['config']['multiple'] ) || empty( $field['config']['multiple'] ) ) {
				/* Ensuring that the field has a valid value for its style, if any. */
				$messages[] = sprintf( 'Field "%s": multiple option must be a non-empty array.', $field['handle'] );
			}
		}

		return apply_filters( "ev_field_validate_structure[type:color]", $messages, $field );
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