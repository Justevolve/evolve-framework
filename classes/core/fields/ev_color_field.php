<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Color field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
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

		$data['config'] = wp_parse_args( $data['config'], array(
			/* Allows for multiple colors properties. */
			'multiple' => false,

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

/**
 * Return the markup required to display a color palette.
 *
 * @since 0.4.0
 * @param array $palette The palette array.
 * @param string $value The current field value.
 * @return string
 */
function ev_color_field_palette_html( $palette, $value ) {
	if ( ! $palette ) {
		return '';
	}

	$palette = array_reverse( $palette, true );
	$palette[''] = __( 'Transparent', 'ev_framework' );
	$palette = array_reverse( $palette, true );

	$palette_html = '<ul class="ev-color-palette">';

	foreach ( $palette as $hex => $color_label ) {
		$color_class = $value == $hex ? 'ev-selected' : '';

		$palette_html .= sprintf( '<li class="ev-color-palette-variant ev-tooltip %s" style="background-color: %s" data-color="%s" data-title="%s"></li>',
			esc_attr( $color_class ),
			esc_attr( $hex ),
			esc_attr( $hex ),
			esc_attr( $color_label )
		);
	}

	$palette_html .= '</ul>';

	return $palette_html;
}
