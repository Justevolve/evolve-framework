<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Date field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_DateField extends Ev_Field {

	/**
	 * Constructor for the date field class.
	 *
	 * @since 0.1.0
	 * @param array $data The field data structure.
	 */
	public function __construct( $data )
	{
		if ( ! isset( $data['default'] ) ) {
			$data['default'] = '';
		}

		if ( ! isset( $data['config'] ) ) {
			$data['config'] = array();
		}

		$data['config'] = wp_parse_args( $data['config'], array(
			'style'  => '',
			'size'   => '',
			'format' => 'yy-mm-dd'
		) );

		parent::__construct( $data );
	}
}

/**
 * Add the date field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_date_field_type( $types ) {
	$types['date'] = 'Ev_DateField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_date_field_type' );

/**
 * Localize the date field.
 *
 * @since 0.1.0
 */
function ev_date_field_i18n() {
	wp_localize_script( 'jquery', 'ev_date_field', array(
		'dayNames' => array(
			__( 'Sunday' ),
			__( 'Monday' ),
			__( 'Tuesday' ),
			__( 'Wednesday' ),
			__( 'Thursday' ),
			__( 'Friday' ),
			__( 'Saturday' )
		),
		'dayNamesShort' => array(
			_x( 'Su', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'Mo', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'Tu', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'We', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'Th', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'Fr', 'jquery ui datepicker short day name', 'ev_framework' ),
			_x( 'Sa', 'jquery ui datepicker short day name', 'ev_framework' )
		),
		'monthNames' => array(
			__( 'January' ),
			__( 'February' ),
			__( 'March' ),
			__( 'April' ),
			__( 'May' ),
			__( 'June' ),
			__( 'July' ),
			__( 'August' ),
			__( 'September' ),
			__( 'October' ),
			__( 'November' ),
			__( 'December' )
		),
		'monthNamesShort' => array(
			__( 'Jan' ),
			__( 'Feb' ),
			__( 'Mar' ),
			__( 'Apr' ),
			__( 'May' ),
			__( 'Jun' ),
			__( 'Jul' ),
			__( 'Aug' ),
			__( 'Sep' ),
			__( 'Oct' ),
			__( 'Nov' ),
			__( 'Dec' )
		),
		'prevText' => _x( 'Prev', 'jquery ui datepicker prev text', 'ev_framework' ),
		'nextText' => _x( 'Next', 'jquery ui datepicker next text', 'ev_framework' )
	) );
}

add_action( 'admin_enqueue_scripts', 'ev_date_field_i18n' );