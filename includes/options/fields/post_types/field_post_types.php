<?php

if ( ! class_exists( 'ReduxFramework_post_types' ) ) {

	/**
	 * Main ReduxFramework_table class
	 */
	class ReduxFramework_post_types {

		/**
		 * Field Constructor.
		 * Required - must call the parent constructor, then assign field and value to vars, and obviously call the render field function
		 *
		 * @param array  $field
		 * @param string $value
		 * @param        $parent
		 */
		public function __construct( $field = array(), $value = '', $parent ) {

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
		}

		/**
		 * Enqueue Function.
		 * If this field requires any scripts, or css define this function and register/enqueue the scripts/css
		 * @return void
		 */
		public function enqueue() {

			$post_types = get_post_types( array('_builtin'=>false) );

			/** @var array $options */
			$options = get_option( 'wpglobus_option' );
			$options_post_types = empty( $options['post_type'] ) ? array() : $options['post_type'];
			
			$disabled_post_types = array();
			/**
			 * Add CPT from woocommerce
			 */
			$disabled_post_types[] = 'product';
			$disabled_post_types[] = 'product_variation';
			$disabled_post_types[] = 'shop_order';
			$disabled_post_types[] = 'shop_order_refund';
			$disabled_post_types[] = 'shop_coupon';
			$disabled_post_types[] = 'shop_webhook';
			
			$enabled_post_types = array();
			foreach( $post_types as $post_type ) {
				if ( ! in_array( $post_type, $disabled_post_types,true ) ) {
					$enabled_post_types[] = $post_type;
				}	
			}	
			
			wp_enqueue_script(
				'wpglobus-redux-field-post_types',
				plugins_url( '/field_post_types' . WPGlobus::SCRIPT_SUFFIX() . '.js', __FILE__ ),
				array( 'jquery' ),
				WPGlobus::SCRIPT_VER(),
				true
			);
			wp_localize_script(
				'wpglobus-redux-field-post_types',
				'wpglobus_post_types',
				array(
					'post_type' => $enabled_post_types,
					'options' 	=> $options_post_types
				)
			);			

		}

	} // class
}

# --- EOF
