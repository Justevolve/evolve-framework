<?php if ( ! defined( 'ABSPATH' ) ) die( 'Forbidden' );

global $wp_version;

if ( ! ( version_compare( $wp_version, '4.4.0' ) >= 0 ) ) {
	/**
	 * Check if the framework meets the basic WordPress version requirement.
	 *
	 * @since 0.1.0
	 */
	function ev_framework_wrong_wp_version_notice() {
		printf( '<div class="error"><p>%s</p></div>',
			esc_html( __( 'Evolve Framework not activated: at least WordPress 4.4.0 is required.', 'ev_framework' ) )
		);
	}

	add_action( 'admin_notices', 'ev_framework_wrong_wp_version_notice' );

	return;
}

/**
 * Plugin Name: Evolve Framework
 * Plugin URI: https://github.com/Justevolve/evolve-framework
 * Description: A WordPress development framework.
 * Version: 1.0.8
 * Author: Evolve
 * Author URI: http://justevolve.it
 * Text Domain: ev_framework
 * License: GPL2
 * Domain Path: /languages/
 *
 * Evolve Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Evolve Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA.
 *
 * @package   EvolveFramework
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_Framework {

	/**
	 * The framework class instance.
	 *
	 * @static
	 * @var Ev_Framework
	 */
	private static $_instance = null;

	/**
	 * The admin controller.
	 *
	 * @var Ev_AdminController
	 */
	private $_admin = null;

	/**
	 * The login controller.
	 *
	 * @var Ev_LoginController
	 */
	private $_login = null;

	/**
	 * The media manager.
	 *
	 * @var Ev_MediaManager
	 */
	private $_media = null;

	/**
	 * The frontend interface.
	 *
	 * @var Ev_FrontendInterface
	 */
	private $_frontend = null;

	/**
	 * The theme configuration array.
	 *
	 * @var array
	 */
	private $_config = array();

	/**
	 * Contructor for the main framework class. This function defines a list of
	 * constants used throughout the framework and bootstraps the framework
	 * and launch the inclusion of files and libraries.
	 *
	 * @since 0.1.0
	 */
	function __construct()
	{
		/* Framework. */
		define( 'EV_FW', true );

		/* Framework version number. */
		define( 'EV_FRAMEWORK_VERSION', '1.0.8' );

		/* Theme folder. */
		define( 'EV_THEME_FOLDER', trailingslashit( get_template_directory() ) );

		/* Theme URI. */
		define( 'EV_THEME_URI', trailingslashit( get_template_directory_uri() ) );

		/* Child theme folder. */
		define( 'EV_CHILD_THEME_FOLDER', trailingslashit( get_stylesheet_directory() ) );

		/* Child theme URI. */
		define( 'EV_CHILD_THEME_URI', trailingslashit( get_stylesheet_directory_uri() ) );

		/* Framework folder. */
		define( 'EV_FRAMEWORK_FOLDER', trailingslashit( dirname( __FILE__ ) ) );

		/* Framework main file path. */
		define( 'EV_FRAMEWORK_MAIN_FILE_PATH', basename( EV_FRAMEWORK_FOLDER ) . '/evolve-framework.php' );

		/* Framework URI. */
		define( 'EV_FRAMEWORK_URI', plugin_dir_url( __FILE__ ) );

		/* Framework includes folder. */
		define( 'EV_FRAMEWORK_INCLUDES_FOLDER', trailingslashit( EV_FRAMEWORK_FOLDER . 'includes' ) );

		/* Framework classes folder. */
		define( 'EV_FRAMEWORK_CLASSES_FOLDER', trailingslashit( EV_FRAMEWORK_FOLDER . 'classes' ) );

		/* Framework templates folder. */
		define( 'EV_FRAMEWORK_TEMPLATES_FOLDER', trailingslashit( EV_FRAMEWORK_FOLDER . 'templates' ) );

		/* Framework includes. */
		$this->_includes();

		/* Framework bootstrap. */
		$this->_bootstrap();

		/* Framework meta information. */
		add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 4 );
	}

	/**
	 * Load internationalization functions and the framework text domain.
	 *
	 * @since 0.1.0
	 */
	private function _i18n()
	{
		/* Load the text domain for framework files. */
		load_plugin_textdomain( 'ev_framework', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		/* Localize framework strings. */
		add_action( 'admin_enqueue_scripts', array( $this, 'i18n_strings' ) );
	}

	/**
	 * Localize framework strings.
	 *
	 * @since 0.4.0
	 */
	public function i18n_strings()
	{
		global $wp_version;

		wp_localize_script( 'jquery', 'ev_framework', array(
			'wp_version' => $wp_version,
			'editor' => array(
				'text' => __( 'Text', 'ev_framework' ),
				'visual' => __( 'Visual', 'ev_framework' ),
				'add_media' => __( 'Add Media', 'ev_framework' ),
			),
			'color' => array(
				'presets' => ev_get_color_presets(),
				'new_preset_name' => __( 'Insert a name for the preset', 'ev_framework' )
			),
			'link' => array(
				'create' => __( 'Insert this URL', 'ev_framework' )
			)
		) );
	}

	/**
	 * Bootstrap the framework. This method runs a series of operations that
	 * are needed by the framework to operate correctly, such as loading the
	 * controllers for the admin part of the website; the text domain
	 * for the framework is also loaded by this method.
	 *
	 * @since 0.1.0
	 */
	private function _bootstrap()
	{
		/* Load internationalization functions and the framework text domain. */
		$this->_i18n();

		/* Instantiate the controller of the admin area. */
		$this->_admin = new Ev_AdminController();

		/* Instantiate the class of the frontend controller. */
		$this->_frontend = new Ev_FrontendController();

		/* Instantiate the controller of the theme login and registration screens. */
		$this->_login = new Ev_LoginController();

		/* Instantiate the class of the theme media manager. */
		$this->_media = new Ev_MediaManager();

		/* Load the update notifier. */
		if ( is_admin() ) {
			$this->load_update_notifier();
		}
	}

	/**
	 * Load the update notifier.
	 *
	 * @since 0.1.0
	 */
	public function load_update_notifier()
	{
		if ( $this->_can_update() ) {
			/* Instantiate the updater. */
		    new Ev_Framework_Updater( __FILE__, 'Justevolve', 'evolve-framework' );
		}
	}

	/**
	 * Check if the framework has updates notifications turned on.
	 *
	 * @since 0.1.0
	 * @return boolean
	 */
	private function _can_update()
	{
		/* Check that the plugin folder name is 'evolve-framework'. */
		$folder_name_check = basename( EV_FRAMEWORK_FOLDER ) === 'evolve-framework';

		/* Check that the plugin folder is not a checkout from a version control repo. */
		$vcss = array( '.svn', '.git', '.hg', '.bzr' );
		$is_vcs = false;

		foreach ( $vcss as $v ) {
			if ( @is_dir( EV_FRAMEWORK_FOLDER . $v ) ) {
				$is_vcs = true;
				break;
			}
		}

		return $folder_name_check && ! $is_vcs && apply_filters( 'ev_framework_can_update', true );
	}

	/**
	 * Load the framework functions. These functions can either be utility
	 * helpers as well as functions enabling specific functionalities; as such
	 * their behavior can be altered via filters or overriding them all
	 * together.
	 *
	 * @since 0.1.0
	 */
	private function _includes()
	{
		/* String utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'string.php' );

		/* General system utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'system.php' );

		/* Button utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'button.php' );

		/* Templating utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'templates.php' );

		/* Images utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'images.php' );

		/* Icons utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'icons.php' );

		/* Media utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'media.php' );

		/* Array utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'array.php' );

		/* Link utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'link.php' );

		/* Color utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'color.php' );

		/* Notices utilities. */
		require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'admin/notices.php' );

		/* Admin utilities */
		if ( is_admin() ) {
			/* Fields utilities. */
			require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'admin/fields.php' );

			/* Editor extension. */
			require_once( EV_FRAMEWORK_INCLUDES_FOLDER . 'admin/editor.php' );

			/* Updater. */
			require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'updater/ev_updater.php' );
		}

		/* Core classes. */
		$this->_includes_core();
	}

	/**
	 * Load the framework core classes.
	 *
	 * @since 0.1.0
	 */
	private function _includes_core()
	{
		/* List array wrapper. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/libs/ev_list.php' );

		/* Rich text editor adapter. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/libs/js_wp_editor.php' );

		/* Query class. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_query.php' );

		/* Pages controller. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_controller.php' );

		/* Admin pages controller. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/controllers/ev_admincontroller.php' );

		/* Frontend interface & controller. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_frontend_interface.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/controllers/ev_frontendcontroller.php' );

		/* Login and registration pages controller. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/controllers/ev_logincontroller.php' );

		/* Media manager class. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_media_manager.php' );

		/* Frontend interface class. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_frontend_interface.php' );

		/* Fields. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_text_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_textarea_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_number_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_checkbox_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_radio_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_multiple_select_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_select_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_image_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_attachment_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_divider_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_description_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_color_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_icon_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_date_field.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields/ev_bundle_field.php' );

		/* Fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_fields_container.php' );

		/* Meta box fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_meta_box.php' );

		/* Modal fields containers. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_modal.php' );
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/ev_simple_modal.php' );

		/* Admin page fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_admin_page.php' );

		/* Admin menu page fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_menu_page.php' );

		/* Admin theme menu page fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_theme_page.php' );

		/* Admin submenu page fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_submenu_page.php' );

		/* User meta box fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_user_meta_box.php' );

		/* Taxonomy meta box fields container. */
		require_once( EV_FRAMEWORK_CLASSES_FOLDER . 'core/fields_containers/ev_taxonomy_meta_box.php' );
	}

	/**
	 * Return the instance of the admin controller.
	 *
	 * @since 0.1.0
	 * @return Ev_AdminController The instance of the admin controller.
	 */
	public function admin()
	{
		return $this->_admin;
	}

	/**
	 * Return the instance of the login controller.
	 *
	 * @since 0.1.0
	 * @return Ev_LoginController The instance of the login controller.
	 */
	public function login()
	{
		return $this->_login;
	}

	/**
	 * Return the instance of the media manager class.
	 *
	 * @since 0.1.0
	 * @return Ev_MediaManager The instance of the media manager class.
	 */
	public function media()
	{
		return $this->_media;
	}

	/**
	 * Return the instance of the frontend interface.
	 *
	 * @since 0.1.0
	 * @return Ev_FrontendInterface The instance of the frontend interface.
	 */
	public function frontend()
	{
		return $this->_frontend;
	}

	/**
	 * Return the theme configuration array.
	 *
	 * @since 0.1.0
	 * @return Ev_LoginController The theme configuration array.
	 */
	public function config()
	{
		return $this->_config;
	}

	/**
	 * Add a configuration setting to the theme configuration array.
	 *
	 * @since 0.1.0
	 * @param string $key The configuration key.
	 * @param mixed $value The configuration value.
	 */
	public function set_config( $key, $value )
	{
		if ( is_array( $value ) ) {
			if ( ! isset( $this->_config[$key] ) ) {
				$this->_config[$key] = array();
			}

			$this->_config[$key] = wp_parse_args( $value, $this->_config[$key] );
		}
		else {
			$this->_config[$key] = $value;
		}
	}

	/**
	 * Add meta information to the plugin row in the Plugins screen in WordPress admin.
	 *
	 * @since 0.1.0
	 * @param array  $plugin_meta An array of the plugin's metadata,
	 *                            including the version, author,
	 *                            author URI, and plugin URI.
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
	 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
	 *                            'Drop-ins', 'Search'.
	 * @return array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status )
	{
		$framework_data = get_plugin_data( __FILE__ );
		$check = array( 'Name', 'PluginURI', 'AuthorName', 'AuthorURI', 'Version', 'TextDomain' );

		/* If this is not our plugin, exit. */
		foreach ( $check as $key ) {
			if ( $plugin_data[$key] !== $framework_data[$key] ) {
				return $plugin_meta;
			}
		}

		$framework_changelog_url = 'https://github.com/Justevolve/evolve-framework/releases';

		if ( $framework_changelog_url !== '' ) {
			$plugin_meta[] = sprintf( '<a target="_blank" rel="noopener noreferrer" data-changelog href="%s">%s</a>',
				esc_url( $framework_changelog_url ),
				esc_html( __( 'Changelog', 'ev_framework' ) )
			);
		}

		return $plugin_meta;
	}

	/**
	 * Return the instance of the framework class.
	 *
	 * @static
	 * @since 0.1.0
	 * @return Ev_Framework
	 */
	public static function instance()
	{
		if ( self::$_instance === null ) {
			self::$_instance = new Ev_Framework();
		}

		return self::$_instance;
	}

}

/* Let the fun begin! */
Ev_Framework::instance();