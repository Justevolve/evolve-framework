<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Taxonomy meta box fields container class.
 *
 * A taxonomy meta box is a field container that is displayed in taxonomy editing screens.
 *
 * @package   EvolveFramework
 * @since 	  0.4.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Ev_TaxonomyMetaBox extends Ev_FieldsContainer {

	/**
	 * An array of taxonomies that should display the user box.
	 *
	 * @var array
	 */
	private $_taxonomies = array();

	/**
	 * Constructor for the taxonomy meta box class.
	 *
	 * @param string $handle A slug-like definition of the taxonomy meta box.
	 * @param string $title A human-readable definition of the taxonomy meta box.
	 * @param array $taxonomies An array of roles that should display the taxonomy meta box.
	 * @param array $fields An array containing a default set of fields that belong to the taxonomy meta box.
	 * @since 0.4.0
	 */
	function __construct( $handle, $title, $taxonomies = array(), $fields = array() )
	{
		$taxonomies = apply_filters( "ev_taxonomy_metabox_taxonomies[metabox:{$handle}]", $taxonomies );
		$this->_taxonomies = (array) $taxonomies;

		parent::__construct( $handle, $title, $fields );

		/* Register the taxonomy meta box in WordPress. */
		foreach ( $this->_taxonomies as $taxonomy ) {
			// add_action( "{$taxonomy}_add_form_fields", array( $this, 'render' ), 10, 2 );
			add_action( "{$taxonomy}_edit_form", array( $this, 'render' ), 10, 2 );
		}

		/* Register the saving action. */
		foreach ( $this->_taxonomies as $taxonomy ) {
			add_action( "edited_{$taxonomy}", array( $this, 'save' ), 10, 2 );
			add_action( "create_{$taxonomy}", array( $this, 'save' ), 10, 2 );
		}
	}

    /**
	 * Render the taxonomy meta box.
	 *
	 * @since 0.4.0
	 */
	public function render() {
		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		echo '<div class="ev ev-native-meta ev-taxonomy-metabox">';
			wp_nonce_field( 'ev_taxonomy_meta_box', 'ev' );

			printf( '<h3>%s</h3>', esc_html( $this->title() ) );
			$this->render_elements();
		echo '</div>';
	}

   /**
	* Set the value for a specific field inside the container.
	*
	* @since 0.4.0
	* @param string $key The field key.
	* @return mixed The value of the field. Returns boolean false if the field has no value.
	*/
	public function set_field_value( $key = '' )
	{
		global $tag_ID;

		if ( $tag_ID ) {
			return $this->get_taxonomy_meta( $tag_ID, $key );
		}

		return false;
	}

	/**
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.4.0
	 * @return array An array of field data.
	 */
	public function elements()
	{
		$fields = apply_filters( "ev_taxonomy_metabox[metabox:{$this->handle()}]", $this->_fields );

		foreach ( $fields as &$field ) {
			if ( isset( $field['type'] ) && isset( $field['handle'] ) && isset( $field['fields'] ) && $field['type'] === 'group' ) {
				$group_handle = $field['handle'];

				$field = apply_filters( "ev_taxonomy_metabox[metabox:{$this->handle()}][group:{$group_handle}]", $field );
			}
		}

		/* Ensuring that the fields array is structurally sound. */
		$valid = self::_validate_fields_structure( $fields );

		if ( $valid !== true ) {
			self::_output_field_errors( $valid );

			return false;
		}

		/* Ensuring that the structure contains only the fields the current user actually has access to. */
		return self::_parse_fields_structure( $fields );
	}

	/**
	 * When the taxonomy information is saved, save a single custom data contained in the taxonomy meta box.
	 *
	 * @since 0.4.0
	 * @param int $term_id The ID of the term being saved.
	 * @param array $element The element structure.
	 * @param string|array $element_value The element value.
	 */
	private function _save_single_field( $term_id, $element, $value )
	{
		/* Escaping user-inserted slashes. */
		$value = str_replace( '\\', '\\\\', $value );

		/* Sanitizing the field value. */
		$value = Ev_Field::sanitize( $element, $value );

		$this->update_taxonomy_meta( $term_id, $element['handle'], $value );
	}

	/**
	 * Determines whether or not the current user has the ability to save meta data
	 * associated with this term.
	 *
	 * @since 0.4.0
	 * @param string $action The submitted nonce action.
	 * @param string $nonce The submitted nonce key.
	 * @return boolean Whether or not the user has the ability to save this term information.
	 */
	private function user_can_save_taxonomy_meta( $action = '', $nonce = 'ev' )
	{
		/* Verify the validity of the supplied nonce. */
		$is_valid_nonce = ev_is_post_nonce_valid( $action, $nonce );

		/* Check the user has the capability to edit the taxonomy data. */
		$is_valid_cap = current_user_can( 'manage_categories' );

		/* Return true if the user is able to save; otherwise, false. */
	    return $is_valid_nonce && $is_valid_cap;
	}

	/**
	 * Delete a custom meta data associated to a specific taxonomy term.
	 *
	 * @since 0.4.0
	 * @param integer $term_id The ID of the term being edited.
	 * @param string $handle The meta data key.
	 */
	private function _delete_taxonomy_meta( $term_id, $handle )
	{
		delete_term_meta( $term_id, $handle );
	}

	/**
	 * Update a custom meta data associated to a specific taxonomy term.
	 *
	 * @since 0.4.0
	 * @param integer $term_id The ID of the term being edited.
	 * @param string $handle The meta data key.
	 * @param string $value The meta data value.
	 */
	private function update_taxonomy_meta( $term_id, $handle, $value )
	{
		update_term_meta( $term_id, $handle, $value );
	}

	/**
	 * Get a custom meta data associated to a specific taxonomy term.
	 *
	 * @since 0.4.0
	 * @param integer $term_id The ID of the term being edited.
	 * @param string $handle The meta data key.
	 */
	private function get_taxonomy_meta( $term_id, $handle )
	{
		return get_term_meta( $term_id, $handle, true );
	}

	/**
	 * When the term is saved, save the custom data contained in the taxonomy meta box.
	 *
	 * @since 0.4.0
	 * @param int $term_id The ID of the term being saved.
	 */
	public function save( $term_id )
	{
		if ( ! $this->user_can_save_taxonomy_meta( 'ev_taxonomy_meta_box' ) ) {
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
								$this->_delete_taxonomy_meta( $term_id, $field['handle'] );
							}
							else {
								$this->_save_single_field( $term_id, $field, $_POST[$field['handle']] );
							}
						}
					}
				}
				else {
					if ( ! ev_is_skipped_on_saving( $element['type'] ) ) {
						if ( ! isset( $_POST[$element['handle']] ) ) {
							$this->_delete_taxonomy_meta( $term_id, $element['handle'] );
						}
						else {
							$this->_save_single_field( $term_id, $element, $_POST[$element['handle']] );
						}
					}
				}
			}
		}
	}

}