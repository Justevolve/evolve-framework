<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Admin page fields container class.
 *
 * An admin page is a field container that is displayed in a page in the WordPress
 * administration.
 *
 * @package   EvolveFramework
 * @since 	  1.0.0
 * @version   1.0.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

abstract class Ev_AdminPage extends Ev_FieldsContainer {

	/**
	 * The URL base for admin pages.
	 *
	 * @var string
	 */
	protected $_base = 'admin.php';

	/**
	 * An array containing a set of arguments that define the admin page.
	 *
	 * @var array
	 */
	protected $_args = array();

	/**
	 * Set to true if the groups should output a form tag as well.
	 *
	 * @var boolean
	 */
	protected $_groups_with_form = true;

	/**
	 * Constructor for the admin page class. Per WordPress Developer documentation
	 * the method also binds the "register" method of the class to the
	 * "admin_menu" action on admin.
	 *
	 * @param string $handle A slug-like definition of the page.
	 * @param string $title A human-readable definition of the page.
	 * @param array $fields An array containing a default set of fields that belong to the admin page.
	 * @param array $args An array containing a set of arguments that define the admin page.
	 * @since 0.1.0
	 */
	function __construct( $handle, $title, $fields = array(), $args = array() )
	{
		$this->_args = $args;

		parent::__construct( $handle, $title, $fields );

		/* Register the admin page in WordPress. */
		add_action( 'admin_menu', array( $this, 'register' ) );

		if ( isset( $this->_args['group'] ) && ! empty( $this->_args['group'] ) ) {
			/* If a group is set for the page, store the information in the system. */
			add_filter( 'ev_admin_pages_groups', array( $this, 'group' ) );
		}

		/* Register the saving action for the page tabs. */
		add_action( "ev_save_options_tab[page:{$this->handle()}]", array( $this, 'save' ) );
	}

	/**
	 * Add the current page to a group of pages.
	 *
	 * @since 0.1.0
	 * @param array $groups An array containing the groups admin pages are grouped into.
	 * @return array
	 */
	public function group( $groups )
	{
		if ( ! array_key_exists( $this->_args['group'], $groups ) ) {
			return $groups;
		}

		$groups[$this->_args['group']]['pages'][] = array(
			'handle' => $this->handle(),
			'title'  => $this->title(),
			'url'    => admin_url( sprintf( '%s?page=%s', $this->_base, $this->handle() ) )
		);

		return $groups;
	}

	/**
	 * Return the title of the page.
	 *
	 * @since 0.1.0
	 * @return string A human-readable definition of the admin page.
	 */
	public function title()
	{
		$page_title = apply_filters( "ev_admin_page_title", $this->_title );
		$page_title = apply_filters( "ev_admin_page_title[page:{$this->handle()}]", $page_title );

		return $page_title;
	}

	/**
	 * Return the menu title of the page.
	 *
	 * @since 0.1.0
	 * @return string A human-readable definition of the admin page when displayed in the menu.
	 */
	public function menu_title()
	{
		$menu_title = apply_filters( "ev_admin_page_menu_title", $this->title() );
		$menu_title = apply_filters( "ev_admin_page_menu_title[page:{$this->handle()}]", $menu_title );

		return $menu_title;
	}

	/**
	 * Get the capability that's required to access the page.
	 *
	 * @since  1.0.0
	 * @return string The capability that's required to access the page.
	 */
	public function capability()
	{
		$capability = 'manage_options';
		$capability = apply_filters( "ev_admin_page_capability", $capability );
		$capability = apply_filters( "ev_admin_page_capability[page:{$this->handle()}]", $capability );

		return $capability;
	}

	/**
	 * Register the admin page in WordPress, appending it to the admin menu.
	 *
	 * @since 0.1.0
	 */
	abstract public function register();

	/**
	 * Render the admin page content.
	 *
	 * @since 0.1.0
	 */
	public function render()
	{
		echo '<div class="ev ev-admin-page">';
			wp_nonce_field( 'ev_admin_page', 'ev' );
			$this->render_heading();

			if ( isset( $this->_args['group'] ) ) {
				$this->render_group_navigation();
			}

			$this->render_elements();
			echo '<div class="ev-persistent-messages-container"></div>';
		echo '</div>';
	}

	/**
	 * Render the admin page heading.
	 *
	 * @since 0.1.0
	 */
	protected function render_heading()
	{
		$theme_data = wp_get_theme();
		$theme = $theme_data->get( 'Name' );

		if ( is_child_theme() ) {
			$parent_data = wp_get_theme( $theme_data->Template );
			$theme = $parent_data->get( 'Name' );
		}

		$title = $this->title();

		if ( isset( $this->_args['group'] ) ) {
			$groups = ev_admin_pages_groups();

			if ( isset( $groups[$this->_args['group']] ) ) {
				$title = $groups[$this->_args['group']]['label'];
			}
		}

		echo '<div class="ev-admin-page-heading">';
			printf( '<h2>%s <span>%s</span></h2>', esc_html( $theme ), esc_html( $title ) );
			do_action( "ev_admin_page_subheading" );
			do_action( "ev_admin_page_subheading[page:{$this->handle()}]" );
		echo '</div>';
	}

	/**
	 * Render the admin page group navigation.
	 *
	 * @since 0.1.0
	 */
	protected function render_group_navigation()
	{
		$groups = ev_admin_pages_groups();

		if ( ! isset( $groups[$this->_args['group']] ) ) {
			return;
		}

		$group = $groups[$this->_args['group']];

		if ( count( $group['pages'] ) > 1 ) {
			echo '<div class="ev-admin-page-group-nav">';
				echo '<ul>';
					foreach ( $group['pages'] as $page ) {
						printf(
							'<li><a href="%s" class="%s">%s</a></li>',
							esc_attr( $page['url'] ),
							isset( $_GET['page'] ) && $_GET['page'] === $page['handle'] ? 'ev-active' : '',
							esc_html( $page['title']
						) );
					}
				echo '</ul>';
			echo '</div>';
		}
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
		return ev_get_option( $key );
	}

	/**
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.1.0
	 * @return array An array of field data.
	 */
	public function elements()
	{
		$fields = apply_filters( "ev_admin_page[page:{$this->handle()}]", $this->_fields );

		foreach ( $fields as &$field ) {
			if ( isset( $field['type'] ) && isset( $field['handle'] ) && isset( $field['fields'] ) && $field['type'] === 'group' ) {
				$group_handle = $field['handle'];

				$field = apply_filters( "ev_admin_page[page:{$this->handle()}][group:{$group_handle}]", $field );
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
	 * When the page or tab is saved, save a single custom option contained in the page or tab.
	 *
	 * @since 0.1.0
	 * @param array $element The element structure.
	 * @param string|array $element_value The element value.
	 */
	private function _save_single_field( $element, $value )
	{
		$value = Ev_Field::sanitize( $element, $value );

		ev_update_option( $element['handle'], $value );
	}

	/**
	 * When the page is refreshed, save the custom data contained in the admin page.
	 *
	 * @since 0.1.0
	 * @param string $group The group of the page that is being saved.
	 */
	public function save( $group = '' )
	{
		/* This should run on admin only. */
		if ( ! is_admin() ) {
			return;
		}

		/* Verify that we're submitting any data. */
		if ( empty( $_POST ) ) {
			return;
		}

		/* Verify the validity of the supplied nonce. */
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		$action = 'ev_admin_page';
		$is_valid_nonce = wp_verify_nonce( $nonce, $action );

		/* Check the user has the capability to save the page. */
		$is_valid_cap = current_user_can( $this->capability() );

		/* Exit if the nonce is invalid or the user doesn't have the required capability to save the page. */
		if ( ! $is_valid_nonce || ! $is_valid_cap ) {
			return;
		}

		$elements = $this->elements();

		if ( ! empty( $elements ) ) {
			foreach ( $elements as $index => $element ) {
				if ( $element['type'] === 'group' && $element['handle'] === $group ) {
					foreach ( $element['fields'] as $field ) {
						if ( ! ev_is_skipped_on_saving( $field['type'] ) ) {
							if ( ! isset( $_POST[$field['handle']] ) ) {
								ev_delete_option( $field['handle'] );
							}
							else {
								$this->_save_single_field( $field, $_POST[$field['handle']] );
							}
						}
					}

					break;
				}
				else {
					if ( ! ev_is_skipped_on_saving( $element['type'] ) ) {
						if ( ! isset( $_POST[$element['handle']] ) ) {
							ev_delete_option( $element['handle'] );
						}
						else {
							$this->_save_single_field( $element, $_POST[$element['handle']] );
						}
					}
				}
			}

			$type = 'success';
			$message = apply_filters( 'ev_save_options_tab_response_message', __( 'All saved!', 'ev_framework' ), $type );
			$heading = apply_filters( 'ev_save_options_tab_response_heading', '', $type );
			$args = apply_filters( "ev_save_options_tab_response_args[tab:$group]", array() );

			ev_ajax_message( $message, $type, $heading, $args );
		}
	}

}

/**
 * Register the saving action for option pages tabs.
 */
function ev_save_options_tab() {
	$group   = isset( $_POST['group'] ) && ! empty( $_POST['group'] ) ? $_POST['group'] : '';
	$context = isset( $_POST['context'] ) && ! empty( $_POST['context'] ) ? $_POST['context'] : '';

	if ( ! empty( $group ) && ! empty( $context ) ) {
		do_action( "ev_save_options_tab[page:{$context}]", $group );
	}

	die();
}

add_action( 'wp_ajax_ev_save_options_tab', 'ev_save_options_tab' );