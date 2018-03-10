<?php
/**
 * File: field_table.php
 *
 * @package     WPGlobus\Admin\Options\Field
 * @author      WPGlobus
 */

if ( ! class_exists( 'WPGlobusOptions_table' ) ) {

	/**
	 * Main WPGlobusOptions_table class.
	 */
	class WPGlobusOptions_table {
	
		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @param array          $field  Field.
		 * @param string         $value  Value.
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
		 * @return        void
		 */
		public function render() {

			require_once dirname( __FILE__ ) . '/class-wpglobus-languages-table.php';
			new WPGlobus_Languages_Table($this->field);

		}
		
	}
}
new WPGlobusOptions_table($field);
