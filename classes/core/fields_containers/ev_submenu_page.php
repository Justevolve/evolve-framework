<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Submenu page fields container class.
 *
 * A submenu page is a field container that is displayed in a page in the WordPress
 * administration as a submenu belonging to a parent page.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_SubmenuPage extends Ev_MenuPage {

	/**
	 * A slug-like definition of the parent page.
	 *
	 * @var string
	 */
	private $_parent = '';

	/**
	 * Constructor for the submenu page class. Per WordPress Developer documentation
	 * the method also binds the "register" method of the class to the
	 * "admin_menu" action on admin.
	 *
	 * @since 0.1.0
	 * @param string $parent A slug-like definition of the parent page.
	 * @param string $handle A slug-like definition of the page.
	 * @param string $title A human-readable definition of the page.
	 * @param array $fields An array containing a default set of fields that belong to the admin page.
	 * @param array $args An array containing a set of arguments that define the admin page.
	 */
	function __construct( $parent, $handle, $title, $fields = array(), $args = array() )
	{
		$this->_parent = $parent;

		parent::__construct( $handle, $title, $fields, $args );
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
		// if ( ! array_key_exists( $this->_args['group'], $groups ) ) {
		// 	return $groups;
		// }

		$data = array(
			'handle' => $this->handle(),
			'title'  => $this->title(),
			'url'    => admin_url( sprintf( '%s?page=%s', $this->_base, $this->handle() ) )
		);

		if ( $this->_parent === $this->handle() ) {
			foreach ( $groups[$this->_args['group']]['pages'] as $index => $page ) {
				if ( $page['handle'] === $this->_parent ) {
					$groups[$this->_args['group']]['pages'][$index] = $data;
				}
			}
		}
		else {
			$groups[$this->_args['group']]['pages'][] = $data;
		}

		return $groups;
	}

	/**
	 * Register the admin page in WordPress, appending it to the parent admin menu item.
	 *
	 * @since 0.1.0
	 */
	public function register()
	{
		$render_callback = array( $this, 'render' );

		if ( $this->_parent === $this->handle() ) {
			/* Preventing alias subpages to be rendered twice. */
			$render_callback = '';
		}

		add_submenu_page(
			$this->_parent,
			$this->title(),
			$this->menu_title(),
			$this->capability(),
			$this->handle(),
			$render_callback
		);
	}

}