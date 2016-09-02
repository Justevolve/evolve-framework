<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Display a partial to integrate the link functionality in a framework field.
 *
 * @since 0.4.0
 * @param string $handle The field base handle.
 * @param array $link The link data.
 */
function ev_link_partial( $handle, $link ) {
	$link = (array) $link;

	if ( ! isset( $link['link'] ) ) {
		$link = array();
	}
	else {
		$link = $link['link'];
	}

	$handle .= '[link]';
	$link_class = 'ev-link-ctrl';

	if ( isset( $link['url'] ) && ! empty( $link['url'] ) ) {
		$link_class .= ' ev-link-on';
	}

	$url    = isset( $link['url'] ) ? $link['url'] : '';
	$target = isset( $link['target'] ) ? $link['target'] : '';
	$rel    = isset( $link['rel'] ) ? $link['rel'] : '';
	$title  = isset( $link['title'] ) ? $link['title'] : '';
	$class  = isset( $link['class'] ) ? $link['class'] : '';

	$link_hidden_inputs = sprintf( '<input data-link-url type="hidden" value="%s" name="%s[url]">', esc_attr( $url ), esc_attr( $handle ) );
	$link_hidden_inputs .= sprintf( '<input data-link-target type="hidden" value="%s" name="%s[target]">', esc_attr( $target ), esc_attr( $handle ) );
	$link_hidden_inputs .= sprintf( '<input data-link-rel type="hidden" value="%s" name="%s[rel]">', esc_attr( $rel ), esc_attr( $handle ) );
	$link_hidden_inputs .= sprintf( '<input data-link-title type="hidden" value="%s" name="%s[title]">', esc_attr( $title ), esc_attr( $handle ) );
	$link_hidden_inputs .= sprintf( '<input data-link-class type="hidden" value="%s" name="%s[class]">', esc_attr( $class ), esc_attr( $handle ) );

	printf( '<span class="%s" data-nonce="%s">',
		esc_attr( $link_class ),
		esc_attr( wp_create_nonce( 'ev_link' ) )
	);

		ev_btn(
			__( 'Link', 'ev_framework' ),
			'action',
			array(
				'attrs' => array(
					'class' => 'ev-link-ctrl-btn',
				),
				'size' => 'small',
				'icon' => 'evfw-link',
				'style' => 'round',
				'hide_text' => true
			)
		);

		echo $link_hidden_inputs;

	echo '</span>';
}

/**
 * Populate the link editing modal.
 *
 * @since 0.4.0
 */
function ev_link_modal_load() {
	if ( ! ev_is_post_nonce_valid( 'ev_link' ) ) {
		die();
	}

	if ( ! isset( $_POST['data'] ) ) {
		die();
	}

	$data = $_POST['data'];

	$url    = isset( $data['url'] ) ? $data['url'] : '';
	$target = isset( $data['target'] ) ? $data['target'] : '';
	$rel    = isset( $data['rel'] ) ? $data['rel'] : '';
	$title  = isset( $data['title'] ) ? $data['title'] : '';
	$class  = isset( $data['class'] ) ? $data['class'] : '';

	$content = '';
	$content = '<div class="ev-link-ctrls-wrapper">';
		$content .= '<div class="ev-link-url-wrapper">';
			$content .= sprintf( '<select name="url" data-nonce="%s">', esc_attr( wp_create_nonce( 'ev_link_search_entries' ) ) );

			if ( $url != '' ) {
				$content .= sprintf( '<option value="%s" data-data="%s" selected></option>',
					esc_attr( $url ),
					htmlspecialchars( json_encode( ev_find_single_entry( $url ) ), ENT_QUOTES, 'UTF-8' )
				);
			}

			$content .= '</select>';

			// $content .= sprintf( '<span class="ev-link-trigger"><span>%s</span></span>', esc_html( __( 'Tab', 'ev_framework' ) ) );
		$content .= '</div>';

		$content .= '<div class="ev-link-inner-wrapper">';
			$content .= '<div class="ev-link-radio-wrapper">';
				$content .= sprintf( '<p>%s</p>', esc_html( __( 'Open in', 'ev_framework' ) ) );
				$content .= ev_radio(
					'target',
					array(
						''       => __( 'Same tab', 'ev_framework' ),
						'_blank' => __( 'New tab', 'ev_framework' ),
					),
					$target,
					array( 'switch', 'small' ),
					false
				);
			$content .= '</div>';

			$content .= '<div class="ev-link-input-wrapper">';
				$label = __( 'Rel attribute', 'ev_framework' );
				$content .= sprintf( '<input type="text" name="rel" value="%s" placeholder="rel">', esc_attr( $rel ), esc_attr( $label ) );
				$content .= sprintf( '<span>%s</span>', $label );
			$content .= '</div>';

			$content .= '<div class="ev-link-input-wrapper">';
				$label = __( 'Title attribute', 'ev_framework' );
				$content .= sprintf( '<input type="text" name="title" value="%s" placeholder="title">', esc_attr( $title ), esc_attr( $label ) );
				$content .= sprintf( '<span>%s</span>', $label );
			$content .= '</div>';

			$content .= '<div class="ev-link-input-wrapper">';
				$label = __( 'Class attribute', 'ev_framework' );
				$content .= sprintf( '<input type="text" name="class" value="%s" placeholder="class">', esc_attr( $class ), esc_attr( $label ) );
				$content .= sprintf( '<span>%s</span>', $label );
			$content .= '</div>';
		$content .= '</div>';
	$content .= '</div>';

	$m = new Ev_SimpleModal( 'ev-link' );
	$m->render( $content );

	die();
}

add_action( 'wp_ajax_ev_link_modal_load', 'ev_link_modal_load' );

/**
 * Print a link opening tag.
 *
 * @since 0.4.0
 * @param array $data The link data.
 * @param boolean $echo Set to true to echo the link.
 * @return string
 */
function ev_link_open( $data, $echo = true ) {
	$data = (array) $data;

	if ( ! isset( $data['url'] ) || empty( $data['url'] ) ) {
		return '';
	}

	$url    = isset( $data['url'] ) ? $data['url'] : '';
	$target = isset( $data['target'] ) ? $data['target'] : '';
	$rel    = isset( $data['rel'] ) ? $data['rel'] : '';
	$title  = isset( $data['title'] ) ? $data['title'] : '';
	$class  = isset( $data['class'] ) ? $data['class'] : '';

	if ( is_numeric( $url ) ) {
		$url = get_permalink( $url );
	}

	if ( $target == '_blank' ) {
		$rel .= ' noopener noreferrer';
	}

	$link = sprintf( '<a href="%s"', esc_attr( $url ) );

	if ( $target ) {
		$link .= sprintf( ' target="%s"', esc_attr( $target ) );
	}

	if ( $rel ) {
		$link .= sprintf( ' rel="%s"', esc_attr( $rel ) );
	}

	if ( $title ) {
		$link .= sprintf( ' title="%s"', esc_attr( $title ) );
	}

	if ( $class ) {
		$link .= sprintf( ' class="%s"', esc_attr( $class ) );
	}

	$link .= '>';

	if ( $echo ) {
		echo $link;
	}

	return $link;
}

/**
 * Print a link closing tag.
 *
 * @since 0.4.0
 * @param boolean $echo Set to true to echo the link.
 * @return string
 */
function ev_link_close( $echo = true ) {
	$link = '</a>';

	if ( $echo ) {
		echo $link;
	}

	return $link;
}

/**
 * Print a link.
 *
 * @since 0.4.0
 * @param array $data The link data.
 * @param string $content The link content.
 * @param boolean $echo Set to true to echo the link.
 * @return string
 */
function ev_link( $data, $content, $echo = true ) {
	$data = (array) $data;

	if ( ! isset( $data['url'] ) || empty( $data['url'] ) ) {
		if ( $echo ) {
			echo $content;
		}

		return $content;
	}

	$link = '';

	$link .= ev_link_open( $data, false );
		$link .= $content;
	$link .= ev_link_close( false );

	if ( $echo ) {
		echo $link;
	}

	return $link;
}

/**
 * Search entries.
 *
 * @since 0.4.0
 */
function ev_link_search_entries() {
	if ( empty( $_POST ) || ! isset( $_POST['search'] ) ) {
		die( json_encode( array() ) );
	}

	$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
	$action = 'ev_link_search_entries';
	$is_valid_nonce = wp_verify_nonce( $nonce, $action );

	if ( ! $is_valid_nonce ) {
		die( json_encode( array() ) );
	}

	$args = array(
		'post_type' => 'any',
		'post_status' => 'publish',
		'posts_per_page' => 10
	);

	$args['s'] = sanitize_text_field( $_POST['search'] );
	$results = array();
	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$results[] = array(
				'id' => get_the_ID(),
				'text' => html_entity_decode( get_the_title() )
			);
		}
	}

	die( json_encode( $results ) );
}

add_action( 'wp_ajax_ev_link_search_entries', 'ev_link_search_entries' );

/**
 * Find a single entry by its ID.
 *
 * @since 1.0.0
 * @param integer $id The entry ID.
 * @return array
 */
function ev_find_single_entry( $id ) {
	if ( is_numeric( $id ) ) {
		$post = get_post( $id );

		return array(
			'id' => $id,
			'text' => html_entity_decode( $post->post_title )
		);
	}

	return array(
		'id' => $id,
		'text' => $id
	);
}