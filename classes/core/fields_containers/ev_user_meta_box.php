<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * User meta box fields container class.
 *
 * A user meta box is a field container that is displayed in user editing screens.
 *
 * @package   EvolveFramework
 * @since 	  0.1.1
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_UserMetaBox extends Ev_FieldsContainer {

	/**
	 * An array of roles that should display the user box.
	 *
	 * @var array
	 */
	private $_roles = array();

	/**
	 * Constructor for the user meta box class.
	 *
	 * @param string $handle A slug-like definition of the user meta box.
	 * @param string $title A human-readable definition of the user meta box.
	 * @param array $roles An array of roles that should display the user meta box.
	 * @param array $fields An array containing a default set of fields that belong to the user meta box.
	 * @since 0.2.0
	 */
	function __construct( $handle, $title, $roles = array(), $fields = array() )
	{
		$roles = apply_filters( "ev_user_metabox_roles[metabox:{$handle}]", $roles );
		$this->_roles = $roles;

		parent::__construct( $handle, $title, $fields );

		/* Register the user meta box in WordPress. */
		add_action( 'show_user_profile', array( $this, 'render' ) );
		add_action( 'edit_user_profile', array( $this, 'render' ) );

		/* Register the saving action. */
		add_action( 'personal_options_update', array( $this, 'save' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save' ) );
	}

    /**
	 * Render the user meta box in WordPress, associating it to the specified
	 * user roles.
	 *
	 * @since 0.2.0
	 */
	public function render() {
		global $user_id;

		if ( $user_id ) {
			$user = get_user_by( 'id', $user_id );

			if ( ! $user ) {
				return;
			}

			/* Check if the meta box should be displayed for the current user's role. */
			$check_current_user_role = true;

			if ( ! empty( $this->_roles ) ) {
				foreach ( (array) $this->_roles as $role ) {
					$check_current_user_role = user_can( $user, $role );

					if ( $check_current_user_role ) {
						break;
					}
				}
			}

			if ( ! $check_current_user_role ) {
				return;
			}

			/* Check if the current can edit the user. */
			$check_current_user_can_edit_user = $user && current_user_can( 'edit_user', $user->ID );

			if ( ! $check_current_user_can_edit_user ) {
				return;
			}

			echo '<div class="ev ev-native-meta ev-user-metabox">';
				wp_nonce_field( 'ev_user_meta_box', 'ev' );

				printf( '<h3>%s</h3>', esc_html( $this->title() ) );
				$this->render_elements();
			echo '</div>';
		}
	}

   /**
	* Set the value for a specific field inside the container.
	*
	* @since 0.2.0
	* @param string $key The field key.
	* @return mixed The value of the field. Returns boolean false if the field has no value.
	*/
	public function set_field_value( $key = '' )
	{
		global $user_id;

		if ( $user_id ) {
			$custom_fields = get_user_meta( $user_id );

			if ( array_key_exists( $key, $custom_fields ) && isset( $custom_fields[$key][0] ) && ! empty( $custom_fields[$key][0] ) ) {
				return get_user_meta( $user_id, $key, true );
			}
		}

		return false;
	}

	/**
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.2.0
	 * @return array An array of field data.
	 */
	public function elements()
	{
		$fields = apply_filters( "ev_user_metabox[metabox:{$this->handle()}]", $this->_fields );

		foreach ( $fields as &$field ) {
			if ( isset( $field['type'] ) && isset( $field['handle'] ) && isset( $field['fields'] ) && $field['type'] === 'group' ) {
				$group_handle = $field['handle'];

				$field = apply_filters( "ev_user_metabox[metabox:{$this->handle()}][group:{$group_handle}]", $field );
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
	 * When the user information is saved, save a single custom data contained in the user meta box.
	 *
	 * @since 0.2.0
	 * @param int $user_id The ID of the user being saved.
	 * @param array $element The element structure.
	 * @param string|array $element_value The element value.
	 */
	private function _save_single_field( $user_id, $element, $value )
	{
		/* Escaping user-inserted slashes. */
		$value = str_replace( '\\', '\\\\', $value );

		/* Sanitizing the field value. */
		$value = Ev_Field::sanitize( $element, $value );

		update_user_meta( $user_id, $element['handle'], $value );
	}

	/**
	 * When the user is saved, save the custom data contained in the user meta box.
	 *
	 * @since 0.2.0
	 * @param int $user_id The ID of the user being saved.
	 */
	public function save( $user_id )
	{
		if ( empty( $_POST ) ) {
			return;
		}

		if ( ! ev_user_can_save_user_meta( $user_id, 'ev_user_meta_box' ) ) {
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
								delete_user_meta( $user_id, $field['handle'] );
							}
							else {
								$this->_save_single_field( $user_id, $field, $_POST[$field['handle']] );
							}
						}
					}
				}
				else {
					if ( ! ev_is_skipped_on_saving( $element['type'] ) ) {
						if ( ! isset( $_POST[$element['handle']] ) ) {
							delete_user_meta( $user_id, $element['handle'] );
						}
						else {
							$this->_save_single_field( $user_id, $element, $_POST[$element['handle']] );
						}
					}
				}
			}
		}
	}

}