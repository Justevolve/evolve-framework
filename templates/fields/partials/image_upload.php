<?php
	$image_upload_class = '';
	$image_url = '';
	$data_attrs = array(
		"data-thumb-size=$thumb_size"
	);

	if ( ! empty( $id ) ) {
		$image_upload_class = 'ev-image-uploaded';
	}

	if ( $multiple ) {
		$data_attrs[] = 'data-multiple';

		if ( $sortable ) {
			$data_attrs[] = 'data-sortable';
		}

		$id = explode( ',', $id );
	}

	$placeholder_html = '<div class="ev-image-placeholder">
		<img data-id="%s" src="%s" alt="">
		<a href="#" class="ev-upload-remove"><span class="screen-reader-text">%s</span></a>
	</div>';

?>

<div class="ev-upload ev-image-upload <?php echo esc_attr( $image_upload_class ); ?>" <?php echo esc_attr( implode( ' ', $data_attrs ) ); ?>>
	<?php if ( count( $densities ) > 1 && ! $multiple ) : ?>
		<p class="ev-density-label"><?php echo esc_html( ev_get_density_label( $density ) ); ?></p>
	<?php endif; ?>

	<?php if ( count( $breakpoints ) > 1 && ! $multiple ) : ?>
		<p class="ev-breakpoint-label"><?php echo esc_html( ev_get_breakpoint_label( $breakpoint ) ); ?></p>
	<?php endif; ?>

	<div class="ev-image-placeholder-container">
		<?php if ( $multiple ) : ?>
			<?php foreach ( $id as $_id ) : ?>
				<?php
					$image_url = ev_get_image( $_id, $thumb_size );

					printf(
						$placeholder_html,
						esc_attr( $_id ),
						esc_url( $image_url ),
						esc_attr( $thumb_size ),
						esc_html( __( 'Remove', 'ev_framework' ) )
					);
				?>
			<?php endforeach; ?>
		<?php else : ?>
			<?php
				$image_url = ev_get_image( $id, $thumb_size );

				printf(
					$placeholder_html,
					esc_attr( $id ),
					esc_url( $image_url ),
					esc_attr( $thumb_size ),
					esc_html( __( 'Remove', 'ev_framework' ) )
				);
			?>
		<?php endif; ?>
	</div>

	<div class="ev-image-upload-action">
		<?php
			ev_btn(
				__( 'Edit', 'ev_framework' ),
				'action',
				array(
					'attrs' => array(
						'class'     => 'ev-edit-action',
					),
					'style' => 'text',
					'size'  => 'medium'
				)
			);
		?>

		<?php
			ev_btn(
				__( 'Upload', 'ev_framework' ),
				'action',
				array(
					'attrs' => array(
						'class'     => 'ev-upload-action',
					),
					'style' => 'text',
					'size'  => 'medium'
				)
			);
		?>

		<?php if ( $multiple ) : ?>
			<?php
				ev_btn(
					__( 'Remove all', 'ev_framework' ),
					'delete',
					array(
						'attrs' => array(
							'class'     => 'ev-remove-all-action',
						),
						'style' => 'text',
						'size'  => 'medium'
					)
				);
			?>
		<?php endif; ?>
	</div>

	<input type="hidden" data-id name="<?php echo esc_attr( $handle ); ?>[<?php echo esc_attr( $breakpoint ); ?>][<?php echo esc_attr( $density ); ?>][id]" value="<?php echo esc_attr( implode( ',', (array) $id ) ); ?>">
</div>