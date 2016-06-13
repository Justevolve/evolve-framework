<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Theme page fields container class.
 *
 * A menu page is a field container that is displayed in a page in the WordPress
 * administration under the Appearance menu.
 *
 * @package   EvolveFramework
 * @since 	  1.0.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_ThemePage extends Ev_AdminPage {

	/**
	 * Get the position of the page in the administration menu.
	 *
	 * @since  0.1.0
	 * @return mixed The position index of the page in the administration menu.
	 */
	public function position()
	{
		$position = null;
		$position = apply_filters( 'ev_admin_page_position', $position );
		$position = apply_filters( "ev_admin_page_position[page:{$this->handle()}]", $position );

		return $position;
	}

	/**
	 * Get the icon of the page in the administration menu.
	 *
	 * @since  0.4.0
	 * @return mixed The icon URL of the page in the administration menu.
	 */
	public function icon()
	{
		$icon = null;
		$icon = apply_filters( 'ev_admin_page_icon', $icon );
		$icon = apply_filters( "ev_admin_page_icon[page:{$this->handle()}]", $icon );

		return $icon;
	}

	/**
	 * Register the admin page in WordPress, appending it to the admin menu.
	 *
	 * @since 0.1.0
	 */
	public function register()
	{
		add_theme_page(
			$this->title(),
			$this->menu_title(),
			$this->capability(),
			$this->handle(),
			array( $this, 'render' ),
			$this->icon(),
			$this->position()
		);
	}

}