<?php
/**
 * wpglobus_checkbox
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobusOptions_wpglobus_checkbox' ) ) :

	/**
	 * Class WPGlobusOptions_wpglobus_checkbox
	 */
	class WPGlobusOptions_wpglobus_checkbox {


		/**
		 * WPGlobusOptions_wpglobus_checkbox constructor.
		 *
		 * @param array $field Field attributes.
		 */
		public function __construct( $field ) {

			$this->render( $field );
		}

		/**
		 * Render the field.
		 *
		 * @param array $field Field attributes.
		 */
		public function render( $field ) {
			?>
			<div id="wpglobus-options-<?php echo esc_attr( $field['id'] ); ?>"
					class="wpglobus-options-field wpglobus-options-field-wpglobus_checkbox">
				<div class="grid__item">
					<p class="title">
						<?php echo esc_html( $field['title'] ); ?>
					</p>
					<?php if ( ! empty( $field['subtitle'] ) ) { ?>
						<p class="subtitle"><?php echo esc_html( $field['subtitle'] ); ?></p>
					<?php } ?>
				</div>
				<div class="grid__item">
					<input type="checkbox"<?php checked( $field['checked'] ); ?>
							id="<?php echo esc_attr( $field['id'] ); ?>"
							name="<?php echo esc_attr( $field['name'] ); ?>"
							value="1">
					<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['label'] ); ?></label>
					<?php if ( ! empty( $field['desc'] ) ) : ?>
						<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php
		}
	}

endif;

/**
 * @global array $field
 * @see WPGlobus_Options::page_options
 */
new WPGlobusOptions_wpglobus_checkbox( $field );
