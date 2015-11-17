<?php

$data = $field->config( 'data' );

$style = $field->config( 'style' );
$graphic = $style == 'graphic';

$value = $field->value();

ev_radio( $field->handle(), $data, $value, $style );