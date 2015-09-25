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
				$title = get_the_title( $id );
				$file = get_attached_file( $id );
				$extension = basename( $file );
				$size = size_format( filesize( $file ) );
				$extension = sprintf( "%s (%s)", esc_html( $extension ), esc_html( $size ) );
				$type = '';

				if ( wp_attachment_is_image( $id ) ) {
					$type = 'image';
				}
				elseif ( wp_attachment_is( 'audio', $id ) ) {
					$type = 'audio';
				}
				elseif ( wp_attachment_is( 'video', $id ) ) {
					$type = 'video';
				}
				else {
					$check = wp_check_filetype( $file );

					if ( isset( $check['ext'] ) ) {
						$type = $check['ext'];
					}
					else {
						$type = 'unknown';
					}
				}

				printf(
					ev_attachment_upload_generic_placeholder_template(),
					$type,
					$id,
					__( 'Remove', 'ev_framework' ),
					$title,
					$extension
				);
			}
		}
	?>

	<div class="ev-attachment-upload-action">
		<a href="#" class="ev-edit-action"><?php esc_html_e( 'Edit', 'ev_framework' ); ?></a>
		<a href="#" class="ev-upload-action"><?php esc_html_e( 'Upload', 'ev_framework' ); ?></a>
	</div>

	<input type="hidden" data-id name="<?php echo esc_attr( $handle ); ?>" value="<?php echo esc_attr( $value ); ?>">
<?php echo '</div>';