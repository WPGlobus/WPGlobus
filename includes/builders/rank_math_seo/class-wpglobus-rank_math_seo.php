<?php
/**
 * File: class-wpglobus-rank_math_seo.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_RankMathSEO' ) ) :

	/**
	 * Class WPGlobus_RankMathSEO.
	 */
	class WPGlobus_RankMathSEO extends WPGlobus_Builder {
	
		/**
		 * Constructor.
		 */
		public function __construct() {
			 
			parent::__construct( 'rank_math_seo' );

		}
	}

endif;

# --- EOF