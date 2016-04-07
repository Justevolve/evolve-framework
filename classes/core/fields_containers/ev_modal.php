<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Modal fields container class.
 *
 * A modal is a field container that is displayed in a popup.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_Modal extends Ev_FieldsContainer {

	/**
	 * The modal initial data.
	 *
	 * @var array
	 */
	private $_data = array();

	/**
	 * The modal configuration array.
	 *
	 * @var array
	 */
	private $_config = array();

	/**
	 * The tabs push method when the corresponding navigation element is clicked.
	 *
	 * @var string
	 */
	protected $_tabs_push_method = '';

	/**
	 * Constructor for the modal class.
	 *
	 * @param string $handle A slug-like definition of the modal.
	 * @param array $fields An array containing a default set of fields that belong to the modal.
	 * @param array $data An array containing the data for the fields that belong to the modal.
	 * @param array $config Optional configuration array.
	 * @since 0.1.0
	 */
	function __construct( $handle, $fields = array(), $data = array(), $config = array() )
	{
		$this->_data = stripslashes_deep( $data );
		$this->_config = wp_parse_args( $config, array(
			/* Title of the modal. */
			'title' => __( 'Edit', 'ev_framework' ),

			/* Title controls. */
			'title_controls' => '',

			/* Text of the close button for the modal. */
			'button' => __( 'OK', 'ev_framework' ),

			/* Nonce of the close button for the modal. */
			'button_nonce' => wp_create_nonce( "ev_modal_$handle" ),

			/* Additional footer content. */
			'footer_content' => ''
		) );

		$title = isset( $this->_config['title'] ) ? $this->_config['title'] : '';

		parent::__construct( $handle, $title, $fields );
	}

   /**
	* Render the modal content.
	*
	* @since 0.1.0
	*/
	public function render()
	{
		echo '<div class="ev-modal-header">';
			echo '<h1>' . esc_html( $this->title() ) . '</h1>';

			if ( ! empty( $this->_config['title_controls'] ) ) {
				printf( '<div class="ev-modal-header-title-controls">%s</div>', wp_kses( $this->_config['title_controls'], array(
					'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array()
					)
				) ) );
			}
		echo '</div>';

		echo '<form class="ev ev-modal">';
			wp_nonce_field( 'ev_modal', 'ev', false );
			$this->render_elements();

			echo '<div class="ev-modal-footer">';
				echo $this->_config['footer_content'];

				$elements = $this->elements();

				if ( ! empty( $elements ) ) {
					ev_btn(
						$this->_config['button'],
						'save',
						array(
							'attrs' => array(
								'data-nonce' => $this->_config['button_nonce'],
								'class' => 'ev-save',
								'type' => 'submit'
							),
							'size' => 'medium'
						)
					);
				}
			echo '</div>';
		echo '</form>';
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
		if ( isset( $this->_data[$key] ) ) {
			return $this->_data[$key];
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
		$fields = apply_filters( "ev_modal[modal:{$this->handle()}]", $this->_fields );

		/* Ensuring that the fields array is structurally sound. */
		if ( ! self::_validate_fields_structure( $fields ) ) {
			return false;
		}

		/* Ensuring that the structure contains only the fields the current user actually has access to. */
		return self::_parse_fields_structure( $fields );
	}

}

/**
 * Display the container for framework-generated modals.
 *
 * @since 0.4.0
 */
function ev_modals_container_wrapper() {
	echo '<div id="ev-modals-container"></div>';
}

add_action( 'admin_footer', 'ev_modals_container_wrapper' );