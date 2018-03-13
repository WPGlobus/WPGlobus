<?php
/**
 * wpglobus_ace_editor
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobusOptions_wpglobus_ace_editor' ) ):

	/**
	 * Class WPGlobusOptions_wpglobus_ace_editor
	 */
	class WPGlobusOptions_wpglobus_ace_editor {


		/**
		 * WPGlobusOptions_wpglobus_ace_editor constructor.
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

			<div class="wpglobus-options-field wpglobus-options-field-wpglobus_ace_editor">

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
					<div id="wpglobus-options-<?php echo esc_attr( $field['id'] ); ?>"><?php
						echo esc_html( $field['value'] ); ?></div>
					<input type="hidden" id="wpglobus-options-<?php echo esc_attr( $field['id'] ); ?>_control"
							name="<?php echo esc_attr( $field['name'] ); ?>" value=""/>
					<?php if ( ! empty( $field['desc'] ) ): ?>
						<p class="description"><?php echo esc_html( $field['desc'] ); ?></p>
					<?php endif; ?>
				</div>
			</div>
			<?php

			/*
			 * <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.1/ace.js"
        integrity="sha256-m7pa1Wh06liKoIDP19avGEdTGo+LoDNxeiHhVkq2hVQ=" crossorigin="anonymous"></script>
<!--suppress JSUnresolvedLibraryURL -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.7.5/beautify.min.js"
        integrity="sha256-z3YWAUWq4ZqhJwjqxdTFwmXUOkEPpQUpdxWHCZVADA4=" crossorigin="anonymous"></script>
<!--suppress JSUnresolvedLibraryURL -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.7.5/beautify-css.min.js"
        integrity="sha256-j7ahmt6lLS5KOhBLZUivk4/awJlkM8eDP/CYbrCDqRA=" crossorigin="anonymous"></script>

			 */

			if ( ! wp_script_is( 'ace-editor-js' ) ) {
				wp_enqueue_script(
					'ace-editor-js',
					'https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.1/ace.js',
					array(),
					null,
					true
				);
			}

			if ( ! wp_script_is( 'beautify-js' ) ) {
				wp_enqueue_script(
					'beautify-js',
					'https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.7.5/beautify.min.js',
					array(),
					null,
					true
				);
			}

			if ( ! wp_script_is( 'beautify-css' ) ) {
				wp_enqueue_script(
					'beautify-css',
					'https://cdnjs.cloudflare.com/ajax/libs/js-beautify/1.7.5/beautify-css.min.js',
					array(),
					null,
					true
				);
			}

			?>
			<script>
                jQuery(function ($) {

                    var editor = ace.edit("wpglobus-options-<?php echo esc_js( $field['id'] ); ?>", {
                        mode: "ace/mode/<?php echo esc_js( $field['mode'] ); ?>",
                        minLines: 20,
                        maxLines: 20,
                        tabSize: 2,
                        showPrintMargin: false
                    });

                    var beautify = <?php echo 'css' === $field['mode'] ? 'css_beautify' : 'js_beautify'; ?>;

                    editor.getSession().setValue(beautify(editor.getValue(), {indent_size: 2}));

                    $("#form-wpglobus-options").on("submit", function () {
                        document
                            .getElementById("wpglobus-options-<?php echo esc_js( $field['id'] ); ?>_control")
                            .value = editor.getValue().replace(/[\s]+/g, " ");
                    });

                });
			</script>
			<?php


		}
	}

endif;

/**
 * @global array $field
 * @see WPGlobus_Options::page_options
 */
new WPGlobusOptions_wpglobus_ace_editor( $field );