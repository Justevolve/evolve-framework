<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Description field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_DescriptionField extends Ev_DividerField {

	/**
	 * Constructor for the description field class.
	 *
	 * @since 0.1.0
	 * @param array $data The field data structure.
	 */
	public function __construct( $data )
	{
		if ( ! isset( $data['config'] ) ) {
			$data['config'] = array();
		}

		$data['config'] = wp_parse_args( $data['config'], array(
			'style' => 'standard'
		) );

		parent::__construct( $data );
	}

	/**
	 * Validate the field declaration structure.
	 *
	 * @static
	 * @since 0.1.0
	 * @param array $field The field declaration structure.
	 * @return boolean
	 */
	public static function validate_structure( $field )
	{
		$messages = array();

		if ( ! array_key_exists( 'text', $field ) || empty( $field['text'] ) ) {
			/* Ensuring that the field has a valid text. */
			$messages[] = 'Field: missing text parameter.';
		}
		elseif ( array_key_exists( 'config', $field ) && array_key_exists( 'style', $field['config'] ) ) {
			$allowed_styles = array(
				// Standard message
				'standard',
				// Blue info notice
				'info',
				// Yellow important message
				'important'
			);
			$allowed_styles = apply_filters( 'ev_description_field_styles', $allowed_styles );

			if ( empty( $field['config']['style'] ) || ! in_array( $field['config']['style'], $allowed_styles ) ) {
				/* Ensuring that the field has a valid value for its style, if any. */
				$messages[] = sprintf( 'Field "%s": invalid style parameter.', $field['type'] );
			}
		}

		return apply_filters( "ev_field_validate_structure[type:description]", $messages, $field );
	}

}

/**
 * Add a specific class to the description field according to its style.
 *
 * @since 0.1.0
 * @param array $types An array of CSS classes.
 * @return array
 */
function ev_description_field_classes( $classes, $field ) {
	$classes[] = 'ev-description-style-' . $field->style();

	return $classes;
}

add_filter( 'ev_field_classes[type:description]', 'ev_description_field_classes', 10, 2 );

/**
 * Add the description field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_description_field_type( $types ) {
	$types['description'] = 'Ev_DescriptionField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_description_field_type' );

/**
 * Add the description field type to list of field types that must be ignored when
 * saving a meta box or a page.
 *
 * @since 0.1.0
 * @param array $types An array containing the field types that must be ignored when saving a meta box or a page.
 * @return array
 */
function ev_skip_description_on_saving( $types ) {
	$types[] = 'description';

	return $types;
}

add_filter( 'ev_skip_on_saving_field_types', 'ev_skip_description_on_saving' );