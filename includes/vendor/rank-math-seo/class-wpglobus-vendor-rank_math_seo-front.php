<?php
/**
 * File: class-wpglobus-vendor-rank_math_seo-front.php
 *
 * @since 2.4.3
 * @since 2.4.4 Add filters for title, description.
 *
 * @package WPGlobus\Vendors\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Vendor_RankMathSEO_Front.
 */
if ( ! class_exists( 'WPGlobus_Vendor_RankMathSEO_Front' ) ) :

	class WPGlobus_Vendor_RankMathSEO_Front {

		public static function controller() {
			add_filter( 'wpglobus_multilingual_meta_keys', array( __CLASS__, 'filter__multilingual_meta_keys' ), 5 );
			
			/**
			 * We need this filter when title is empty and use macro.
			 *
			 * @since 2.4.4
			 * @see seo-by-rank-math\includes\frontend\paper\class-paper.php
			 * @see seo-by-rank-math\includes\traits\class-hooker.php
			 */
			add_filter( 'rank_math/frontend/title', array( __CLASS__, 'filter__frontend_title' ), 5 );
			
			/**
			 * @since 2.4.4
			 * @see seo-by-rank-math\includes\frontend\paper\class-paper.php
			 * @see seo-by-rank-math\includes\traits\class-hooker.php
			 */			
			add_filter( 'rank_math/frontend/description', array( __CLASS__, 'filter__frontend_description' ), 5 );
		}

		/**
		 * Filter the title.
		 * @since 2.4.4
		 *
		 * @param string $title The title being put out.
		 */		
		public static function filter__frontend_title( $title ) {
			
			if ( WPGlobus_Core::has_translations($title) ) {
				return WPGlobus_Core::extract_text($title);	
			}

			return $title;
		}
		
		/**
		 * Filter the description.
		 * @since 2.4.4
		 *
		 * @param string $description The description sentence.
		 */		
		public static function filter__frontend_description( $description ) {
			
			if ( WPGlobus_Core::has_translations($description) ) {
				return WPGlobus_Core::extract_text($description);	
			}

			return $description;
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