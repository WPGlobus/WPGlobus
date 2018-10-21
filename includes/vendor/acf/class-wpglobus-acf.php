<?php
/**
 * @package WPGlobus\Vendor\ACF.
 *
 * @since 1.9.17
 */

/**
 * Class WPGlobus_Acf_2.
 */
class WPGlobus_Acf_2 {
	
		protected static $post_multilingual_fields = null;
		
		protected static $post_acf_field_prefix = 'acf-';
		
		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		/**
		 * Constructor.
		 */		
		protected function __construct() {}

		/**
		 * Get instance of this class.
		 *
		 * @return object
		 */
		public static function get_instance() {
			if ( ! ( self::$instance instanceof self ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Get multilingual fields.
		 */		
		public static function get_post_multilingual_fields() {
			if ( is_null(self::$post_multilingual_fields) ) {
				return false;
			}
			return self::$post_multilingual_fields;
		}
		
		/**
		 * Get post meta.
		 */
		public static function get_post_meta_fields( $post_id ) {
			
			global $wpdb;
			
			$_post_meta_fields = array();
			
			$post_id = (int) $post_id;
			
			if ( $post_id > 0 ) {
				
				$rows = $wpdb->get_results($wpdb->prepare(
					"SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = %d AND meta_value LIKE 'field_%'",
					$post_id
				), ARRAY_A);
				
				if ( ! empty($rows) ) {
					self::$post_multilingual_fields = array();
					foreach( $rows as $key=>$field ) {
						if ( '_' == $field['meta_key'][0] ) {
							$_post_meta_fields[] = substr_replace( $field['meta_key'], '', 0, 1 );
							self::$post_multilingual_fields[] = self::$post_acf_field_prefix . $field['meta_value'];
						}
					}
				}
			}
			
			return $_post_meta_fields;
		
		}
	
}
# --- EOF