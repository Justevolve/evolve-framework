<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Meta box fields container class.
 *
 * A meta box is a field container that is displayed in post types editing
 * screens in the WordPress admininistration.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_MetaBox extends Ev_FieldsContainer {

	/**
	 * An array of post types that should display the meta box.
	 *
	 * @var array
	 */
	private $_post_types = array();

	/**
	 * The part of the page where the edit screen section should be shown.
	 *
	 * @var array
	 */
	private $_context = 'normal';

	/**
	 * The priority within the context where the boxes should show.
	 *
	 * @var array
	 */
	private $_priority = 'high';

	/**
	 * Constructor for the meta box class. Per WordPress Developer documentation
	 * the method also binds the "register" method of the class to the
	 * "add_meta_boxes" action on admin.
	 *
	 * @param string $handle A slug-like definition of the meta box.
	 * @param string $title A human-readable definition of the meta box.
	 * @param array $post_types An array of post types that should display the meta box.
	 * @param array $fields An array containing a default set of fields that belong to the meta box.
	 * @since 0.1.0
	 */
	function __construct( $handle, $title, $post_types = 'post', $fields = array() )
	{
		$post_types = apply_filters( "ev_metabox_post_types[metabox:{$handle}]", (array) $post_types );
		$this->_post_types = (array) $post_types;

		parent::__construct( $handle, $title, $fields );

		/* Register the meta box in WordPress. */
		add_action( 'add_meta_boxes', array( $this, 'register' ) );

		/* Register the saving action. */
		add_action( 'save_post', array( $this, 'save' ) );
	}

	/**
	 * Get the current page template.
	 *
	 * @since 0.2.0
	 * @return string
	 */
	private function _get_page_template()
	{
		global $post;
		$page_template = '';

		if ( $post ) {
			$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
		}

		if ( empty( $page_template ) ) {
			$page_template = 'default';
		}

		return $page_template;
	}

	/**
	 * Register the meta box in WordPress, associating it to the specified
	 * post types.
	 *
	 * @since 0.1.0
	 */
	public function register()
	{
		foreach ( $this->_post_types as $post_type ) {
			$add = true;

			if ( $post_type === 'page' ) {
				$page_template = $this->_get_page_template();

				$add = apply_filters( "ev_metabox_display[post_type:{$post_type}][template:{$page_template}][metabox:{$this->handle()}]", true );
			}

			if ( ! $add ) {
				continue;
			}

			add_meta_box(
				$this->handle(),
				$this->title(),
				array( $this, 'render' ),
				$post_type,
				$this->_context,
				$this->_priority
			);
		}
	}

   /**
	* Render the metabox content.
	*
	* @since 0.1.0
	*/
	public function render()
	{
		echo '<div class="ev ev-metabox">';
			wp_nonce_field( 'ev_meta_box', 'ev' );
			$this->render_elements();
		echo '</div>';
	}

   /**
	* Set the value for a specific field inside the container.
	*
	* @since 0.1.0
	* @param string $key The field key.
	* @return mixed The value of the field. Returns boolean false if the field has no value.
	*/
	public function set_field_value( $key = '' )
	{
		global $post;

		if ( $post && $post->ID ) {
			$custom_fields = get_post_custom( $post->ID );

			if ( array_key_exists( $key, $custom_fields ) ) {
				return get_post_meta( $post->ID, $key, true );
			}
		}

		return false;
	}

	/**
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.1.0
	 * @return array An array of field data.
	 */
	public function elements()
	{
		$current_screen = get_current_screen();
		$post_type = $current_screen->post_type;

		$fields = apply_filters( "ev[post_type:{$post_type}][metabox:{$this->handle()}]", $this->_fields );

		if ( $post_type === 'page' ) {
			$page_template = $this->_get_page_template();

			$fields = apply_filters( "ev[post_type:{$post_type}][template:{$page_template}][metabox:{$this->handle()}]", $fields );
		}

		foreach ( $fields as &$field ) {
			if ( isset( $field['type'] ) && isset( $field['handle'] ) && isset( $field['fields'] ) && $field['type'] === 'group' ) {
				$group_handle = $field['handle'];

				$field = apply_filters( "ev[post_type:{$post_type}][metabox:{$this->handle()}][group:{$group_handle}]", $field );

				if ( $post_type === 'page' ) {
					$field = apply_filters( "ev[post_type:{$post_type}][template:{$page_template}][metabox:{$this->handle()}][group:{$group_handle}]", $field );
				}
			}
		}

		/* Ensuring that the fields array is structurally sound. */
		if ( ! self::_validate_fields_structure( $fields ) ) {
			return false;
		}

		/* Ensuring that the structure contains only the fields the current user actually has access to. */
		return self::_parse_fields_structure( $fields );
	}

	/**
	 * When the post is saved, save a single custom data contained in the meta box.
	 *
	 * @since 0.1.0
	 * @param int $post_id The ID of the post being saved.
	 * @param array $element The element structure.
	 * @param string|array $element_value The element value.
	 */
	private function _save_single_field( $post_id, $element, $value )
	{
		/* Escaping user-inserted slashes. */
		$value = str_replace( '\\', '\\\\', $value );

		/* Sanitizing the field value. */
		$value = Ev_Field::sanitize( $element, $value );

		update_post_meta( $post_id, $element['handle'], $value );
	}

	/**
	 * When the post is saved, save the custom data contained in the meta box.
	 *
	 * @since 0.1.0
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save( $post_id )
	{
		if ( ! ev_user_can_save( $post_id, 'ev_meta_box' ) ) {
			return;
		}

		$elements = $this->elements();

		if ( ! empty( $elements ) ) {
			$skip_field_types = ev_skip_on_saving_field_types();

			foreach ( $elements as $index => $element ) {
				if ( $element['type'] === 'group' ) {
					foreach ( $element['fields'] as $field ) {
						if ( ! ev_is_skipped_on_saving( $field['type'] ) ) {
							if ( ! isset( $_POST[$field['handle']] ) ) {
								delete_post_meta( $post_id, $field['handle'] );
							}
							else {
								$this->_save_single_field( $post_id, $field, $_POST[$field['handle']] );
							}
						}
					}
				}
				else {
					if ( ! ev_is_skipped_on_saving( $element['type'] ) ) {
						if ( ! isset( $_POST[$element['handle']] ) ) {
							delete_post_meta( $post_id, $element['handle'] );
						}
						else {
							$this->_save_single_field( $post_id, $element, $_POST[$element['handle']] );
						}
					}
				}
			}
		}
	}

}