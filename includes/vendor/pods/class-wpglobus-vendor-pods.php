<?php
/**
 * File: class-wpglobus-vendor-pods.php
 *
 * @since 2.3.0
 *
 * @package WPGlobus\Vendor\Pods.
 * @author  Alex Gor(alexgff)
 */
/**
 * Class WPGlobus_Vendor_Pods.
 */
if ( ! class_exists( 'WPGlobus_Vendor_Pods' ) ) : 
 
	class WPGlobus_Vendor_Pods {

		protected static $post_meta_fields = null;

		/**
		 * List of fields to add `wpglobus-translatable` class.
		 */
		protected static $post_multilingual_fields = null;

		protected static $field_type = '_pods_field';

		protected static $meta_field_name_prefix = 'pods_meta_';

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
		 * @return WPGlobus_Vendor_Pods
		 */
		public static function get_instance() {
			if ( ! ( self::$instance instanceof WPGlobus_Vendor_Pods ) ) {
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

			global $wpdb;

			$post_id = (int) $post_id;

			$default_fields = array(
				'paragraph',
				'text',
				'website',
			);

			if ( $post_id > 0 ) {
				
				/**
				 * @todo may be use pods_api() @see includes\builders\pods\class-wpglobus-builder-pods.php
				 * @since 2.3.0
				 */
				 
				$fields = $wpdb->get_results( $wpdb->prepare(
					"SELECT p.post_name AS name, p.ID, p.post_title AS title, p.post_parent AS parent, pm.meta_value AS type FROM $wpdb->posts as p 
						LEFT JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID WHERE p.post_type = %s AND p.post_status = 'publish' AND pm.meta_key = 'type'",
					self::$field_type
				), OBJECT_K );

				if ( ! empty( $fields ) ) {
					
					self::$post_meta_fields 		= array();
					self::$post_multilingual_fields = array();

					foreach ( $fields as $key => $field ) :
					
						if ( 'wysiwyg' == $field->type ) {
							// wysiwyg field is enabled be default.
							if ( true ) {
								self::$post_meta_fields[$key] = $key;
							}			
						} else if ( '____some_value____' == $field->type ) {	
							// @todo W.I.P.
						} else if ( in_array( $field->type, $default_fields ) ) {	
							self::$post_meta_fields[$key] = $key;
							self::$post_multilingual_fields[$key] = self::$meta_field_name_prefix . $key;
						} else {
							// 
						}
						
					endforeach;
				}
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