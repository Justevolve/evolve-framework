<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Add a new button to the main editor toolbar.
 *
 * @since 1.0.8
 * @param array $buttons An array of TinyMCE buttons.
 * @return array
 */
function ev_editr_register_buttons( $buttons ) {
   array_push( $buttons, 'separator', 'ev_editr' );

   return $buttons;
}

add_filter( 'mce_buttons', 'ev_editr_register_buttons' );

/**
 * Load the TinyMCE plugin : editor_plugin.js (wp2.5)
 *
 * @since 1.0.8
 * @param array $plugin_array An array of TinyMCE plugins
 * @return array
 */
function ev_editr_register_tinymce_javascript( $plugin_array ) {
   $plugin_array[ 'ev_editr' ] = plugins_url( '/assets/js/admin/tinymce/editr.js', EV_FRAMEWORK_MAIN_FILE_PATH );

   return $plugin_array;
}

add_filter( 'mce_external_plugins', 'ev_editr_register_tinymce_javascript' );

/**
 * Localize the additional formats.
 *
 * @since 1.0.8
 */
function ev_editr_localize() {
	wp_localize_script( 'jquery', 'ev_editr', apply_filters( 'ev_editr_formats', array() ) );
}

add_action( 'admin_enqueue_scripts', 'ev_editr_localize' );