<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Generic fields container class.
 *
 * A field container is an object entitled to be a container for form fields in
 * meta boxes, other meta forms, or option pages.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

abstract class Ev_FieldsContainer {

	/**
	 * A slug-like definition of the fields container.
	 *
	 * @var string
	 */
	protected $_handle = '';

	/**
	 * A human-readable definition of the fields container. This string should
	 * usually be localized.
	 *
	 * @var string
	 */
	protected $_title = '';

	/**
	 * An array containing a default set of fields that belong to the container.
	 *
	 * @var array
	 */
	protected $_fields = array();

	/**
	 * Set to true if the groups should output a form tag as well.
	 *
	 * @var boolean
	 */
	protected $_groups_with_form = false;

	/**
	 * The tabs push method when the corresponding navigation element is clicked.
	 *
	 * @var string
	 */
	protected $_tabs_push_method = 'querystring';

	/**
	 * Constructor for the fields container class.
	 *
	 * @param string $handle A slug-like definition of the fields container.
	 * @param string $title A human-readable definition of the fields container.
	 * @param array $fields An array containing a default set of fields that belong to the container.
	 * @since 0.1.0
	 */
	function __construct( $handle, $title, $fields = array() )
	{
		$this->_handle = $handle;
		$this->_title = $title;
		$this->fields = $this->_add_fields( $fields );
	}

	/**
	 * Return the fields container handle.
	 *
	 * @since 0.1.0
	 * @return string A slug-like definition of the fields container.
	 */
	public function handle()
	{
		return $this->_handle;
	}

	/**
	 * Return the fields container title.
	 *
	 * @since 0.1.0
	 * @return string A human-readable definition of the fields container.
	 */
	public function title()
	{
		return $this->_title;
	}

	/**
	 * Add a series of fields to the fields container.
	 *
	 * @since 0.1.0
	 * @param array $fields An array of fields data.
	 */
	private function _add_fields( $fields = array() )
	{
		foreach ( $fields as $field ) {
			$this->add_field( $field );
		}
	}

	/**
	 * Add a field to the fields container.
	 *
	 * @since 0.1.0
	 * @param array $field The field data.
	 */
	public function add_field( $field = array() )
	{
		$this->_fields[] = $field;
	}

	/**
	 * Render a field in the fields container.
	 *
	 * @since 0.1.0
	 * @param array $element The field data.
	 */
	protected function render_field( $element )
	{
		$field_types = ev_field_types();
		$field_class = $field_types[$element['type']];
		$ev_field = new $field_class( $element );

		/* Set the field value, if necessary. */
		if ( isset( $element['handle'] ) ) {
			$ev_field->value( $this->set_field_value( $element['handle'] ) );
		}

		$ev_field->render();
	}

	/**
	 * Render a group of fields in the fields container.
	 *
	 * @since 0.1.0
	 * @param array $element The group data.
	 * @param integer $index The group index.
	 * @param integer $current_index The index of the current group.
	 */
	private function render_group( $group, $index, $current_index = 0 )
	{
		$class = '';

		if ( isset( $_GET['tab'] ) ) {
			if ( $_GET['tab'] === $group['handle'] ) {
				$class = 'ev-active';
			}
		}
		elseif ( $index == $current_index ) {
			$class = 'ev-active';
		}

		printf(
			'<div aria-labelledby="%s" id="ev-tab-%s" class="ev-tab %s" role="tabpanel">',
			esc_attr( $group['handle'] ),
			esc_attr( $group['handle'] ),
			esc_attr( $class )
		);

		$can_be_saved = false;

		if ( $this->_groups_with_form === true ) {
			foreach ( $group['fields'] as $index => $field ) {
				if ( ! ev_is_skipped_on_saving( $field['type'] ) ) {
					$can_be_saved = true;
					break;
				}
			}
		}

		if ( $can_be_saved && $this->_groups_with_form === true ) {
			printf(
				'<form method="%s" action="%s">',
				'post',
				admin_url( '/admin-ajax.php' )
			);

			printf( '<input type="hidden" name="group" value="%s">', esc_attr( $group['handle'] ) );
			printf( '<input type="hidden" name="context" value="%s">', esc_attr( $this->handle() ) );
		}

		foreach ( $group['fields'] as $index => $field ) {
			$this->render_field( $field );
		}

		if ( $can_be_saved && $this->_groups_with_form === true ) {
			$group_callback = 'ev_save_options_tab';

				echo '<div class="ev-form-submit-container">';

					ev_btn(
						__( 'Save', 'ev_framework' ),
						'save',
						array(
							'attrs' => array(
								'data-callback' => $group_callback,
								'type' => 'submit'
							),
							'size' => 'medium'
						)
					);

				echo '</div>';

			echo '</form>';
		}

		echo '</div>';
	}

	/**
	 * Render the elements navigation.
	 *
	 * @since 1.0.7
	 * @param array $elements An array of elements.
	 * @param string $current_key The array current key.
	 */
	public static function render_elements_nav( $elements, $current_key )
	{
		/* If the elements are grouped and we have more than one group, display their navigation. */
		echo '<ul class="ev-groups-nav ev-tabs-nav ev-vertical ev-align-left" role="tablist">';
			foreach ( $elements as $index => $element ) {
				$class = '';

				if ( isset( $_GET['tab'] ) ) {
					if ( $_GET['tab'] === $element['handle'] ) {
						$class = 'ev-active';
					}
				}
				elseif ( $index == $current_key ) {
					$class = 'ev-active';
				}

				printf(
					'<li><a id="%s" role="tab" aria-controls="ev-tab-%s" class="ev-tab-trigger %s" href="#%s">%s</a></li>',
					esc_attr( $element['handle'] ),
					esc_attr( $element['handle'] ),
					esc_attr( $class ),
					esc_attr( $element['handle'] ),
					esc_html( $element['label'] )
				);
			}
		echo '</ul>';
	}

	/**
	 * Render the fields container fields.
	 *
	 * @since 0.1.0
	 */
	protected function render_elements()
	{
		$elements = $this->elements();

		do_action( "ev_before_field_container_elements[handle:{$this->handle()}]" );

		if ( ! empty( $elements ) ) {
			$current_element = current( $elements );
			$current_key = key( $elements );
			$is_grouped = $current_element['type'] == 'group';
			$has_tabs = $is_grouped && count( $elements ) > 1;

			if ( $has_tabs ) {
				printf( '<div class="ev-tabs ev-component" data-push="%s">', esc_attr( $this->_tabs_push_method ) );

				self::render_elements_nav( $elements, $current_key );
			}

			echo '<div class="ev-tab-container">';

				if ( $is_grouped ) {
					foreach ( $elements as $index => $element ) {
						$this->render_group( $element, $index, $current_key );
					}
				}
				else {
					$this->render_group( array(
						'handle' => '_default',
						'fields' => $elements
					), 0, $current_key );
				}

			echo '</div>';

			if ( $has_tabs ) {
				echo '</div>';
			}
		}
	}

	/**
	 * Parse a fields container fields structure. This method ensures that
	 * the structure contains only the fields the current user actually has
	 * access to depending on the required capability.
	 *
	 * @since 0.1.0
	 * @param array $elements The fields container fields structure.
	 * @return array
	 */
	protected static function _parse_fields_structure( $elements )
	{
		foreach ( $elements as $index => $element ) {
			if ( ! ev_user_can_handle_data( $element ) ) {
				unset( $elements[$index] );
			}
			else {
				if ( isset( $element['fields'] ) && is_array( $element['fields'] ) ) {
					if ( empty( $element['fields'] ) ) {
						unset( $elements[$index] );
					}
					else {
						$elements[$index]['fields'] = self::_parse_fields_structure( $element['fields'] );

						if ( empty( $elements[$index]['fields'] ) ) {
							unset( $elements[$index] );
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Validate a fields container fields structure. This method ensures that
	 * the provided structure for the fields container doesn't lead to
	 * inconsistencies.
	 * If the validator fails, the fields container will display no fields at
	 * all.
	 *
	 * @since 0.1.0
	 * @param array $elements The fields container fields structure.
	 * @return boolean
	 */
	protected static function _validate_fields_structure( $elements )
	{
		$groups = 0;
		$messages = array();

		foreach ( $elements as $index => $element ) {
			if ( isset( $element['type'] ) && $element['type'] === 'group' && array_key_exists( 'fields', $element ) && is_array( $element['fields'] ) ) {
				$groups++;

				if ( ! array_key_exists( 'handle', $element ) || empty( $element['handle'] ) ) {
					$messages[] = 'Group is missing handle parameter.';
				}
				elseif ( ! array_key_exists( 'label', $element ) || empty( $element['label'] ) ) {
					$messages[] = sprintf( 'Group "%s": missing label.', $element['handle'] );
				}
				else {
					$field_messages = self::_validate_fields_structure( $element['fields'] );

					if ( $field_messages !== true ) {
						foreach ( $field_messages as $field_message ) {
							$messages[] = $field_message;
						}
					}
				}
			}
			else {
				$field_types = ev_field_types();
				$field_types_keys = array_keys( $field_types );

				if ( ! is_array( $element ) || empty( $element ) ) {
					/* Ensuring that the field data structure is valid. */
					$messages[] = 'Invalid field structure.';
				}
				elseif ( ! array_key_exists( 'type', $element ) || empty( $element['type'] ) ) {
					/* Ensuring that the field has a type. */
					if ( array_key_exists( 'handle', $element ) && ! empty( $element['handle'] ) ) {
						$messages[] = sprintf( 'Field "%s": missing type parameter.', $element['handle'] );
					}
					else {
						$messages[] = 'Field: missing type parameter.';
					}
				}
				elseif ( array_search( $element['type'], $field_types_keys, true ) === false ) {
					/* Ensuring that the field's type is valid. */
					if ( array_key_exists( 'handle', $element ) && ! empty( $element['handle'] ) ) {
						$messages[] = sprintf( 'Field "%s": invalid type.', $element['handle'] );
					}
					else {
						$messages[] = sprintf( '%s field: invalid type.', $element['type'] );
					}
				}
				elseif ( array_key_exists( 'capability', $element ) && empty( $element['capability'] ) ) {
					/* Ensuring that the field has a valid capability, if any. */
					if ( array_key_exists( 'handle', $element ) && ! empty( $element['handle'] ) ) {
						$messages[] = sprintf( 'Field "%s": invalid capability.', $element['handle'] );
					}
					else {
						$messages[] = sprintf( '%s field: invalid capability.', $element['type'] );
					}
				}
				else {
					$field_class = $field_types[$element['type']];

					$field_messages = call_user_func( array( $field_class, 'validate_structure' ), $element );

					if ( $field_messages !== true ) {
						foreach ( $field_messages as $field_message ) {
							$messages[] = $field_message;
						}
					}
				}
			}
		}

		if ( $groups > 0 && $groups !== count( $elements ) ) {
			return array(
				'All first-level elements must be field groups.'
			);
		}

		return ! empty( $messages ) ? $messages : true;
	}

	/**
	 * Display fields syntax errors.
	 *
	 * @since 0.2.0
	 * @param array $errors An array of error messages.
	 */
	protected static function _output_field_errors( $errors )
	{
		echo '<pre>';

		foreach ( $errors as $message ) {
			printf( "* %s\n", esc_html( $message ) );
		}

		echo '</pre>';
	}

	/**
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.1.0
	 * @return array An array of elements data.
	 */
	abstract public function elements();

	/**
	 * Display the fields container.
	 *
	 * @since 0.1.0
	 */
	abstract public function render();

   /**
	* Set the value for a specific field inside the container.
	*
	* @since 0.1.0
	* @return mixed The value of the field.
	*/
	abstract public function set_field_value();

}