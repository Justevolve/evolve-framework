<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Bundle field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
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
		if ( ! isset( $data['default'] ) ) {
			$data['default'] = array();
		}

		if ( ! isset( $data['config'] ) ) {
			$data['config'] = array();
		}

		$data['config'] = wp_parse_args( $data['config'], array(
			'style' => ''
		) );

		parent::__construct( $data );

		$this->_fields = $data['fields'];
	}

	/**
	 * Return the bundle style.
	 *
	 * @since 0.4.0
	 * @return string
	 */
	public function style()
	{
		return $this->_data['config']['style'];
	}

	/**
	 * Output custom content just after the field container has been printed.
	 *
	 * @since 0.4.0
	 */
	protected function _field_container_start()
	{
		if ( $this->style() === 'grid' ) {
			$field_types = ev_field_types();

			echo '<div class="ev-bundle-fields-wrapper-heading">';
				foreach ( $this->_fields as $index => $field_data ) {
					$field_class = $field_types[$field_data['type']];
					$fld = new $field_class( $field_data );

					$size = $fld->get_size();

					printf( '<div class="ev-field ev-field-size-%s">', esc_attr( $size ) );
						$fld->_render_label();
					echo '</div>';
				}
			echo '</div>';
		}
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
		$value = $this->value();
		$handle = $this->handle();

		if ( $field !== false ) {
			$value = $field->value();
			$handle = $field->handle();
		}

		echo '<div class="ev-bundle-fields-wrapper">';
			echo '<div class="ev-field-panel-controls-wrapper">';
				echo '<div class="ev-field-panel-controls-inner-wrapper">';
					echo '<span class="ev-repeatable-remove"></span>';
					echo '<span class="ev-sortable-handle"></span>';
				echo '</div>';
			echo '</div>';

			if ( ! ev_is_skipped_on_saving( $this->_type ) ) {
				$this->_render_repeatable_controls( 'prepend', 'medium' );
			}

			foreach ( $this->_fields as $index => $field_data ) {
				$field_class = $field_types[$field_data['type']];
				$field_data['bundle'] = $handle;

				$fld = new $field_class( $field_data );

				if ( isset( $value[$field_data['handle']] ) ) {
					$fld->value( $value[$field_data['handle']] );
				}

				$fld->render();
			}

			if ( ! ev_is_skipped_on_saving( $this->_type ) ) {
				$this->_render_repeatable_controls( 'append', 'medium' );
			}

		echo '</div>';
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

/**
 * Add a specific class to the bundle field according to its style.
 *
 * @since 0.1.0
 * @param array $types An array of CSS classes.
 * @return array
 */
function ev_bundle_field_classes( $classes, $field ) {
	$style = $field->style();

	if ( $style ) {
		$classes[] = 'ev-bundle-style-' . $style;
	}

	return $classes;
}

add_filter( 'ev_field_classes[type:bundle]', 'ev_bundle_field_classes', 10, 2 );
