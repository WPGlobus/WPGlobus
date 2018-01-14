<?php
/**
 * File: field_table.php
 *
 * @package     WPGlobus\Admin\Options\Field
 * @author      WPGlobus
 */

// .
if ( ! class_exists( 'ReduxFramework_table' ) ) {

	/**
	 * Main ReduxFramework_table class
	 */
	class ReduxFramework_table {

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @param array          $field  Field.
		 * @param string         $value  Value.
		 * @param ReduxFramework $parent Parent.
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
		 * @return        void
		 */
		public function render() {

			require_once dirname( __FILE__ ) . '/class-wpglobus-languages-table.php';
			new WPGlobus_Languages_Table();

		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @return void
		 */
		public function enqueue() {

			wp_enqueue_style(
				'wpglobus-redux-field-table',
				plugins_url( '/field_table' . WPGlobus::SCRIPT_SUFFIX() . '.css', __FILE__ ),
				array(),
				WPGlobus::SCRIPT_VER()
			);

			wp_enqueue_script(
				'wpglobus-redux-field-table',
				plugins_url( '/field_table' . WPGlobus::SCRIPT_SUFFIX() . '.js', __FILE__ ),
				array( 'jquery' ),
				WPGlobus::SCRIPT_VER(),
				true
			);
		}
	}
}
