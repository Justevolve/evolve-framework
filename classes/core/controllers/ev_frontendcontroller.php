<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Login controller class. This controller is entitled to handle the loading
 * of login and registration external resources as well as routing operations.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_FrontendController extends Ev_FrontendInterface {

	/**
	 * The frontend context. The array is populated on demand, and contains a
	 * series of context names depending on what page the user is currently
	 * browsing.
	 *
	 * @var array
	 */
	private $_context = null;

	/**
	 * Registered scripts that will be enqueued and included in the page.
	 *
	 * @var array
	 */
	protected $_scripts = array();

	/**
	 * Registered styles that will be enqueued and included in the page.
	 *
	 * @var array
	 */
	protected $_styles = array();

	/**
	 * Registered scripts that will be removed from the page.
	 *
	 * @var array
	 */
	protected $_removed_scripts = array();

	/**
	 * Registered styles that will be removed from the page.
	 *
	 * @var array
	 */
	protected $_removed_styles = array();

	/**
	 * Contructor for the login controller class. This method binds
	 * operations to specific hooks in the request cycle, such as the ones
	 * entitled to load external resources (scripts and styles).
	 *
	 * @since 0.1.0
	 */
	function __construct()
	{
		/* Bind the enqueue of scripts and stylesheets. */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), apply_filters( 'ev_frontend_enqueue_scripts_priority', 20 ) );

		/* Register the components scripts. */
		$this->_register_components_scripts();

		parent::__construct();
	}

	/**
	 * Register the components scripts.
	 *
	 * @since 0.4.0
	 */
	private function _register_components_scripts()
	{
		/* Base file for event bindings. */
		$this->register_script( 'ev-base', EV_FRAMEWORK_URI . 'assets/js/base.js' );

		/* Tabs. */
		$this->register_script( 'ev-tabs', EV_FRAMEWORK_URI . 'components/tabs/js/script.js', array( 'ev-base' ) );
		$this->register_script( 'ev-accordion', EV_FRAMEWORK_URI . 'components/accordion/js/script.js', array( 'ev-tabs' ) );

		/* Inview. */
		$this->register_script( 'ev-inview-lib', EV_FRAMEWORK_URI . 'components/inview/js/jquery.inview.min.js', array( 'ev-base' ) );
		$this->register_script( 'ev-inview', EV_FRAMEWORK_URI . 'components/inview/js/script.js', array( 'ev-inview-lib' ) );
	}

	/**
	 * Add a script to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_script', with the only difference being that scripts are
	 * added in the footer by default; as such, the method accepts the very same
	 * set of parameters.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_script
	 * @param string 	$handle 	Name of the script. Should be unique.
	 * @param string 	$src 		URL to the script.
	 * @param array 	$deps 		Array of the handles of all the registered scripts that this script depends on, that is, the scripts that must be loaded before this script.
	 * @param string 	$ver 		String specifying the script version number, if it has one.
	 * @param bool 		$in_footer 	If this parameter is true the script is placed at the bottom of the <body>.
	 */
	public function register_script( $handle, $src = null, $deps = array(), $ver = '', $in_footer = true )
	{
		$script_data = false;

		if ( $src ) {
			$deps[] = 'jquery';

			$script_data = array(
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
				'enqueue' 	=> false
			);
		}

		$this->_scripts[$handle] = $script_data;
	}

	/**
	 * Add a script to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_script' and 'wp_enqueue_script', with the only differences
	 * being that the $src parameter isn't required and scripts are added in the
	 * footer by default; as such, the method accepts the very same set of
	 * parameters.
	 *
	 * If only the handle is provided, the script won't be registered as the
	 * controller is assuming it has already be previously by WordPress core or
	 * custom components.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_script
	 * @param string 	$handle 	Name of the script. Should be unique.
	 * @param string 	$src 		URL to the script.
	 * @param array 	$deps 		Array of the handles of all the registered scripts that this script depends on, that is, the scripts that must be loaded before this script.
	 * @param string 	$ver 		String specifying the script version number, if it has one.
	 * @param bool 		$in_footer 	If this parameter is true the script is placed at the bottom of the <body>.
	 */
	public function add_script( $handle, $src = null, $deps = array(), $ver = '', $in_footer = true )
	{
		$script_data = false;

		if ( $src ) {
			$deps[] = 'jquery';

			$script_data = array(
				'src'       => $src,
				'deps'      => $deps,
				'ver'       => $ver,
				'in_footer' => $in_footer,
				'enqueue' 	=> true
			);

			$this->_scripts[$handle] = $script_data;
		}
		elseif ( isset( $this->_scripts[$handle] ) ) {
			$this->_scripts[$handle]['enqueue'] = true;
		}
	}

	/**
	 * Add a style to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_script'; as such, the method accepts the very same
	 * set of parameters.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_script
	 * @param string 	$handle 	Name of the style. Should be unique.
	 * @param string 	$src 		URL to the style.
	 * @param array 	$deps 		Array of the handles of all the registered styles that this style depends on, that is, the styles that must be loaded before this style.
	 * @param string 	$ver 		String specifying the style version number, if it has one.
	 * @param string 	$media 	String specifying the media for which this stylesheet has been defined.
	 */
	public function register_style( $handle, $src = null, $deps = array(), $ver = '', $media = 'all' )
	{
		$style_data = false;

		if ( $src ) {
			$style_data = array(
				'src'     => $src,
				'deps'    => $deps,
				'ver'     => $ver,
				'media'   => $media,
				'enqueue' => false
			);
		}

		$this->_styles[$handle] = $style_data;
	}

	/**
	 * Add a stylesheet to be registered, enqueued and included in the page.
	 * Essentially this method is a wrapper for the WordPress core functions
	 * 'wp_register_style' and 'wp_enqueue_style', with the only differences
	 * being that the $src parameter isn't required; as such, the method accepts
	 * the very same set of parameters.
	 *
	 * If only the handle is provided, the stylesheet won't be registered as the
	 * controller is assuming it has already be previously by WordPress core or
	 * custom components.
	 *
	 * @since 0.1.0
	 * @see http://codex.wordpress.org/Function_Reference/wp_register_style
	 * @param string 	$handle 	Name of the stylesheet. Should be unique.
	 * @param string 	$src 		URL to the stylesheet.
	 * @param array 	$deps 		Array of the handles of all the registered stylesheets that this stylesheet depends on, that is, the stylesheets that must be loaded before this stylesheet.
	 * @param string 	$ver 		String specifying the stylesheet version number, if it has one.
	 * @param bool 		$media 		String specifying the media for which this stylesheet has been defined.
	 */
	public function add_style( $handle, $src = null, $deps = array(), $ver = '', $media = 'all' )
	{
		$style_data = false;

		if ( $src ) {
			$style_data = array(
				'src'     => $src,
				'deps'    => $deps,
				'ver'     => $ver,
				'media'   => $media,
				'enqueue' => true
			);

			$this->_styles[$handle] = $style_data;
		}
		elseif ( isset( $this->_styles[$handle] ) ) {
			$this->_styles[$handle]['enqueue'] = true;
		}
	}

	/**
	 * Prevent a script from being registered or enqueued and included in the
	 * page.
	 *
	 * @since 0.1.0
	 * @param string 	$handle 	Name of the script. Should be unique.
	 */
	public function remove_script( $handle )
	{
		$this->_removed_scripts[] = $handle;
	}

	/**
	 * Prevent a style from being registered or enqueued and included in the
	 * page.
	 *
	 * @since 0.1.0
	 * @param string 	$handle 	Name of the style. Should be unique.
	 */
	public function remove_style( $handle )
	{
		$this->_removed_styles[] = $handle;
	}

	/**
	 * Register and enqueue or remove scripts in the page.
	 *
	 * @since 0.1.0
	 */
	public function enqueue_scripts()
	{
		/* Register and enqueuing scripts in the page. */
		$scripts_to_be_enqueued = array();

		foreach ( $this->_scripts as $handle => $script_data ) {
			if ( $script_data['src'] ) {
				wp_register_script( $handle, $script_data['src'], $script_data['deps'], $script_data['ver'], $script_data['in_footer'] );
			}

			if ( $script_data['enqueue'] === true ) {
				$scripts_to_be_enqueued[] = $handle;
			}
		}

		/* Deregister scripts, if needed. */
		foreach ( $this->_removed_scripts as $handle ) {
			wp_deregister_script( $handle );
		}

		foreach ( $scripts_to_be_enqueued as $script ) {
			wp_enqueue_script( $script );
		}

		/* Register and enqueuing stylesheets in the page. */
		$styles_to_be_enqueued = array();

		foreach ( $this->_styles as $handle => $style_data ) {
			if ( $style_data['src'] ) {
				wp_register_style( $handle, $style_data['src'], $style_data['deps'], $style_data['ver'], $style_data['media'] );
			}

			if ( $style_data['enqueue'] === true ) {
				$styles_to_be_enqueued[] = $handle;
			}
		}

		/* Deregister stylesheets, if needed. */
		foreach ( $this->_removed_styles as $handle ) {
			wp_deregister_style( $handle );
		}

		foreach ( $styles_to_be_enqueued as $style ) {
			wp_enqueue_style( $style );
		}
	}

	/**
	 * Get the frontend context.
	 *
	 * @since 0.1.0
	 * @return array
	 */
	public function context()
	{
		if ( $this->_context === null ) {
			$this->_context = array();

			/* Get the currently queried object. */
			$object = get_queried_object();
			$object_id = get_queried_object_id();

			if ( is_front_page() ) {
				/* Front page. */
				$this->_context[] = 'home';
			}

			if ( is_home() ) {
				/* Blog page. */
				$this->_context[] = 'blog';
			}
			elseif ( is_singular() ) {
				/* Singular views. */
				$this->_context[] = 'singular';
				$this->_context[] = "singular-{$object->post_type}";
				$this->_context[] = "singular-{$object->post_type}-{$object_id}";

				if ( $object->post_type === 'page' ) {
					$templates = wp_get_theme()->get_page_templates( $object );
					$page_template = ev_get_page_template( $object_id );
					$template = '';

					if ( in_array( $page_template, array_keys( $templates ) ) ) {
						$template = current( $templates );
					}
					else {
						$template = $page_template;
					}

					$template = sanitize_title( $template );

					$this->_context[] = 'page-template-' . $template;
				}
			}
			elseif ( is_archive() ) {
				/* Archive views. */
				$this->_context[] = 'archive';

				if ( is_post_type_archive() ) {
					/* Post type archives. */
					$post_type = get_post_type_object( get_query_var( 'post_type' ) );
					$this->_context[] = "archive-{$post_type->name}";
				}

				if ( is_tax() || is_category() || is_tag() ) {
					/* Taxonomy archives. */
					$this->_context[] = 'taxonomy';
					$this->_context[] = "taxonomy-{$object->taxonomy}";
					$slug = ( ( 'post_format' == $object->taxonomy ) ? str_replace( 'post-format-', '', $object->slug ) : $object->slug );
					$this->_context[] = "taxonomy-{$object->taxonomy}-" . sanitize_html_class( $slug, $object->term_id );
				}

				if ( is_author() ) {
					/* User/author archives. */
					$user_id = get_query_var( 'author' );
					$this->_context[] = 'user';
					$this->_context[] = 'user-' . sanitize_html_class( get_the_author_meta( 'user_nicename', $user_id ), $user_id );
				}

				if ( is_date() ) {
					/* Date archives. */
					$this->_context[] = 'date';

					if ( is_year() ) {
						$this->_context[] = 'year';
					}

					if ( is_month() ) {
						$this->_context[] = 'month';
					}

					if ( get_query_var( 'w' ) ) {
						$this->_context[] = 'week';
					}

					if ( is_day() ) {
						$this->_context[] = 'day';
					}
				}

				if ( is_time() ) {
					/* Time archives. */
					$this->_context[] = 'time';

					if ( get_query_var( 'hour' ) ) {
						$this->_context[] = 'hour';
					}

					if ( get_query_var( 'minute' ) ) {
						$this->_context[] = 'minute';
					}
				}
			}
			elseif ( is_search() ) {
				/* Search results. */
				$this->_context[] = 'search';
			}
			elseif ( is_404() ) {
				/* Error 404 pages. */
				$this->_context[] = 'error-404';
			}
		}

		return array_map( 'esc_attr', apply_filters( 'ev_frontend_context', array_unique( $this->_context ) ) );
	}

}