<?php
$style = $field->config( 'style' );

ev_select( $field->handle(), $field->config( 'data' ), $field->value(), $style );