<?php
	$icon_fonts = ev_get_icon_fonts();

	$value  = $field->value();
	$set    = isset( $value['set'] ) ? $value['set'] : '';
	$icon   = isset( $value['icon'] ) ? $value['icon'] : '';
	$prefix = isset( $value['prefix'] ) ? $value['prefix'] : '';

	$wrapper_class = '';

	if ( empty( $icon ) ) {
		$wrapper_class .= ' ev-empty';
	}
?>

<div class="ev-selected-icon-wrapper <?php echo esc_attr( $wrapper_class ); ?>">
	<i data-preview class="<?php echo esc_attr( $prefix ); ?> <?php echo esc_attr( $icon ); ?> ev-icon ev-component" aria-hidden="true"></i>
	<span class="ev-remove ev-icon-remove"></span>
</div>

<div class="ev-icon-sets-external-wrapper">
	<span class="ev-icon-sets-close"><span class="screen-reader-text"><?php echo esc_html( __( 'Close', 'ev_framework' ) ); ?></span></span>
	<div class="ev-icon-set-select-wrapper">
		<?php
			$font_sets = array();

			foreach ( $icon_fonts as $font ) {
				$font_sets[$font['name']] = $font['label'];
			}

			ev_select(
				'',
				$font_sets,
				$set
			);
		?>

		<div class="ev-icon-search-wrapper">
			<input type="text" placeholder="<?php esc_attr_e( 'Search for an icon', 'ev_framework' ); ?>" data-icon-search>
			<p class="ev-icon-search-results"></p>
		</div>
	</div>

	<div class="ev-icon-sets">
		<?php
			reset( $icon_fonts );
			$first_index = key( $icon_fonts );

			foreach ( $icon_fonts as $index => $font ) : ?>
			<?php
				$set_class = 'ev-icon-set-' . $font['name'];

				if ( $font['name'] == $set || $set == '' && $index == $first_index ) {
					$set_class .= ' ev-on';
				}
			?>
			<div class="<?php echo esc_attr( $set_class ); ?>">
				<?php foreach ( $font['mapping'] as $set_icon ) : ?>
					<?php
						$icon_class = $font['prefix'] . ' ' . $set_icon . ' ev-icon ev-component';

						if ( $set_icon == $icon ) {
							$icon_class .= ' ev-selected';
						}

						$set_icon_stripped = strstr( $set_icon, '-' );
					?>
					<i data-prefix="<?php echo esc_attr( $font['prefix'] ); ?>" data-set="<?php echo esc_attr( $font['name'] ); ?>" data-icon-name="<?php echo esc_attr( $set_icon ); ?>" data-icon-stripped="<?php echo esc_attr( $set_icon_stripped ); ?>" class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<input type="hidden" data-prefix name="<?php echo esc_attr( $field->handle() ); ?>[prefix]" value="<?php echo esc_attr( $prefix ); ?>">
<input type="hidden" data-set name="<?php echo esc_attr( $field->handle() ); ?>[set]" value="<?php echo esc_attr( $set ); ?>">
<input type="hidden" data-icon name="<?php echo esc_attr( $field->handle() ); ?>[icon]" value="<?php echo esc_attr( $icon ); ?>">