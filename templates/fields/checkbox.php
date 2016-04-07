<?php
$style = $field->config( 'style' );
$args = array();

ev_checkbox( $field->handle(), $field->value(), $style, $args );