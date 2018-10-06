<?php
/**
 * File: class-wpglobus-yoast_seo.php
 *
 * @package WPGlobus\Builders\Yoast_SEO
 * @author  Alex Gor(alexgff)
 */


if ( ! class_exists( 'WPGlobus_Yoast_SEO' ) ) :

	/**
	 * Class WPGlobus_Yoast_SEO.
	 */
	class WPGlobus_Yoast_SEO extends WPGlobus_Builder {

		/**
		 * Constructor.
		 */
		public function __construct() {
			parent::__construct( 'yoast_seo' );
		}

	}

endif;
