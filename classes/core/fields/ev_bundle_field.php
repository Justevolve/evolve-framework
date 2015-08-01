<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Bundle field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_BundleField extends Ev_Field {

	/**
	 * An array containing the fields data structure for the bundle field.
	 *
	 * @var array
	 */
	private $_fields = array();

	/**
	 * Constructor for the text field class.
	 *
	 * @since 0.1.0
	 * @param array $data The field data structure.
	 */
	public function __construct( $data )
	{
		parent::__construct( $data );

		$this->_fields = $data['fields'];
	}

	/**
	 * Render the field inner content.
	 *
	 * @since 0.1.0
	 * @param Ev_Field $field A field object.
	 */
	public function render_inner( $field = false )
	{
		$field_types = ev_field_types();
		$value = $field->value();

		foreach ( $this->_fields as $index => $field_data ) {
			$field_class = $field_types[$field_data['type']];
			$field_data['bundle'] = $field->handle();

			$fld = new $field_class( $field_data );

			if ( isset( $value[$field_data['handle']] ) ) {
				$fld->value( $value[$field_data['handle']] );
			}

			$fld->render();
		}
	}
}

/**
 * Add the bundle field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_bundle_field_type( $types ) {
	$types['bundle'] = 'Ev_BundleField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_bundle_field_type' );