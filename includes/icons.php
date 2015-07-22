<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Load the imported icon fonts on admin.
 *
 * @since 0.1.0
 */
function ev_load_admin_icon_fonts() {
	foreach ( ev_get_icon_fonts() as $icon_font ) {
		ev_fw()->admin()->add_style(
			'ev-icon-font-' . $icon_font['name'],
			$icon_font['url']
		);
	}
}

add_action( 'admin_init', 'ev_load_admin_icon_fonts' );

/**
 * Return a list of the imported icon fonts to be used with the icon picker
 * field.
 *
 * To add a new icon font, hook into the `ev_get_icon_fonts` filter and append
 * the following array:
 *
 * $fonts[] = array(
 * 	 'name'    => Library name,
 *   'label'   => Library label,
 *   'url'     => URL TO THE ICON FONT CSS FILE,
 *   'prefix'  => '',  // Library CSS prefix, optional
 *   'mapping' => array(
 *   	'fa-envelope-o',
 *    	'fa-heart',
 *     	// other mappings
 * );
 *
 * @since 0.1.0
 * @return array
 */
function ev_get_icon_fonts() {
	return apply_filters( 'ev_get_icon_fonts', array() );
}