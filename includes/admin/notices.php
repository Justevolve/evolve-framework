<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Add an update notice to the admin static notices queue.
 *
 * @since 0.1.0
 * @param string $message The notice message.
 */
function ev_updated_notice( $message ) {
	ev_fw()->admin()->add_notice( $message, 'updated' );
}

/**
 * Add an error notice to the admin static notices queue.
 *
 * @since 0.1.0
 * @param string $message The notice message.
 */
function ev_error_notice( $message ) {
	ev_fw()->admin()->add_notice( $message, 'error' );
}

/**
 * Add a static notice to the admin static notices queue.
 *
 * @since 0.1.0
 * @param string $message The notice message.
 */
function ev_static_notice( $message ) {
	ev_fw()->admin()->add_notice( $message, 'ev-static-notice' );
}

/**
 * Add an update nag notice to the admin static notices queue.
 *
 * @since 0.1.0
 * @param string $message The notice message.
 */
function ev_update_nag_notice( $message ) {
	ev_fw()->admin()->add_notice( $message, 'update-nag' );
}