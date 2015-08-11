<?php

/**
 * Redux Framework is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 * Redux Framework is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with Redux Framework. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     Redux_Field
 * @subpackage  ACE_Editor
 * @version     3.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'ReduxFramework_wpglobus_ace_editor' ) ) {
	/**
	 * Class ReduxFramework_wpglobus_ace_editor
	 */
	class ReduxFramework_wpglobus_ace_editor {
		/** @noinspection PhpUndefinedClassInspection */

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @since ReduxFramework 1.0.0
		 * @param array          $field
		 * @param string         $value
		 * @param ReduxFramework $parent
		 */
		public function __construct( $field = array(), $value = '', $parent ) {
			$this->parent = $parent;
			$this->field  = $field;
			$this->value  = $value;

			if ( is_array( $this->value ) ) {
				$this->value = '';
			} else {
				$this->value = trim( $this->value );
			}

			if ( ! empty( $this->field['options'] ) ) {
				$this->field['args'] = $this->field['options'];
				unset( $this->field['options'] );
			}

		}

		/**
		 * Field Render Function.
		 * Takes the vars and outputs the HTML for the field in the settings
		 *
		 * @since ReduxFramework 1.0.0
		 */
		public function render() {

			if ( ! isset( $this->field['mode'] ) ) {
				$this->field['mode'] = 'javascript';
			}
			if ( ! isset( $this->field['theme'] ) ) {
				$this->field['theme'] = 'monokai';
			}

			$params = array(
				'minLines' => 10,
				'maxLines' => 30,
			);

			if ( isset( $this->field['args'] ) && ! empty( $this->field['args'] ) && is_array( $this->field['args'] ) ) {
				$params = wp_parse_args( $this->field['args'], $params );
			}

			?>
			<div class="ace-wrapper">
				<input type="hidden" class="localize_data"
				       value="<?php echo htmlspecialchars( json_encode( $params ) ); ?>"/>
                <textarea name="<?php echo $this->field['name'] . $this->field['name_suffix']; ?>"
                          id="<?php echo $this->field['id']; ?>-textarea"
                          class="ace-editor hide <?php echo $this->field['class']; ?>"
                          data-editor="<?php echo $this->field['id']; ?>-editor"
                          data-mode="<?php echo $this->field['mode']; ?>"
                          data-theme="<?php echo $this->field['theme']; ?>">
                    <?php echo $this->value; ?>
                </textarea>
                <pre id="<?php echo $this->field['id']; ?>-editor"
                     class="ace-editor-area"><?php echo htmlspecialchars( $this->value ); ?></pre>
			</div>
			<?php
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 *
		 * @since       1.0.0
		 * @access      public
		 * @return      void
		 */
		public function enqueue() {

			/** @var array $parent_args */
			$parent_args = $this->parent->args;

			if ( $parent_args['dev_mode'] && ! wp_style_is( 'redux-field-wpglobus_ace-editor-css' ) ) {
				wp_enqueue_style(
					'redux-field-wpglobus_ace-editor-css',
					plugins_url( '/field_wpglobus_ace_editor' . WPGlobus::SCRIPT_SUFFIX() . '.css', __FILE__ ),
					array(),
					WPGlobus::SCRIPT_VER()
				);
			}

			if ( ! wp_script_is( 'ace-editor-js' ) ) {
				wp_enqueue_script(
					'ace-editor-js',
					'//cdn.jsdelivr.net/ace/1.2.0/min/ace.js',
					array( 'jquery' ),
					null,
					true
				);
			}

			if ( ! wp_script_is( 'redux-field-wpglobus_ace_editor-js' ) ) {
				wp_enqueue_script(
					'redux-field-wpglobus_ace_editor-js',
					plugins_url( '/field_wpglobus_ace_editor' . WPGlobus::SCRIPT_SUFFIX() . '.js', __FILE__ ),
					array( 'jquery', 'ace-editor-js', 'redux-js' ),
					WPGlobus::SCRIPT_VER(),
					true
				);
			}
		}
	}
}
