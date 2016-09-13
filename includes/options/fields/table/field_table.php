<?php
/**
 * File: field_table.php
 *
 * @package     WPGlobus\Admin\Options\Field
 * @author      WPGlobus
 */

if ( ! class_exists( 'ReduxFramework_table' ) ) {

	/**
	 * Main ReduxFramework_table class
	 */
	class ReduxFramework_table {
		/** @noinspection PhpUndefinedClassInspection */

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @param array          $field
		 * @param string         $value
		 * @param ReduxFramework $parent
		 * @return ReduxFramework_table
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

			include( dirname( __FILE__ ) . '/table-languages.php' );
			new LanguagesTable();

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

	} // class
}

# --- EOF
