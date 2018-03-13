<?php
/**
 * wpglobus_multicheck
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobusOptions_wpglobus_multicheck' ) ):

	/**
	 * Class WPGlobusOptions_wpglobus_multicheck
	 */
	class WPGlobusOptions_wpglobus_multicheck {


		/**
		 * WPGlobusOptions_wpglobus_multicheck constructor.
		 *
		 * @param array $field Field attributes.
		 */
		public function __construct( $field ) {

			$this->render( $field );
		}

		/**
		 * Render the field.
		 * The "control" hidden fields are used to capture the unchecked elements.
		 *
		 * @param array $field Field attributes.
		 */
		public function render( $field ) {
			?>
			<div id="wpglobus-options-<?php echo esc_attr( $field['id'] ); ?>"
					class="wpglobus-options-field wpglobus-options-field-wpglobus_select">
				<div class="grid__item">
					<?php if ( ! empty( $field['title'] ) ) { ?>
						<p class="title">
							<?php echo esc_html( $field['title'] ); ?>
						</p>
					<?php } ?>
					<?php if ( ! empty( $field['subtitle'] ) ) { ?>
						<p class="subtitle"><?php echo esc_html( $field['subtitle'] ); ?></p>
					<?php } ?>
				</div>
				<div class="grid__item">
					<fieldset id="<?php echo esc_attr( $field['id'] ); ?>-multicheck">
						<?php foreach ( $field['options'] as $value => $attrs ): ?>
							<div>
								<input type="hidden" value="0"
										name="<?php echo esc_attr( $field['name'] ); ?>[control][<?php echo esc_attr( $value ); ?>]"/>
								<input type="checkbox"<?php checked( $attrs['checked'] ); ?>
										id="<?php echo esc_attr( $field['id'] ); ?>-<?php echo esc_attr( $value ); ?>"
										name="<?php echo esc_attr( $field['name'] ); ?>[<?php echo esc_attr( $value ); ?>]"
										value="1">
								<label for="<?php echo esc_attr( $field['id'] ); ?>-<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $attrs['label'] ); ?></label>
							</div>
						<?php endforeach; ?>
					</fieldset>
					<?php if ( ! empty( $field['desc'] ) ): ?>
						<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
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
new WPGlobusOptions_wpglobus_multicheck( $field );