<?php if ( ! defined( 'EV_FW' ) ) die( 'Forbidden' );

/**
 * Icon field class.
 *
 * @package   EvolveFramework
 * @since 	  0.1.0
 * @version   0.1.0
 * @author 	  Evolve <info@justevolve.it>
 * @copyright Copyright (c) 2015, Andrea Gandino, Simone Maranzana
 * @link 	  https://github.com/Justevolve/evolve-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class Ev_IconField extends Ev_Field {

	/**
	 * Constructor for the icon field class.
	 *
	 * @since 0.1.0
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

		// $data['config'] = wp_parse_args( $data['config'], array(
		// 	'size' => ''
		// ) );

		parent::__construct( $data );
	}
}

/**
 * Add the icon field type to the valid registered field types.
 *
 * @since 0.1.0
 * @param array $types An array containing the valid registered field types.
 * @return array
 */
function ev_register_icon_field_type( $types ) {
	$types['icon'] = 'Ev_IconField';

	return $types;
}

add_filter( 'ev_field_types', 'ev_register_icon_field_type' );

/**
 * Localize the icon field.
 *
 * @since 0.1.0
 */
function ev_icon_field_i18n() {
	wp_localize_script( 'jquery', 'ev_icon_field', array(
		'0' => _x( 'Nothing found', 'no icons found', 'ev_framework' ),
		'1' => _x( '%s found', 'one icon found', 'ev_framework' ),
		'2' => _x( '%s found', 'multiple icons found', 'ev_framework' ),
	) );
}

add_action( 'admin_enqueue_scripts', 'ev_icon_field_i18n' );

/**
 * Append the template for the icon selection modal to the body of the page
 * for later use.
 *
 * @since 0.4.0
 */
function ev_icon_modal_template() {
	$icon_fonts = ev_get_icon_fonts();

	echo '<script type="text/template" data-template="ev-icon-modal">';
		?>
		<div class="ev-icon-sets-external-wrapper ev-modal-container ev-active">
			<div class="ev-icon-sets-inner-wrapper">
				<div class="ev-icon-set-select-wrapper">
					<div class="ev-icon-search-wrapper">
						<input type="text" placeholder="<?php echo esc_attr( __( 'Search', 'icon search', 'ev_framework' ) ); ?>" data-icon-search>
						<p class="ev-icon-search-results"></p>
					</div>

					<span class="ev-close-icon-modal"><span class="screen-reader-text"><?php echo esc_html( __( 'Close', 'ev_framework' ) ); ?></span></span>
				</div>

				<div class="ev-icon-sets">
					<?php
						foreach ( $icon_fonts as $index => $font ) : ?>
						<?php
							$set_class = 'ev-on ev-icon-set-' . $font['name'];
						?>
						<div class="<?php echo esc_attr( $set_class ); ?>">
							<h2><?php echo esc_html( $font['label'] ); ?></h2>

							<?php foreach ( $font['mapping'] as $set_icon ) : ?>
								<?php
									$icon_class = $font['prefix'] . ' ' . $set_icon . ' ev-icon ev-component';

									$set_icon_stripped = strstr( $set_icon, '-' );
								?>
								<i data-prefix="<?php echo esc_attr( $font['prefix'] ); ?>" data-set="<?php echo esc_attr( $font['name'] ); ?>" data-icon-name="<?php echo esc_attr( $set_icon ); ?>" data-icon-stripped="<?php echo esc_attr( $set_icon_stripped ); ?>" class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
							<?php endforeach; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
		<?php
	echo '</script>';
}

add_action( 'admin_print_footer_scripts', 'ev_icon_modal_template' );