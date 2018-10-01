<?php
/**
 * File: class-wpglobus-js_composer.php
 *
 * @package WPGlobus\Builders\WPBakeryPageBuilder
 * @author  Alex Gor(alexgff)
 */


if ( ! class_exists( 'WPGlobus_JS_Composer' ) ) :

	/**
	 * Class WPGlobus_JS_Composer.
	 */
	class WPGlobus_JS_Composer extends WPGlobus_Builder {

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct( 'js_composer' );
		}

	}

endif;
