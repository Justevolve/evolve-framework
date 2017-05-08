<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Admin page fields container class.
 *
 * An admin page is a field container that is displayed in a page in the WordPress
 * administration.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
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

		/* Register default fields. */
		add_filter( "ev_admin_page_default_fields[page:{$this->handle()}]", array( $this, 'default_fields' ) );

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
	 * Register a list of default fields.
	 *
	 * @since 1.0.7
	 * @param array $fields An array of fields.
	 * @return array
	 */
	public function default_fields( $fields )
	{
		return $this->_fields;
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
		if ( array_key_exists( $this->_args['group'], $groups ) ) {
			return $groups;
		}

		$url = admin_url( sprintf( '%s?page=%s', $this->_base, $this->handle() ) );
		$url = apply_filters( 'ev_admin_page_group_url', $url, $this->handle() );
		$url = apply_filters( "ev_admin_page_group_url[page:{$this->handle()}]", $url );

		$groups[$this->_args['group']]['pages'][] = array(
			'handle' => $this->handle(),
			'title'  => $this->title(),
			'url'    => $url
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
	 * Return the title of the page when displayed in the page heading section.
	 *
	 * @since 0.4.0
	 * @return string A human-readable definition of the admin page.
	 */
	public function heading_title()
	{
		$page_title = $this->title();
		$is_group = isset( $this->_args['group'] ) && ! empty( $this->_args['group'] );

		if ( $is_group ) {
			$page_title = apply_filters( "ev_admin_page_heading_title[group:{$this->_args['group']}]", $page_title );
			$page_title = apply_filters( "ev_admin_page_heading_title[page:{$this->handle()}][group:{$this->_args['group']}]", $page_title );
		}

		return $page_title;
	}

	/**
	 * Get the capability that's required to access the page.
	 *
	 * @since  0.1.0
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
		$handle = $this->handle();
		$class = '';
		$is_group = isset( $this->_args['group'] ) && ! empty( $this->_args['group'] );
		$is_vertical = isset( $this->_args['vertical'] ) && $this->_args['vertical'] === true;

		if ( $is_group ) {
			$class .= ' ev-admin-page-group-' . $this->_args['group'];
		}

		if ( $is_vertical ) {
			$class .= ' ev-admin-page-group-vertical';
		}

		printf( '<div id="ev-admin-page-%s" class="ev ev-admin-page %s">', esc_attr( $handle ), esc_attr( $class ) );
			wp_nonce_field( 'ev_admin_page', 'ev' );

			if ( $is_vertical ) {
				echo '<div class="ev-admin-page-inner-wrapper">';
					echo '<div class="ev-admin-page-side-nav">';
			}

					$this->render_heading();

					if ( $is_group ) {
						$this->render_group_navigation();
					}

				if ( $is_vertical ) {
					echo '</div>';
					echo '<div class="ev-admin-page-content-wrapper">';
				}

				/**
				 * Hook before page elements are shown. Good for static pages or
				 * pages that want to display a different kind of form.
				 *
				 * @since 0.3.0
				 */
				do_action( "ev_admin_page_content[page:{$handle}]" );

				$this->render_elements();

			if ( $is_vertical ) {
					echo '</div>';
				echo '</div>';
			}

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

		$pre_title = '';

		if ( isset( $this->_args['group'] ) ) {
			$groups = ev_admin_pages_groups();

			if ( isset( $groups[$this->_args['group']] ) ) {
				$group_title = apply_filters( 'ev_admin_pages_group_title', '', $this->_args['group'] );

				if ( ! empty( $group_title ) ) {
					$pre_title = $group_title;
				}
			}
		}

		$pre_title = apply_filters( 'ev_admin_pages_pre_title', $pre_title );
		$title = $this->heading_title();

		echo '<div class="ev-admin-page-heading">';
			printf( '<h1>%s <span>%s</span></h1>', esc_html( $pre_title ), esc_html( $title ) );
			do_action( "ev_admin_page_subheading" );
			do_action( "ev_admin_page_subheading[page:{$this->handle()}]" );

			if ( isset( $this->_args['group'] ) ) {
				do_action( "ev_admin_page_subheading[group:{$this->_args['group']}]" );
			}
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
		$group['pages'] = apply_filters( "ev_admin_page_group_pages[group:{$this->_args['group']}]", $group['pages'] );

		if ( count( $group['pages'] ) > 1 ) {
			echo '<div class="ev-admin-page-group-nav">';
				echo '<ul>';
					foreach ( $group['pages'] as $page ) {
						$page_class = isset( $_GET['page'] ) && $_GET['page'] === $page['handle'] ? 'ev-active' : '';
						$page_class .= ' ev-group-page-' . $page['handle'];
						$nav = '';

						if ( isset( $this->_args['vertical'] ) && $this->_args['vertical'] === true ) {
							$vertical_elements = self::_elements( $page['handle'] );

							ob_start();
							self::render_elements_nav( $vertical_elements, key( $vertical_elements ) );
							$nav = ob_get_contents();
							ob_end_clean();
						}

						printf(
							'<li class="%s"><a href="%s">%s</a>%s</li>',
							esc_attr( $page_class ),
							esc_attr( $page['url'] ),
							esc_html( $page['title'] ),
							$nav
						);
					}
				echo '</ul>';
			echo '</div>';

			do_action( "ev_admin_page_group_nav_after" );
			do_action( "ev_admin_page_group_nav_after[page:{$this->handle()}]" );

			if ( isset( $this->_args['group'] ) ) {
				do_action( "ev_admin_page_group_nav_after[group:{$this->_args['group']}]" );
			}
		}
	}

	/**
	 * Get the page elements.
	 *
	 * @since 1.0.7
	 * @param string $handle The page handle.
	 * @return array
	 */
	public static function _elements( $handle )
	{
		$fields = apply_filters( "ev_admin_page_default_fields[page:{$handle}]", array() );
		$fields = apply_filters( "ev_admin_page[page:{$handle}]", $fields );

		foreach ( $fields as &$field ) {
			if ( isset( $field['type'] ) && isset( $field['handle'] ) && isset( $field['fields'] ) && $field['type'] === 'group' ) {
				$group_handle = $field['handle'];

				$field = apply_filters( "ev_admin_page[page:{$handle}][group:{$group_handle}]", $field );
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
	 * Return the list of the elements that belong to the fields container.
	 *
	 * @since 0.1.0
	 * @return array An array of field data.
	 */
	public function elements()
	{
		return self::_elements( $this->handle() );
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
	 * When the page or tab is saved, save a single custom option contained in the page or tab.
	 *
	 * @since 0.1.0
	 * @param array $element The element structure.
	 * @param string|array $element_value The element value.
	 */
	protected function _save_single_field( $element, $value )
	{
		$value = Ev_Field::sanitize( $element, $value );

		ev_update_option( $element['handle'], $value );
	}

	/**
	 * Delete a single custom option contained in the page or tab.
	 *
	 * @since 0.4.0
	 * @param string $handle The element handle.
	 */
	protected function _delete_single_field( $handle )
	{
		ev_delete_option( $handle );
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
		$is_valid_nonce = ev_is_post_nonce_valid( 'ev_admin_page' );

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
								$this->_delete_single_field( $field['handle'] );
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
							$this->_delete_single_field( $element['handle'] );
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
			$args    = apply_filters( "ev_save_options_tab_response_args[tab:$group]", array() );

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
