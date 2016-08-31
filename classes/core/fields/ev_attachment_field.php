<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Attachment field class.
 *
 * @package   EvolveFramework
 * @since 	  0.4.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2016, Andrea Gandino, Simone Maranzana
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
 * Generic upload placeholder template.
 *
 * @since 0.4.0
 * @return string
 */
function ev_attachment_upload_generic_placeholder_template() {
	$placeholder_html = '<div class="ev-attachment-placeholder ev-attachment-%s-placeholder">
		<div class="ev-field-panel-controls-wrapper">
			<div class="ev-field-panel-controls-inner-wrapper">
				<a href="#" class="ev-repeatable-remove ev-upload-remove"><span class="screen-reader-text">%s</span></a>
				<span class="ev-sortable-handle ev-attachment-sortable-handle"></span>
			</div>
		</div>
		<span class="ev-attachment-placeholder-icon" data-id="%s" alt=""></span>
		<div class="ev-attachment-details">
			<span class="ev-attachment-title">%s</span>
			<a href="%s" target="_blank" rel="noopener noreferrer" class="ev-attachment-extension">%s</a>
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
	echo '<script type="text/template" data-template="ev-attachment-placeholder">';
		// echo '<span class="ev-sortable-handle"></span>';
		printf(
			ev_attachment_upload_generic_placeholder_template(),
			'{{ type }}',
			__( 'Remove', 'ev_framework' ),
			'{{ id }}',
			'{{ title }}',
			'{{ url }}',
			'{{ extension }}'
		);
	echo '</script>';
}

add_action( 'admin_print_footer_scripts', 'ev_attachment_upload_placeholder_templates' );