<?php
/**
 * File: class-wpglobus-vendor-rank_math_seo.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Vendors\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */
/**
 * Class WPGlobus_Vendor_RankMathSEO.
 */
if ( ! class_exists( 'WPGlobus_Vendor_RankMathSEO' ) ) : 
 
	class WPGlobus_Vendor_RankMathSEO {

		protected static $post_meta_fields = null;

		/**
		 * List of fields to add `wpglobus-translatable` class.
		 */
		protected static $post_multilingual_fields = null;

		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;

		/**
		 * Constructor.
		 */
		protected function __construct(){}

		/**
		 * Get instance of this class.
		 *
		 * @return WPGlobus_Vendor_RankMathSEO
		 */
		public static function get_instance() {
			if ( ! ( self::$instance instanceof WPGlobus_Vendor_RankMathSEO ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Get multilingual fields.
		 */
		public static function get_post_multilingual_fields() {
			if ( is_null( self::$post_multilingual_fields ) ) {
				return false;
			}

			return self::$post_multilingual_fields;
		}

		/**
		 * Get post meta.
		 *
		 * Don't call WPGlobus::Config() inside function to prevent the resetting of `meta` property.
		 *
		 * @param        $post_id
		 * @param string $post_type
		 *
		 * @return array
		 */
		public static function get_post_meta_fields( $post_id, $post_type = 'post' ) {

			if ( ! is_null( self::$post_meta_fields ) ) {
				return self::$post_meta_fields;
			}

			$post_id = (int) $post_id;

			if ( $post_id > 0 ) {
				
				self::$post_meta_fields = array(
					'rank_math_title',
					'rank_math_description',
					'rank_math_focus_keyword',
				);
			}

			return self::$post_meta_fields;
		}
		
		/**
		 * Check 3rd party plugin to enable/disable field.
		 *
		 * @param array $field
		 *
		 * @return bool
		 */
		protected static function get_3rd_party_field_status( $field ) {
			// @see wpglobus\includes\vendor\acf\class-wpglobus-acf.php for example.
			return true;
		}
		
	}

endif;

# --- EOF