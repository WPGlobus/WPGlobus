<?php
/**
 * File: field_wpglobus_select.php
 *
 * @package     WPGlobus\Admin\Options\Field
 * @author      WPGlobus
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
		/** @noinspection PhpUndefinedClassInspection */

		/**
		 * Field Constructor.
		 *
		 * @param array $field
		 * @param string|array $value
		 */
		public function __construct( $field = array(), $value = '' ) {

			$this->field  = $field;

			if ( ! empty($field['value']) ) {
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
			/** @var array $parent_args */
			//$parent_args = $this->parent->args;

			$sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : '';

			if ( ! empty( $sortable ) ) { // Dummy proofing  :P.
				$this->field['multi'] = true;
			}

			if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
				if ( empty( $this->field['args'] ) ) {
					$this->field['args'] = array();
				}

				if ( $this->field['data'] === 'elusive-icons' || $this->field['data'] === 'elusive-icon' || $this->field['data'] === 'elusive' ) {
					$icons_file = dirname( __FILE__ ) . '/elusive-icons.php';
					/**
					 * Filter 'redux-font-icons-file}'
					 *
					 * @param  array $icon_file File for the icons
					 */
					$icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

					/**
					 * Filter 'redux/{opt_name}/field/font/icons/file'
					 *
					 * @param  array $icon_file File for the icons
					 */
					$icons_file =
						apply_filters( "redux/{$parent_args['opt_name']}/field/font/icons/file", $icons_file );
					if ( file_exists( $icons_file ) ) {
						/** @noinspection PhpIncludeInspection */
						require_once $icons_file;
					}
				}

				$this->field['options'] =
					$this->parent->get_wordpress_data( $this->field['data'], $this->field['args'] );
			}

			if ( ! empty( $this->field['data'] ) && ( $this->field['data'] === "elusive-icons" || $this->field['data'] === "elusive-icon" || $this->field['data'] === "elusive" ) ) {
				$this->field['class'] .= " font-icons";
			}


			if ( ! empty( $this->field['options'] ) ) {
				$multi = ( isset( $this->field['multi'] ) && $this->field['multi'] ) ? ' multiple="multiple"' : "";

				$width = ' style="width: 40%;"';
				if ( ! empty( $this->field['width'] ) ) {
					$width = ' style="' . $this->field['width'] . '"';
				}

				$nameBrackets = "";
				if ( ! empty( $multi ) ) {
					$nameBrackets = "[]";
				}

				$placeholder =
					( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) :
						__( 'Select an item', 'wpglobus' );

				if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js.
					$select2_params = wp_json_encode( $this->field['select2'] );
					$select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

					echo '<input type="hidden" class="select2_params" value="' . esc_attr( $select2_params ) . '">';
				}

				/** @noinspection NotOptimalIfConditionsInspection */
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
					( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

				echo $this->render_wrapper('before');	
					
				echo '<select ' . esc_attr( $multi ) . ' id="' . esc_attr( $this->field['id'] ) . '-select" data-placeholder="' . esc_attr( $placeholder ) . '" name="' . esc_attr( $this->field['name'] . $this->field['name_suffix'] . $nameBrackets ) . '" class="redux-select-item ' . esc_attr( $this->field['class'] . $sortable ) . '"';
				echo $width; // WPCS: XSS ok, sanitization ok.
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
			if ( ! empty($this->field['desc']) ) {
				echo '<p class="description">' . $this->field['desc'] . '</p>';
			}
			echo $this->render_wrapper('after');	
			
		} //function
		
		/**
		 * @todo add doc.
		 */
		public function render_wrapper($wrapper = 'before') {
			$render = '';
			if ( 'before' == $wrapper ) {
				ob_start();
				?>
				<div 
					id="wpglobus-options-<?php echo $this->field['id']; ?>" 
					class="wpglobus-options-field wpglobus-options-field-wpglobus_select" 
					data-id="<?php echo $this->field['id']; ?>"
					data-type="<?php echo $this->field['type']; ?>">
					<div class="grid__item">
						<p class="title"><?php echo $this->field['title']; ?></p>
						<?php if ( ! empty($this->field['subtitle']) ) { 	?>
							<p class="subtitle"><?php echo $this->field['subtitle']; ?></p>
						<?php }	?>	
					</div>
					<div class="grid__item">
				<?php
				$render = ob_get_clean();
			} elseif ( 'after' == $wrapper ) {
				?>
					</div><!-- .grid__item -->
				</div><!-- #wpglobus-options-<?php echo $this->field['id']; ?> -->
				<?php				
				$render = ob_get_clean();
			}
			return $render;			
		}
		
		/**
		 * @param        $id
		 * @param        $value
		 * @param string $group_name
		 */
		private function make_option(
			$id, $value, /** @noinspection PhpUnusedParameterInspection */
			$group_name = ''
		) {
			if ( is_array( $this->value ) ) {
				$selected =
					( is_array( $this->value ) && in_array( $id, $this->value, true ) ) ? ' selected="selected"' : '';
			} else {
				$selected = selected( $this->value, $id, false );
			}

			echo '<option value="' . esc_attr( $id ) . '"';
			echo $selected; // WPCS: XSS ok.
			echo '>' . esc_html( $value ) . '</option>';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since ReduxFramework 1.0.0
		 */
		public function enqueue() {
			/** @var array $parent_args */
			
			return;
			
			
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
		}
	}
}
new WPGlobusOptions_wpglobus_select($field);