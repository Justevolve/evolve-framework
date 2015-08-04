<?php
	$args = array();

	if ( $field->config( 'vertical' ) === true ) {
		$args['vertical'] = true;
	}

	ev_multiple_select( $field->handle(), $field->config( 'data' ), $field->value(), $args );
?>