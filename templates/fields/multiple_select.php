<?php
	$args = array();

	if ( $field->config( 'vertical' ) === true ) {
		$args['vertical'] = true;
	}

	if ( $field->config( 'max' ) !== false ) {
		$args['max'] = (int) $field->config( 'max' );
	}

	if ( $field->config( 'create' ) !== false ) {
		$args['create'] = (int) $field->config( 'create' );
	}

	$data = $field->config( 'data' );

	if ( is_array( $data ) ) {
		ev_multiple_select( $field->handle(), $data, $field->value(), $args );
	}
	elseif ( is_string( $data ) ) {
		$data_callback = $field->config( 'data_callback' );

		if ( $data_callback && is_callable( $data_callback ) ) {
			$args['data_callback'] = $data_callback;

			ev_multiple_select_ajax( $field->handle(), $data, $field->value(), $args );
		}
	}

?>