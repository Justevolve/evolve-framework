<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Admin controller class. This controller is entitled to handle the loading
 * of admin external resources as well as routing operations.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_AdminController extends Ev_Controller {

	/**
	 * An array containing admin notices.
	 *
	 * @var array
	 */
	private $_notices = array();

	/**
	 * The admin pages.
	 *
	 * @var array
	 */
	public $pages = array();

	/**
	 * Contructor for the admin controller class. This method binds
	 * operations to specific hooks in the request cycle, such as the ones
	 * entitled to load external resources (scripts and styles).
	 *
	 * @since 0.1.0
	 */
	function __construct()
	{
		/* Add the Javascript file for admin components. */
		$this->add_script( 'ev-admin', EV_FRAMEWORK_URI . 'assets/js/min/admin.min.js', array( 'underscore', 'jquery-ui-sortable', 'jquery-ui-datepicker', 'media-upload' ) );

		/* Add the CSS file for admin components. */
		$this->add_style( 'ev-admin-icons', EV_FRAMEWORK_URI . 'assets/css/f/evframework.css' );
		$this->add_style( 'ev-admin', EV_FRAMEWORK_URI . 'assets/css/admin.css' );

		/* Bind the enqueue of scripts and stylesheets. */
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), apply_filters( 'ev_admin_enqueue_scripts_priority', 20 ) );

		/* Hooking admin notices */
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Register and add a meta box to the admin interface binding it to one or
	 * more specific post types.
	 *
	 * @since 0.1.0
	 * @param string $handle A slug-like definition of the meta box.
	 * @param string $title A human-readable definition of the meta box.
	 * @param string|array $post_types A string or array of post types handles.
	 * @param array $fields An array containing a default set of fields that belong to the meta box.
	 * @return Ev_MetaBox
	 */
	public function add_meta_box( $handle, $title, $post_types = 'post', $fields = array() )
	{
		return new Ev_MetaBox( $handle, $title, $post_types, $fields );
	}

	/**
	 * Register and add a page to the admin interface creating a new paged
	 * appended to the admin menu.
	 *
	 * @since 0.1.0
	 * @param string $handle A slug-like definition of the page.
	 * @param string $title A human-readable definition of the page.
	 * @param array $fields An array containing a default set of fields that belong to the menu page.
	 * @param array $args An array containing a set of arguments that define the admin page.
	 * @return Ev_MenuPage
	 */
	public function add_menu_page( $handle, $title, $fields = array(), $args = array() )
	{
		return new Ev_MenuPage( $handle, $title, $fields, $args );
	}

	/**
	 * Register and add a page to the admin interface creating a new paged
	 * appended to the appearance menu.
	 *
	 * @since 1.0.0
	 * @param string $handle A slug-like definition of the page.
	 * @param string $title A human-readable definition of the page.
	 * @param array $fields An array containing a default set of fields that belong to the menu page.
	 * @param array $args An array containing a set of arguments that define the admin page.
	 * @return Ev_MenuPage
	 */
	public function add_theme_page( $handle, $title, $fields = array(), $args = array() )
	{
		return new Ev_ThemePage( $handle, $title, $fields, $args );
	}

	/**
	 * Register and add a subpage to a page in the admin menu.
	 *
	 * @since 0.1.0
	 * @param string $parent A slug-like definition of the parent page.
	 * @param string $handle A slug-like definition of the page.
	 * @param string $title A human-readable definition of the page.
	 * @param array $fields An array containing a default set of fields that belong to the menu page.
	 * @param array $args An array containing a set of arguments that define the admin page.
	 * @return Ev_MenuPage
	 */
	public function add_submenu_page( $parent, $handle, $title, $fields = array(), $args = array() )
	{
		if ( $parent === $handle ) {
			/* We're essentially creating an alias here, so we don't need fields. */
			$fields = array();
		}

		return new Ev_SubmenuPage( $parent, $handle, $title, $fields, $args );
	}

	/**
	 * Register and add a meta box to the user editing interface binding it to
	 * one or more specific user roles.
	 *
	 * @since 0.2.0
	 * @param string $handle A slug-like definition of the user meta box.
	 * @param string $title A human-readable definition of the user meta box.
	 * @param string|array $roles A string or array of roles.
	 * @param array $fields An array containing a default set of fields that belong to the user meta box.
	 * @return Ev_UserMetaBox
	 */
	public function add_user_meta_box( $handle, $title, $roles = '', $fields = array() )
	{
		return new Ev_UserMetaBox( $handle, $title, $roles, $fields );
	}

	/**
	 * Register and add a meta box to a term editing interface binding it to
	 * one or more taxonomies.
	 *
	 * @since 0.4.0
	 * @param string $handle A slug-like definition of the taxonomy meta box.
	 * @param string $title A human-readable definition of the taxonomy meta box.
	 * @param string|array $taxonomies A string or array of taxonomies.
	 * @param array $fields An array containing a default set of fields that belong to the taxonomy meta box.
	 * @return Ev_UserMetaBox
	 */
	public function add_taxonomy_meta_box( $handle, $title, $taxonomies = '', $fields = array() )
	{
		return new Ev_TaxonomyMetaBox( $handle, $title, $taxonomies, $fields );
	}

	/**
	 * Hook for custom admin notices.
	 *
	 * @since 0.1.0
	 */
	public function admin_notices()
	{
		$data = $this->notices();

		$allowed_tags = array(
			'a' => array(
				'href' => array(),
				'title' => array()
			),
			'p' => array(),
			'br' => array(),
			'em' => array(),
			'strong' => array(),
		);

		$nag_allowed_tags = $allowed_tags;
		unset( $nag_allowed_tags['p'] );

		foreach ( $data as $key => $value ) {
			$type = $value['type'];
			$message = $value['message'];
			$message = wptexturize( $message );

			echo '<div class="' . esc_attr( $type ) . ' ev-admin-notice">';
				if ( $type !== 'update-nag' ) {
					echo wp_kses( wpautop( $message ), $allowed_tags );
				} else {
					echo wp_kses( $message, $nag_allowed_tags );
				}

			echo '</div>';
		}
	}

	/**
	 * Return the array containing the admin notices.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function notices()
	{
		return $this->_notices;
	}

	/**
	 * Add a notice to the array containing the admin notices.
	 *
	 * @since 0.1.0
	 * @param string $message The admin notice message.
	 * @param string $type The admin notice type.
	 */
	public function add_notice( $message, $type )
	{
		$this->_notices[] = array(
			'type' => $type,
			'message' => $message
		);
	}

	/**
	 * Enqueue scripts on admin.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_admin_scripts()
	{
		if ( function_exists( 'wp_enqueue_media' ) ) {
			global $pagenow;
			global $wp_customize;

			$edit_page = $pagenow == 'post.php' || $pagenow == 'post-new.php';
			$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( $_GET['post_type'] ) : ( isset( $_GET['post'] ) ? get_post_type( absint( $_GET['post'] ) ) : 'post' );
			$post_type_support = post_type_supports( $post_type, 'editor' ) || post_type_supports( $post_type, 'thumbnail' );
			$is_customizer = function_exists( 'is_customize_preview' ) ? is_customize_preview() : isset( $wp_customize );

			if( ! $is_customizer && ( ! $edit_page || ! $post_type_support ) ) {
				wp_enqueue_media();
			}
		}
	}

}