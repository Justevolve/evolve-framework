<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Query class. This class is a wrapper to the WPQuery class that allows
 * blocks and components to perform custom loops.
 *
 * @package   EvolveFramework
 * @since 	  1.0.0
 * @version   1.0.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
class Ev_Query {

	/**
	 * The query arguments.
	 *
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query
	 * @var array
	 */
	protected $_args = array();

	/**
	 * The query object.
	 *
	 * @see https://codex.wordpress.org/Class_Reference/WP_Query
	 * @var WP_Query
	 */
	public $_query = null;

	/**
	 * Constructor for the query class.
	 *
	 * @since 0.1.0
	 * @param array $args The query arguments.
	 */
	public function __construct( $args = array() )
	{
		$this->_args = $this->parse_args( $args );
	}

	/**
	 * Set a query parameter.
	 *
	 * @since 0.1.0
	 * @param string $arg The query parameter.
	 * @param mixed $value The query parameter value.
	 */
	public function set_query_arg( $arg, $value )
	{
		$this->_args[$arg] = $value;
	}

	/**
	 * Include entries belonging to a specific taxonomy term in query results.
	 *
	 * @since 0.1.0
	 * @param string $taxonomy The taxonomy slug.
	 * @param integer $term_id The term ID.
	 */
	public function include_term( $taxonomy, $term_id )
	{
		if ( ! isset( $this->_args['tax_query'] ) ) {
			$this->_args['tax_query'] = array();
		}

		$found = false;
		$term = get_term_by( 'id', $term_id, $taxonomy );

		if ( ! term_exists( $term->name, $taxonomy ) ) {
			return;
		}

		foreach ( $this->_args['tax_query'] as &$tax_query ) {
			if ( $tax_query['taxonomy'] == $taxonomy && $tax_query['operator'] == 'IN' ) {
				$found = true;
				$tax_query['terms'][] = $term_id;
				break;
			}
		}

		if ( ! $found  ) {
			$this->_args['tax_query'][] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'ID',
				'terms'            => array( $term_id ),
				'operator'         => 'IN',
				'include_children' => true
			);
		}
	}

	/**
	 * Exclude entries belonging to a specific taxonomy term in query results.
	 *
	 * @since 0.1.0
	 * @param string $taxonomy The taxonomy slug.
	 * @param integer $term_id The term ID.
	 */
	public function exclude_term( $taxonomy, $term_id )
	{
		if ( ! isset( $this->_args['tax_query'] ) ) {
			$this->_args['tax_query'] = array();
		}

		$found = false;
		$term = get_term_by( 'id', $term_id, $taxonomy );

		if ( ! term_exists( $term->name, $taxonomy ) ) {
			return;
		}

		foreach ( $this->_args['tax_query'] as &$tax_query ) {
			if ( $tax_query['taxonomy'] == $taxonomy && $tax_query['operator'] == 'NOT IN' ) {
				$found = true;
				$tax_query['terms'][] = $term_id;
				break;
			}
		}

		if ( ! $found  ) {
			$this->_args['tax_query'][] = array(
				'taxonomy'         => $taxonomy,
				'field'            => 'ID',
				'terms'            => array( $term_id ),
				'operator'         => 'NOT IN',
				'include_children' => true
			);
		}
	}

	/**
	 * Paginate query results.
	 *
	 * @since 0.1.0
	 */
	public function paginate()
	{
		$paged = 1;

		if ( get_query_var( 'paged' ) ) {
			$paged = get_query_var( 'paged' );
		}
		elseif ( get_query_var( 'page' ) ) {
			$paged = get_query_var( 'page' );
		}

		$this->_args['paged'] = $paged;
	}

	/**
	 * Parse the query arguments.
	 *
	 * @since 0.1.0
	 * @param array $args The query arguments.
	 * @return array
	 */
	private function parse_args( $args )
	{
		$default_args = array(
			'post_status' => 'publish'
		);

		$args = wp_parse_args( $args, $default_args );

		return $args;
	}

	/**
	 * Instantiate the query object and run it.
	 *
	 * @since 0.1.0
	 */
	private function run()
	{
		$this->_args = apply_filters( 'ev_query_args', $this->_args );
		$this->_query = new WP_Query( $this->_args );
	}

	/**
	 * Whether current query has results to loop over.
	 *
	 * @since 0.1.0
	 * @see WP_Query::have_posts()
	 * @return bool
	 */
	public function have_posts()
	{
		if ( ! $this->_query ) {
			$this->run();
		}

		return $this->_query->have_posts();
	}

	/**
	 * Iterate the post index in the loop.
	 *
	 * @since 0.1.0
	 * @see WP_Query::the_post()
	 */
	public function the_post()
	{
		if ( ! $this->_query ) {
			return false;
		}

		return $this->_query->the_post();
	}

}