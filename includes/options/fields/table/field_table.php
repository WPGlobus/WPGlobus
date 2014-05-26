<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     ReduxFramework
 * @subpackage  Field_Date
 * @author      Daniel J Griffiths (Ghost1227)
 * @author      Dovy Paukstys
 * @version     3.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}
			
// Don't duplicate me!
if (!class_exists('ReduxFramework_table')) {

    /**
     * Main ReduxFramework_table class
     *
     * @since       1.0.0
     */
    class ReduxFramework_table {

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 * @since         1.0.0
		 * @access        public
		 * @param array  $field
		 * @param string $value
		 * @param        $parent
		 * @return \ReduxFramework_table
		 */
        function __construct($field = array(), $value = '', $parent) {

            $this->parent   = $parent;
            $this->field    = $field;
            $this->value    = $value;

			//error_log( print_r( $parent, true) );

			
        }

        /**
         * Field Render Function.
         *
         * Takes the vars and outputs the HTML for the field in the settings
         *
         * @since 		1.0.0
         * @access		public
         * @return		void
         */
        public function render() {
            // $placeholder = (isset($this->field['placeholder'])) ? ' placeholder="' . esc_attr($this->field['placeholder']) . '" ' : '';

            //echo '<input data-id="' . $this->field['id'] . '" type="text" id="' . $this->field['id'] . '-date" name="' . $this->field['name'] . $this->field['name_suffix'] . '"' . $placeholder . 'value="' . $this->value . '" class="redux-datepicker ' . $this->field['class'] . '" />';
       
			include( dirname(__FILE__) . '/table-languages.php' );
			new LanguagesTable();



		}

        /**
         * Enqueue Function.
         *
         * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
         *
         * @since 		1.0.0
         * @access		public
         * @return		void
         */
        public function enqueue() {

			wp_enqueue_style(
				'redux-table-css',
				#ReduxFramework::$_url . 'inc/fields/select/field_select.css',
				plugins_url( '/field_table.css', __FILE__ ),
				time(),
				true
			);

			wp_enqueue_script(
				'field-table-js',
				#ReduxFramework::$_url . 'inc/fields/select/field_select.js',
				plugins_url( '/field_table.js', __FILE__ ),
				array('jquery'),
				time(),
				true
			);

			/*
            wp_enqueue_script(
                'redux-field-date-js', 
                ReduxFramework::$_url . 'inc/fields/date/field_date.js', 
                array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'), 
                time(), 
                true
            );		// */
        }
    }
}
