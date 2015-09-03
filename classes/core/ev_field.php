<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Generic field class. A field is an object that that stores, sets and
 * retrieves data to and from the database and that has a specific visual
 * representation.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

abstract class Ev_Field {

	/**
	 * The field data structure.
	 *
	 * @var array
	 */
	protected $_data = array();

	/**
	 * A slug-like definition of the field type.
	 *
	 * @var string
	 */
	protected $_type = '';

	/**
	 * A slug-like definition of the field handle.
	 *
	 * @var string
	 */
	private $_handle = '';

	/**
	 * The field data value.
	 *
	 * @var mixed
	 */
	private $_value = false;

	/**
	 * The field default data value.
	 *
	 * @var mixed
	 */
	private $_default = false;

	/**
	 * A brief definition of the field control function.
	 *
	 * @var string
	 */
	private $_label = false;

	/**
	 * An help text of the field control function.
	 *
	 * @var mixed
	 */
	private $_help = false;

	/**
	 * The size of the field. Accepted values:
	 * - 'full'
	 * - 'large'
	 * - 'medium'
	 * - 'small'
	 *
	 * @var string
	 */
	private $_size = 'full';

	/**
	 * Set to TRUE if the field should be displayed on its own row.
	 *
	 * @var boolean
	 */
	private $_break = false;

	/**
	 * The optional configuration array for the field.
	 *
	 * @var array
	 */
	private $_config = array();

	/**
	 * The handle of the bundle the field belongs to, if any.
	 *
	 * @var string
	 */
	private $_bundle = false;

	/**
	 * Set to TRUE if the field is repeatable.
	 *
	 * @var boolean
	 */
	private $_repeatable = false;

	/**
	 * Constructor for the field class.
	 *
	 * @param array $data The field data structure.
	 * @since 0.1.0
	 */
	function __construct( $data = array() )
	{
		$this->_data = $data;
		$this->_type = $this->_data['type'];

		if ( isset( $this->_data['handle'] ) ) {
			$this->_handle = $this->_data['handle'];
		}

		if ( isset( $this->_data['bundle'] ) ) {
			$this->_bundle = $this->_data['bundle'];
		}

		if ( isset( $this->_data['repeatable'] ) && ( $this->_data['repeatable'] === true || is_array( $this->_data['repeatable'] ) ) ) {
			if ( is_array( $this->_data['repeatable'] ) ) {
				$this->_data['repeatable'] = wp_parse_args( $this->_data['repeatable'], array(
					'sortable' => false,
					'append'   => true
				) );
			}

			$this->_repeatable = $this->_data['repeatable'];
		}

		if ( isset( $this->_data['default'] ) ) {
			$this->default_value( $this->_data['default'] );
		}

		if ( isset( $this->_data['label'] ) ) {
			$this->label( $this->_data['label'] );
		}

		if ( isset( $this->_data['help'] ) ) {
			$this->help( $this->_data['help'] );
		}

		if ( isset( $this->_data['size'] ) && ! empty( $this->_data['size'] ) ) {
			$this->_size = $this->_data['size'];
		}

		if ( isset( $this->_data['break'] ) ) {
			$this->_break = (bool) $this->_data['break'];
		}

		if ( isset( $this->_data['config'] ) ) {
			$this->_config = $this->_data['config'];
		}
	}

	/**
	 * Set the field default data value.
	 *
	 * @since 0.1.0
	 * @param mixed $default The field default data value.
	 */
	private function set_default( $default )
	{
		$this->_default = $default;
	}

	/**
	 * Get the field default data value.
	 *
	 * @since 0.1.0
	 * @return mixed
	 */
	private function get_default()
	{
		return $this->_default;
	}

	/**
	 * Set the field label.
	 *
	 * @since 0.1.0
	 * @param string $label The field label.
	 */
	private function set_label( $label )
	{
		$label_types = array(
			'inline',
			'block',
			'hidden'
		);

		$field_label = array(
			'type' => 'inline',
			'text' => ''
		);

		if ( is_string( $label ) ) {
			$field_label['text'] = $label;
		}
		elseif ( is_array( $label ) ) {
			if ( isset( $label['type'] ) && in_array( $label['type'], $label_types ) ) {
				$field_label['type'] = $label['type'];
			}

			if ( isset( $label['text'] ) ) {
				$field_label['text'] = $label['text'];
			}
		}
		else {
			$field_label = false;
		}

		$this->_label = $field_label;
	}

	/**
	 * Get the field label.
	 *
	 * @since 0.1.0
	 * @return string
	 */
	private function get_label()
	{
		return $this->_label;
	}

	/**
	 * Set the field help text.
	 *
	 * @since 0.1.0
	 * @param array|string $help The field help text.
	 */
	private function set_help( $help )
	{
		$help_types = array(
			'inline',
			'tooltip',
			// 'popup'
		);

		$field_help = array(
			'type' => 'inline',
			'text' => ''
		);

		if ( is_string( $help ) ) {
			$field_help['text'] = $help;
		}
		elseif ( is_array( $help ) ) {
			if ( isset( $help['type'] ) && in_array( $help['type'], $help_types ) ) {
				$field_help['type'] = $help['type'];
			}

			if ( isset( $help['text'] ) ) {
				$field_help['text'] = $help['text'];
			}
		}
		else {
			$field_help = false;
		}

		$this->_help = $field_help;
	}

	/**
	 * Get the field help text.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	private function get_help()
	{
		return $this->_help;
	}

	/**
	 * Set or retrieve the field label.
	 *
	 * @since 0.1.0
	 * @param boolean|string $label A brief definition of the field control function.
	 * @return string
	 */
	public function label( $label = false )
	{
		if ( $label === false ) {
			return $this->get_label();
		}
		else {
			$this->set_label( $label );
		}
	}

	/**
	 * Set or retrieve the field help text.
	 *
	 * @since 0.1.0
	 * @param boolean|array $help An help text of the field control function.
	 * @return array
	 */
	public function help( $help = false )
	{
		if ( $help === false ) {
				return $this->get_help();
			}
			else {
				$this->set_help( $help );
			}
	}

	/**
	 * Set or retrieve the field default data value.
	 *
	 * @since 0.1.0
	 * @param mixed|boolean $default The field default data value.
	 * @return mixed|void
	 */
	public function default_value( $default = false )
	{
		if ( $default === false ) {
			return $this->get_default();
		}
		else {
			$this->set_default( $default );
		}
	}

	/**
	 * Set the field data value.
	 *
	 * @since 0.1.0
	 * @param mixed $value The field data value.
	 */
	private function set_value( $value )
	{
		$this->_value = $value;
	}

	/**
	 * Get the field data value.
	 *
	 * @since 0.1.0
	 * @return mixed
	 */
	private function get_value()
	{
		if ( $this->_value === false ) {
			if ( $this->default_value() !== false ) {
				return $this->default_value();
			}
		}

		return $this->_value;
	}

	/**
	 * Set or retrieve the field data value.
	 *
	 * @since 0.1.0
	 * @param mixed|boolean $value The field data value.
	 * @return mixed|void
	 */
	public function value( $value = false )
	{
		if ( $value === false ) {
			return $this->get_value();
		}
		else {
			$this->set_value( $value );
		}
	}

	/**
	 * Return a set of CSS classes to be applied when the field is rendered to
	 * screen.
	 *
	 * @since 0.1.0
	 * @return array An array of CSS classes.
	 */
	private function classes()
	{
		$classes = array(
			'ev-field',
			'ev-field-' . $this->_type,
			'ev-field-size-' . $this->_size
		);

		if ( $this->_break === true ) {
			$classes[] = 'ev-field-break';
		}

		if ( $this->_repeatable !== false ) {
			$classes[] = 'ev-repeatable';

			if ( isset( $this->_repeatable['sortable'] ) && $this->_repeatable['sortable'] === true ) {
				$classes[] = 'ev-sortable';
			}

			if ( isset( $this->_repeatable['append'] ) && $this->_repeatable['append'] === false ) {
				$classes[] = 'ev-repeatable-prepend';
			}
		}

		$classes = apply_filters( "ev_field_classes[type:{$this->_type}]", (array) $classes, $this );

		return array_map( 'esc_attr', $classes );
	}

	/**
	 * Return the field handle.
	 *
	 * @since 0.1.0
	 * @return string The field handle.
	 */
	public function handle()
	{
		$handle = $this->_handle;

		if ( $this->_bundle !== false ) {
			if ( $this->_repeatable === false ) {
				$handle = str_replace( '[', '][', $handle );
				$handle = ev_string_ensure_left( $handle, '[' );
				$handle = ev_string_ensure_right( $handle, ']' );

				$handle = sprintf( '%s%s', $this->_bundle, $handle );
			}
			else {
				$handle = sprintf( '%s[%s][]', $this->_bundle, $this->_handle );
			}
		}
		elseif ( $this->_repeatable !== false ) {
			$handle .= '[]';
		}

		return $handle;
	}

	/**
	 * Return the field configuration array.
	 *
	 * @since 0.1.0
	 * @param string $key A specific configuration key to be retrieved.
	 * @return string The field configuration value.
	 */
	public function config( $key )
	{
		$config = '';

		if ( isset( $this->_config[$key] ) ) {
			$config = $this->_config[$key];
		}

		return $config;
	}

	/**
	 * Render the field repeatable controls.
	 *
	 * @since 0.1.0
	 */
	private function _render_repeatable_controls()
	{
		$count = count( $this->value() );

		printf( '<div class="ev-repeatable-controls" data-key="%s" data-count="%s">', esc_attr( $this->_handle ), esc_attr( $count ) );
			if ( isset( $this->_repeatable['controls'] ) && ! empty( $this->_repeatable['controls'] ) ) {
				foreach ( $this->_repeatable['controls'] as $action => $label ) {
					$controls[] = printf( '<a href="#" data-action="%s" class="ev-repeat">%s</a>', esc_attr( $action ), esc_html( $label ) );
				}
			}
			else {
				printf( '<a href="#" class="ev-repeat">%s</a>', esc_html( __( 'Add', 'ev_framework' ) ) );
			}

			/* Print the repeatable field template for later use. */
			$this->repeatable_template();
		echo '</div>';

		if ( isset( $this->_repeatable['empty_state'] ) && $this->_repeatable['empty_state'] !== '' ) {
			echo '<div class="ev-empty-state">';
				$empty_state = esc_html( $this->_repeatable['empty_state'] );
				$controls = array();

				foreach ( $this->_repeatable['controls'] as $action => $label ) {
					$controls[] = sprintf( '<a href="#" data-action="%s" class="ev-repeat">%s</a>', esc_attr( $action ), esc_html( $label ) );
				}

				$empty_state = ev_sprintf_array( $empty_state, $controls );

				echo $empty_state;
			echo '</div>';
		}
	}

	/**
	 * Render the field label.
	 *
	 * @since 0.1.0
	 */
	private function _render_label()
	{
		$label = $this->label();

		if ( $label != false && $label['text'] != '' ) {
			printf( '<label class="ev-label">%s</label>', esc_html( $label['text'] ) );
		}
	}

	/**
	 * Render the field help text.
	 *
	 * @since 0.1.0
	 */
	private function _render_help()
	{
		$help = $this->help();

		if ( $help !== false && $help['text'] != '' ) {
			printf( '<div class="ev-help ev-help-%s">', esc_attr( $help['type'] ) );
				switch( $help['type'] ) {
					case 'tooltip':
						printf( '<div href="#" class="ev-help-handle ev-tooltip" title="%s"><span>%s</span></div>', esc_attr( $help['text'] ), __( 'Need help?', 'ev_framework' ) );
						break;
					// case 'popup':
					// 	printf( '<div href="#" class="ev-help-handle"><span>%s</span></div>', __( 'Need help?', 'ev_framework' ) );
					// 	break;
					case 'inline':
					default:
						$help_text = wp_kses( $help['text'], array(
							'code' => array(),
							'strong' => array(),
							'b' => array()
						) );

						echo $help_text;
						break;
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
		if ( $field === false ) {
			$field = $this;
		}

		echo '<div class="ev-field-inner">';
			echo '<span class="ev-sortable-handle"></span>';

			$template = EV_FRAMEWORK_TEMPLATES_FOLDER . "fields/{$this->_type}";
			$template = apply_filters( "ev_field_template[type:{$this->_type}]", $template );

			ev_template( $template, array(
				'field' => $field
			) );

			echo '<span class="ev-repeatable-remove"></span>';
		echo '</div>';
	}

	/**
	 * Render the field inner content in the event that the field is listed as
	 * repeatable.
	 *
	 * @since 0.1.0
	 */
	private function _render_inner_repeatable()
	{
		/* Retrieve the field value and cast it to array so that we can count how many times the field needs to be repeated. */
		$values = (array) $this->value();

		if ( empty( $values ) || isset( $values[0] ) && empty( $values[0] ) ) {
			return;
		}

		$class = get_class( $this );
		$field = null;
		$index = 0;

		foreach ( $values as $value ) {
			$field_data = $this->_data;
			$field_data['repeatable'] = false;

			if ( ! isset( $field_data['handle'] ) ) {
				$field_data['handle'] = '';
			}

			$field_data['handle'] .= '[' . $index . ']';

			/* For each repeated field, create an instance and render its inner content. */
			$field = new $class( $field_data );
			$field->value( $value );

			$this->render_inner( $field );
			$index++;
		}
	}

	/**
	 * Render the field template when it is set to be repeatable.
	 *
	 * @since 0.1.0
	 */
	public function repeatable_template()
	{
		$class = get_class( $this );
		$field = new $class( $this->_data );

		printf( '<script type="text/template" data-template="%s">', esc_attr( $this->_handle ) );
			$this->render_inner( $field );
		echo '</script>';
	}

	/**
	 * Render the field interface.
	 *
	 * @since 0.1.0
	 */
	public function render()
	{
		$label = $this->label();

		printf( '<div class="%s">', esc_attr( implode( ' ', $this->classes() ) ) );
			echo '<div class="ev-field-header ev-field-header-label-' . esc_attr( $label["type"] ) . '">';
				$this->_render_label();
				$this->_render_help();
			echo '</div>';

			$values = (array) $this->value();
			$container_class = '';

			if ( isset( $this->_repeatable['empty_state'] ) && $this->_repeatable['empty_state'] !== '' ) {
				if ( empty( $values ) || isset( $values[0] ) && empty( $values[0] ) ) {
					$container_class .= ' ev-container-empty';
				}
			}

			printf( '<div class="ev-container %s">', esc_attr( $container_class ) );
				if ( $this->_repeatable !== false ) {
					if ( ! ev_is_skipped_on_saving( $this->_type ) ) {
						$this->_render_repeatable_controls();
					}
					$this->_render_inner_repeatable();
				}
				else {
					$this->render_inner();
				}
			echo '</div>';
		echo '</div>';
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
		$field_types = array_keys( ev_field_types() );

		if ( ! is_array( $field ) || empty( $field ) ) {
			/* Ensuring that the field data structure is valid. */
			$messages[] = 'Invalid field structure.';
		}
		elseif ( ! array_key_exists( 'handle', $field ) || empty( $field['handle'] ) ) {
			/* Ensuring that the field has a valid handle. */
			$messages[] = 'Field is missing handle parameter.';
		}
		elseif ( ! array_key_exists( 'type', $field ) || empty( $field['type'] ) ) {
			/* Ensuring that the field has a type. */
			$messages[] = sprintf( 'Field "%s": missing type parameter.', $field['handle'] );
		}
		elseif ( array_search( $field['type'], $field_types, true ) === false ) {
			/* Ensuring that the field's type is valid. */
			$messages[] = sprintf( 'Field "%s": invalid type.', $field['handle'] );
		}
		elseif ( ! array_key_exists( 'label', $field ) || empty( $field['label'] ) ) {
			/* Ensuring that the field has a valid label. */
			$messages[] = sprintf( 'Field "%s": missing label parameter.', $field['handle'] );
		}
		elseif ( array_key_exists( 'fields', $field ) && ! is_array( $field['fields'] ) ) {
			/* Ensuring that the field has a valid set of fields, if any. */
			$messages[] = sprintf( 'Field "%s": subfields must be in array form.', $field['handle'] );
		}
		elseif ( array_key_exists( 'config', $field ) && ! is_array( $field['config'] ) ) {
			/* Ensuring that the field has a valid set of configuration options, if any. */
			$messages[] = sprintf( 'Field "%s": config must be in array form.', $field['handle'] );
		}
		elseif ( array_key_exists( 'repeatable', $field ) && ( ! is_array( $field['repeatable'] ) && ! is_bool( $field['repeatable'] ) ) ) {
			/* Ensuring that the field has a valid repeatable configuration. */
			$messages[] = sprintf( 'Field "%s": repeatable parameter must be in array/boolean form.', $field['handle'] );
		}
		elseif ( array_key_exists( 'size', $field ) ) {
			$allowed_sizes = array( 'full', 'large', 'medium', 'small' );

			if ( ! empty( $field['size'] ) && ! in_array( $field['size'], $allowed_sizes ) ) {
				/* Ensuring that the field has a valid value for its size, if any. */
				$messages[] = sprintf( 'Field "%s": invalid size parameter.', $field['handle'] );
			}
		}

		$messages = apply_filters( "ev_field_validate_structure[type:{$field['type']}]", $messages, $field );

		return ! empty( $messages ) ? $messages : true;
	}

	/**
	 * Sanitize the field value upon form submission.
	 *
	 * @since 0.1.0
	 * @param array $field The field data structure.
	 * @param mixed $value The field submitted value.
	 * @return mixed The sanitized field value.
	 */
	public static function sanitize( $field, $value )
	{
		$value = stripslashes_deep( $value );
		$value = apply_filters( "ev_sanitize_field[type:{$field['type']}]", $value );

		if ( ! array_key_exists( 'sanitize', $field ) ) {
			return $value;
		}

		$sanitize_function = 'ev_sanitize_' . $field['sanitize'];

		if ( function_exists( $sanitize_function ) ) {
			$value = $sanitize_function( $value );
		}

		return $value;
	}

}