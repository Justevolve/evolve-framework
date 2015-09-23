<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Attachment field class.
 *
 * @package   EvolveFramework
 * @since 	  0.4.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_AttachmentField extends Ev_Field {

	/**
	 * Constructor for the attachment field class.
	 *
	 * @since 0.4.0
	 * @param array $data The field data structure.
	 */
	public function __construct( $data )
	{
		if ( ! isset( $data['default'] ) ) {
			$data['default'] = '';
		}

		if ( ! isset( $data['config'] ) ) {
			$data['config'] = array();
		}

		$data['config'] = wp_parse_args( $data['config'], array(
			/* Attachment type. */
			'type'  => '',

			/* Allows for multiple attachments upload. */
			'multiple'    => false,

			/* Allows for multiple attachments to be manually sorted. */
			'sortable'    => false,

			/* Image size on backend UI. */
			'thumb_size'  => 'medium',
		) );

		if ( ! in_array( $data['config']['type'], array( '', 'image', 'audio', 'video', 'application' ) ) ) {
			$data['config']['type'] = '';
		}

		parent::__construct( $data );
	}
}

/**
 * Add the attachment field type to the valid registered field types.
 *
 * @since 0.4.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_attachment_field_type( $types ) {
	$types['attachment'] = 'Ev_AttachmentField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_attachment_field_type' );

/**
 * Image upload placeholder template.
 *
 * @since 0.4.0
 * @return string
 */
function ev_attachment_upload_image_placeholder_template() {
	$placeholder_html = '<div class="ev-attachment-placeholder ev-attachment-image-placeholder">
		<img data-id="%s" src="%s" alt="">
		<a href="#" class="ev-upload-remove"><span class="screen-reader-text">%s</span></a>
	</div>';

	return $placeholder_html;
}

/**
 * Generic upload placeholder template.
 *
 * @since 0.4.0
 * @return string
 */
function ev_attachment_upload_generic_placeholder_template( $type = '' ) {
	$placeholder_html = '<div class="ev-attachment-placeholder ev-attachment-' . $type . '-placeholder">
		<span class="ev-attachment-placeholder-icon" data-id="%s" alt="">
			<a href="#" class="ev-upload-remove"><span class="screen-reader-text">%s</span></a>
		</span>
		<div class="ev-attachment-details">
			<ul>
				<li class="ev-attachment-title">%s</li>
				<li class="ev-attachment-extension">%s</li>
			</ul>
		</div>
	</div>';

	return $placeholder_html;
}

/**
 * Append the template for the attachment upload placeholder to the body of the page
 * for later use.
 *
 * @since 0.4.0
 */
function ev_attachment_upload_placeholder_templates() {
	/* Image upload template. */
	echo '<script type="text/template" data-template="ev-attachment-image-placeholder">';
		printf(
			ev_attachment_upload_image_placeholder_template(),
			'{{ id }}',
			'{{ url }}',
			__( 'Remove', 'ev_framework' )
		);
	echo '</script>';

	/* Audio upload template */
	echo '<script type="text/template" data-template="ev-attachment-audio-placeholder">';
		printf(
			ev_attachment_upload_generic_placeholder_template( 'audio' ),
			'{{ id }}',
			__( 'Remove', 'ev_framework' ),
			'{{ title }}',
			'{{ extension }}'
		);
	echo '</script>';

	/* Video upload template */
	echo '<script type="text/template" data-template="ev-attachment-video-placeholder">';
		printf(
			ev_attachment_upload_generic_placeholder_template( 'video' ),
			'{{ id }}',
			__( 'Remove', 'ev_framework' ),
			'{{ title }}',
			'{{ extension }}'
		);
	echo '</script>';

	/* Application upload template */
	echo '<script type="text/template" data-template="ev-attachment-application-placeholder">';
		printf(
			ev_attachment_upload_generic_placeholder_template( 'application' ),
			'{{ id }}',
			__( 'Remove', 'ev_framework' ),
			'{{ title }}',
			'{{ extension }}'
		);
	echo '</script>';
}

add_action( 'admin_print_footer_scripts', 'ev_attachment_upload_placeholder_templates' );