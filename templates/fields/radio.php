<?php

$data = $field->config( 'data' );
$style = $field->config( 'style' );
$value = $field->value();

ev_radio( $field->handle(), $data, $value, $style );