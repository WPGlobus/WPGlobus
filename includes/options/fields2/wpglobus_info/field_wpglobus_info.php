<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Don't duplicate me!
if ( ! class_exists( 'WPGlobusOptions_wpglobus_info' ) ) {

	/**
	 * Main WPGlobusOptions_wpglobus_info class.
	 */
	class WPGlobusOptions_wpglobus_info {

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @since       1.0.0
		 * @access      public
		 * @param array  $field
		 * @param string $value
		 * @param        $parent
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
		 * Takes the vars and outputs the HTML for the field in the settings.
		 *
		 * @access      public
		 * @return      void
		 */
		public function render() {

			ob_start();
			$class = empty($this->field['class']) ? '' : ' '.$this->field['class']; ?>
			<div 
				id="wpglobus-options-<?php echo $this->field['id']; ?>" 
				class="wpglobus-options-field wpglobus-options-field-<?php echo $this->field['id']; ?> wpglobus-options-field-<?php echo $this->field['type']; ?><?php echo $class; ?>" 
				data-id="<?php echo $this->field['id']; ?>" 
				data-type="<?php echo $this->field['type']; ?>">
					<?php if ( ! empty( $this->field['title'] ) ) {	?>
						<p class="title"><?php echo $this->field['title']; ?></p>
					<?php }	?>		
					<?php if ( ! empty( $this->field['subtitle'] ) ) {	?>
						<p class="subtitle"><?php echo $this->field['subtitle']; ?></p>
					<?php }	?>
					<?php if ( ! empty( $this->field['html'] ) ) {	?>
						<?php echo $this->field['html']; ?>
					<?php }	?>	
					<?php if ( ! empty( $this->field['desc'] ) ) {	?>
						<p class="description"><?php echo $this->field['desc']; ?></p>
					<?php }	?>	
			</div>	
			<div style="clear:both;"></div>
			<?php
			echo ob_get_clean();

		}
	}
}

new WPGlobusOptions_wpglobus_info($field);
