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

if ( ! class_exists( 'ReduxFramework_wpglobus_select' ) ) {
	/**
	 * Class ReduxFramework_wpglobus_select
	 */
	class ReduxFramework_wpglobus_select {
		/** @noinspection PhpUndefinedClassInspection */

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @since ReduxFramework 1.0.0
		 * @param array          $field
		 * @param string|array   $value
		 * @param ReduxFramework $parent
		 */
		public function __construct( $field = array(), $value = '', $parent ) {
			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;
		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since ReduxFramework 1.0.0
		 */
		public function render() {
			/** @var array $parent_args */
			$parent_args = $this->parent->args;

			$sortable = ( isset( $this->field['sortable'] ) && $this->field['sortable'] ) ? ' select2-sortable"' : "";

			if ( ! empty( $sortable ) ) { // Dummy proofing  :P
				$this->field['multi'] = true;
			}

			if ( ! empty( $this->field['data'] ) && empty( $this->field['options'] ) ) {
				if ( empty( $this->field['args'] ) ) {
					$this->field['args'] = array();
				}

				if ( $this->field['data'] === "elusive-icons" || $this->field['data'] === "elusive-icon" || $this->field['data'] === "elusive" ) {
					$icons_file = dirname( __FILE__ ) . '/elusive-icons.php';
					/**
					 * filter 'redux-font-icons-file}'
					 *
					 * @param  array $icon_file File for the icons
					 */
					$icons_file = apply_filters( 'redux-font-icons-file', $icons_file );

					/**
					 * filter 'redux/{opt_name}/field/font/icons/file'
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
			//if

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
					( isset( $this->field['placeholder'] ) ) ? esc_attr( $this->field['placeholder'] ) : __( 'Select an item', 'redux-framework' );

				if ( isset( $this->field['select2'] ) ) { // if there are any let's pass them to js
					$select2_params = json_encode( $this->field['select2'] );
					$select2_params = htmlspecialchars( $select2_params, ENT_QUOTES );

					echo '<input type="hidden" class="select2_params" value="' . $select2_params . '">';
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

				echo '<select ' . $multi . ' id="' . $this->field['id'] . '-select" data-placeholder="' . $placeholder . '" name="' . $this->field['name'] . $this->field['name_suffix'] . $nameBrackets . '" class="redux-select-item ' . $this->field['class'] . $sortable . '"' . $width . ' rows="6">';
				echo '<option></option>';

				foreach ( $this->field['options'] as $k => $v ) {

					if ( is_array( $v ) ) {
						echo '<optgroup label="' . $k . '">';

						foreach ( $v as $opt => $val ) {
							$this->make_option( $opt, $val, $k );
						}

						echo '</optgroup>';

						continue;
					}

					$this->make_option( $k, $v );
				}
				//foreach

				echo '</select>';
			} else {
				echo '<strong>' . __( 'No items of this type were found.', 'redux-framework' ) . '</strong>';
			}
		} //function

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

			echo '<option value="' . $id . '"' . $selected . '>' . $value . '</option>';
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since ReduxFramework 1.0.0
		 */
		public function enqueue() {
			/** @var array $parent_args */
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
					plugins_url( '/field_wpglobus_select' . WPGlobus::SCRIPT_SUFFIX() . '.css', __FILE__ ),
					array(),
					WPGlobus::SCRIPT_VER()
				);
			}
		} //function
	} //class
}
