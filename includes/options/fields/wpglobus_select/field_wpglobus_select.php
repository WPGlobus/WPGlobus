<?php
/**
 * File: field_wpglobus_select.php
 *
 * @package     WPGlobus\Admin\Options\Field
 * Author      WPGlobus
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobusOptions_wpglobus_select' ) ) {
	/**
	 * Class WPGlobusOptions_wpglobus_select
	 */
	class WPGlobusOptions_wpglobus_select {

		/**
		 * Field Constructor.
		 *
		 * @param array        $field
		 * @param string|array $value
		 */
		public function __construct( $field = array(), $value = '' ) {

			$this->field = $field;

			if ( ! empty( $field['value'] ) ) {
				$this->value = $field['value'];
			} else {
				$this->value = $value;
			}

			$this->render();
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since
		 */
		public function render() {
			/* @var array $parent_args */
			//$parent_args = $this->parent->args;

			$sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : '';

			if ( ! empty( $sortable ) ) { // Dummy proofing  :P.
				$this->field['multi'] = true;
			}

			if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
				if ( empty( $this->field['args'] ) ) {
					$this->field['args'] = array();
				}

				if ( 'elusive-icons' === $this->field['data'] || 'elusive-icon' === $this->field['data'] || 'elusive' === $this->field['data'] ) {
					$icons_file = dirname( __FILE__ ) . '/elusive-icons.php';
					/**
					 * Filter 'redux-font-icons-file}'
					 *
					 * @since 1.2.2
					 *
					 * @param array $icon_file File for the icons
					 */
					$icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

					/**
					 * Filter 'redux/{opt_name}/field/font/icons/file'
					 *
					 * @since 1.2.2
					 *
					 * @param array $icon_file File for the icons
					 */
					$icons_file = apply_filters( "redux/{$parent_args['opt_name']}/field/font/icons/file", $icons_file );
					if ( file_exists( $icons_file ) ) {
						require_once $icons_file;
					}
				}

				/**
				 * Undefined parent?
				 *
				 * @noinspection PhpUndefinedFieldInspection
				 */
				$this->field['options'] =
					$this->parent->get_wordpress_data( $this->field['data'], $this->field['args'] );
			}

			if ( ! empty( $this->field['data'] ) && ( 'elusive-icons' === $this->field['data'] || 'elusive-icon' === $this->field['data'] || 'elusive' === $this->field['data'] ) ) {
				$this->field['class'] .= ' font-icons';
			}

			if ( ! empty( $this->field['options'] ) ) {
				$multi = ( isset( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : '';

				$nameBrackets = '';
				if ( ! empty( $multi ) ) {
					$nameBrackets = '[]';
				}

				$placeholder =
					( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) :
						__( 'Select an item', 'wpglobus' );

				if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js.
					$select2_params = wp_json_encode( $this->field['select2'] );
					$select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

					echo '<input type="hidden" class="select2_params" value="' . esc_attr( $select2_params ) . '">';
				}

				if ( isset( $this->field['multi'], $this->field['sortable'] ) && $this->field['multi'] && $this->field['sortable'] && ! empty( $this->value ) && is_array( $this->value ) ) {
					$origOption             = $this->field['options'];
					$this->field['options'] = array();

					foreach ( $this->value as $value ) {
						$this->field['options'][ $value ] = $origOption[ $value ];
					}

					if ( count( $this->field['options'] ) < count( $origOption ) ) {
						foreach ( $origOption as $key => $value ) {
							if ( ! in_array( $key, $this->field['options'], true ) ) {
								$this->field['options'][ $key ] = $value;
							}
						}
					}
				}

				$sortable =
					( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : '';

				echo wp_kses_post( $this->render_wrapper( 'before' ) );

				echo '<select ' . esc_attr( $multi ) . ' id="' . esc_attr( $this->field['id'] ) . '-select" data-placeholder="' . esc_attr( $placeholder ) . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . $nameBrackets ) . '" class="redux-select-item ' . esc_attr( $this->field['class'] . $sortable ) . '"';

				if ( ! empty( $this->field['width'] ) ) {
					echo ' style="' . empty( $this->field['width'] ) ? '40%' : esc_attr( $this->field['width'] ) . '"';
				}

				echo ' rows="6">';
				echo '<option></option>';

				foreach ( $this->field['options'] as $k => $v ) {

					if ( is_array( $v ) ) {
						echo '<optgroup label="' . esc_attr( $k ) . '">';

						foreach ( $v as $opt => $val ) {
							$this->make_option( $opt, $val, $k );
						}

						echo '</optgroup>';

						continue;
					}

					$this->make_option( $k, $v );
				}

				echo '</select>';
			} else {
				echo '<strong>' .
					 esc_html__( 'No items of this type were found.', 'wpglobus' ) . '</strong>';
			}
			if ( ! empty( $this->field['desc'] ) ) {
				echo '<p class="description">' . esc_html( $this->field['desc'] ) . '</p>';
			}
			echo wp_kses_post( $this->render_wrapper( 'after' ) );

		} //function

		/**
		 * Method render_wrapper.
		 *
		 * @param string $wrapper
		 *
		 * @return false|string
		 */
		public function render_wrapper( $wrapper = 'before' ) {
			$render = '';
			if ( 'before' === $wrapper ) {
				ob_start();
				?>
				<div
				id="wpglobus-options-<?php echo esc_attr( $this->field['id'] ); ?>"
				class="wpglobus-options-field wpglobus-options-field-wpglobus_select"
				data-id="<?php echo esc_attr( $this->field['id'] ); ?>"
				data-type="<?php echo esc_attr( $this->field['type'] ); ?>">
				<div class="grid__item">
					<p class="title"><?php echo esc_html( $this->field['title'] ); ?></p>
					<?php if ( ! empty( $this->field['subtitle'] ) ) { ?>
						<p class="subtitle"><?php echo esc_html( $this->field['subtitle'] ); ?></p>
					<?php } ?>
				</div>
				<div class="grid__item">
				<?php
				$render = ob_get_clean();
			} elseif ( 'after' === $wrapper ) {
				?>
				</div><!-- .grid__item -->
				</div><!-- #wpglobus-options-<?php echo esc_attr( $this->field['id'] ); ?> -->
				<?php
				$render = ob_get_clean();
			}

			return $render;
		}

		/**
		 * Method make_option
		 *
		 * @param        $id
		 * @param        $value
		 * @param string $group_name
		 * @noinspection PhpUnusedParameterInspection
		 */
		private function make_option( $id, $value, $group_name = '' ) {
			if ( is_array( $this->value ) ) {
				$selected =
					( is_array( $this->value ) && in_array( $id, $this->value, true ) ) ? ' selected="selected"' : '';
			} else {
				$selected = selected( $this->value, $id, false );
			}

			echo '<option value="' . esc_attr( $id ) . '"';
			echo wp_kses_post( $selected );
			echo '>' . esc_html( $value ) . '</option>';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since ReduxFramework 1.0.0
		 */
		public function enqueue() {

			return;

			/*
			 * @var array $parent_args

			$parent_args = $this->parent->args;

			wp_enqueue_style( 'select2-css' );

			wp_enqueue_script(
				'redux-field-wpglobus_select-js',
				plugins_url( '/field_wpglobus_select' . WPGlobus::SCRIPT_SUFFIX() . '.js', __FILE__ ),
				array( 'jquery', 'select2-js', 'redux-js' ),
				WPGlobus::SCRIPT_VER(),
				true
			);

			if ( $parent_args['dev_mode'] ) {
				wp_enqueue_style(
					'redux-field-select-css',
					plugins_url( '/field_wpglobus_select.css', __FILE__ ),
					array(),
					WPGlobus::SCRIPT_VER()
				);
			}
			*/
		}
	}
}
/**
 * Go
 *
 * @see WPGlobus_Options::page_options
 * @global array $field
 */
new WPGlobusOptions_wpglobus_select( $field );
