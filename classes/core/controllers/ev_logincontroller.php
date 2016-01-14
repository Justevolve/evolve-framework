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

class Ev_LoginController extends Ev_Controller {

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
		add_action( 'login_enqueue_scripts', array( $this, 'enqueue_scripts' ), apply_filters( 'ev_login_enqueue_scripts_priority', 20 ) );
	}

}