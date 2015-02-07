<?php

if ( ! class_exists( 'ReduxFramework_table' ) ) {

	/**
	 * Main ReduxFramework_table class
	 */
	class ReduxFramework_table {

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @param array  $field
		 * @param string $value
		 * @param        $parent
		 *
		 * @return \ReduxFramework_table
		 */
		function __construct( $field = array(), $value = '', $parent ) {

			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;

		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 * @return        void
		 */
		public function render() {

			include( dirname( __FILE__ ) . '/table-languages.php' );
			new LanguagesTable();

		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 * @return void
		 */
		public function enqueue() {

			$suffix = '.min';
			$ver    = date( 'ymd' );
			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$suffix = '';
				$ver    = sprintf( 'debug-%d', time() );
			}

			wp_enqueue_style(
				'wpglobus-redux-field-table',
				plugins_url( '/field_table' . $suffix . '.css', __FILE__ ),
				array(),
				$ver,
				true
			);

			wp_enqueue_script(
				'wpglobus-redux-field-table',
				plugins_url( '/field_table' . $suffix . '.js', __FILE__ ),
				array( 'jquery' ),
				$ver,
				true
			);

		}

	} // class
}

# --- EOF