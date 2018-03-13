<?php
/**
 * wpglobus_info
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'WPGlobusOptions_wpglobus_info' ) ) {

	/**
	 * Main WPGlobusOptions_wpglobus_info class.
	 */
	class WPGlobusOptions_wpglobus_info {

		/**
		 * Field Constructor.
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

			$class = empty( $field['class'] ) ? '' : ' ' . $field['class'];
			?>
			<div id="wpglobus-options-<?php echo esc_attr( $field['id'] ); ?>"
					class="wpglobus-options-field wpglobus-options-field-<?php echo esc_attr( $field['id'] ); ?> wpglobus-options-field-<?php echo esc_attr( $field['type'] ); ?><?php echo esc_attr( $class ); ?>">
				<?php if ( ! empty( $field['title'] ) ) { ?>
					<p class="title"><?php echo esc_html( $field['title'] ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $field['subtitle'] ) ) { ?>
					<p class="subtitle"><?php echo esc_html( $field['subtitle'] ); ?></p>
				<?php } ?>
				<?php if ( ! empty( $field['html'] ) ) { ?>
					<?php echo wp_kses_post( $field['html'] ); ?>
				<?php } ?>
				<?php if ( ! empty( $field['desc'] ) ) { ?>
					<p class="description"><?php echo wp_kses_post( $field['desc'] ); ?></p>
				<?php } ?>
			</div>
			<div style="clear:both;"></div>
			<?php

		}
	}
}

/**
 * @global array $field
 * @see WPGlobus_Options::page_options
 */
new WPGlobusOptions_wpglobus_info( $field );
