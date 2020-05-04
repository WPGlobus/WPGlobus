<?php
/**
 * File: class-wpglobus-vendor-rank_math_seo-front.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Vendor_RankMathSEO_Front.
 */
if ( ! class_exists( 'WPGlobus_Vendor_RankMathSEO_Front' ) ) :

	class WPGlobus_Vendor_RankMathSEO_Front {

		public static function controller() {
			add_filter( 'wpglobus_multilingual_meta_keys', array( __CLASS__, 'filter__multilingual_meta_keys' ), 5 );
		}
		
		/**
		 * Specify meta keys where the meta data can be multilingual.
		 */
		public static function filter__multilingual_meta_keys( $multilingual_meta_keys ) {

			$multilingual_meta_keys['rank_math_title'] = true;
			$multilingual_meta_keys['rank_math_description'] = true;
			$multilingual_meta_keys['rank_math_focus_keyword'] = true;

			return $multilingual_meta_keys;
		}	
		
	}
	
endif;

# --- EOF