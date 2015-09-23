<?php

$value      = $field->value();
$handle     = $field->handle();
$thumb_size = $field->config( 'thumb_size' );
$multiple   = $field->config( 'multiple' );
$sortable   = $field->config( 'sortable' );
$type       = $field->config( 'type' );

$data_attrs = array(
	'data-type="' . esc_attr( $type ) . '"',
	'data-thumb-size="' . esc_attr( $thumb_size ) . '"'
);

if ( $multiple ) {
	$data_attrs[] = 'data-multiple';

	if ( $sortable ) {
		$data_attrs[] = 'data-sortable';
	}
}

$container_class = '';

if ( ! empty( $value ) ) {
	$container_class .= ' ev-attachment-uploaded';
}

printf( '<div class="ev-attachment-upload-container %s" %s>', esc_attr( $container_class ), implode( ' ', $data_attrs ) ); ?>
	<?php
		if ( ! empty( $value ) ) {
			foreach ( explode( ',', $value ) as $id ) {
				if ( wp_attachment_is_image( $id ) ) {
					printf(
						ev_attachment_upload_image_placeholder_template(),
						$id,
						ev_fw_get_image( $id, $thumb_size ),
						__( 'Remove', 'ev_framework' )
					);
				}
				else {
					$title = get_the_title( $id );
					$extension = basename( get_attached_file( $id ) );
					$size = size_format( filesize( get_attached_file( $id ) ) );

					$extension = sprintf( "%s (%s)", $extension, $size );

					if ( wp_attachment_is( 'audio', $id ) ) {
						printf(
							ev_attachment_upload_generic_placeholder_template( 'audio' ),
							$id,
							__( 'Remove', 'ev_framework' ),
							$title,
							$extension
						);
					}
					elseif ( wp_attachment_is( 'video', $id ) ) {
						printf(
							ev_attachment_upload_generic_placeholder_template( 'video' ),
							$id,
							__( 'Remove', 'ev_framework' ),
							$title,
							$extension
						);
					}
					else {
						printf(
							ev_attachment_upload_generic_placeholder_template( 'application' ),
							$id,
							__( 'Remove', 'ev_framework' ),
							$title,
							$extension
						);
					}
				}
			}
		}
	?>

	<div class="ev-attachment-upload-action">
		<a href="#" class="ev-edit-action"><?php esc_html_e( 'Edit', 'ev_framework' ); ?></a>
		<a href="#" class="ev-upload-action"><?php esc_html_e( 'Upload', 'ev_framework' ); ?></a>
	</div>

	<input type="hidden" data-id name="<?php echo esc_attr( $handle ); ?>" value="<?php echo esc_attr( $value ); ?>">
<?php echo '</div>';