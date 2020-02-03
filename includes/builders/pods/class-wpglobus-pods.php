<?php
/**
 * File: class-wpglobus-pods.php
 *
 * @since 2.3.0
 *
 * @package WPGlobus\Builders\Pods.
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Pods' ) ) :

	/**
	 * Class WPGlobus_Pods.
	 */
	class WPGlobus_Pods extends WPGlobus_Builder {
	
		/**
		 * Constructor.
		 */
		public function __construct() {
			 
			parent::__construct( 'pods' );

		}

	}

endif;

# --- EOF