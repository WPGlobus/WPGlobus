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
		protected function __construct() {
			
			if ( is_admin() ) {
				
				/**
				 * @since 2.5.17
				 */
				add_action( 'admin_print_scripts', array(
					__CLASS__,
					'on__admin_print_scripts'
				) );
			}
		}

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
		 * Enqueue JS for Pods support.
		 *
		 * @since 2.5.17
		 */
		public static function on__admin_print_scripts() {

			if ( WPGlobus_WP::is_pagenow( array( 'post.php' ) ) ) :
			
				wp_register_script(
					'wpglobus-vendor-pods',
					WPGlobus::$PLUGIN_DIR_URL . "includes/js/wpglobus-vendor-pods" . WPGlobus::SCRIPT_SUFFIX() . ".js",
					array( 'jquery' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-vendor-pods' );
				wp_localize_script(
					'wpglobus-vendor-pods',
					'WPGlobusVendorPods',
					array(
						'version' => WPGLOBUS_VERSION,
						'vendor_version' => PODS_VERSION,
					)
				);
				
			endif;
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
				'phone',
			);

			if ( $post_id > 0 ) {
				
				$fields = self::get_fields( $post_id, $post_type );

				if ( $fields ) {
					
					self::$post_meta_fields 		= array();
					self::$post_multilingual_fields = array();

					foreach ( $fields as $key => $field ) :

						if ( 'wysiwyg' == $field->type ) {
							// wysiwyg field is enabled by default.
							if ( true ) {
								self::$post_meta_fields[$key] = $key;
								/**
								 * @since 2.5.16 @W.I.P
								 */
								self::$post_multilingual_fields[$key] = array( 'id'=>self::$meta_field_name_prefix . $key, 'type'=>$field->type );
							}			
						} else if ( '____some_value____' == $field->type ) {	
							// @todo W.I.P.
						} else if ( in_array( $field->type, $default_fields ) ) {	
							self::$post_meta_fields[$key] = $key;
							self::$post_multilingual_fields[$key] = self::$meta_field_name_prefix . $key;
						} else if ( 'file' == $field->type ) {
							// @todo need more investigation.
							// Pods creates 2 meta: 
							// _pods_person_photo = `{:en}a:1:{i:0;i:19;}{:}{:ru}a:1:{i:0;i:21;}{:}`
							// person_photo = imgID (latest value for any language)
							// self::$post_meta_fields['_pods_' . $key] = '_pods_' . $key;
							// self::$post_meta_fields[$key] =  $key;
							// self::$post_multilingual_fields[$key] = array( 'id'=>self::$meta_field_name_prefix . $key, 'type'=>$field->type );
						} else {
							// 
						}
						
					endforeach;
				}
			}

			return self::$post_meta_fields;
		}
		
		/**
		 * Get fields.
		 *
		 * @since 2.5.17
		 *
		 * @return array || boolean
		 */
		protected static function get_fields( $post_id, $post_type ) {
							 
				/**
				 * @see pods\includes\classes.php
				 */
				$pods = pods($post_type, $post_id); 
				
				/**
				 * Return if Pods is false.
				 * @since 2.5.18
				 */
				if ( ! $pods ) {
					return false;
				}
				
				$_fields = $pods->fields();
				
				if ( empty( $_fields ) ) {
					return false;
				}
				
				$fields = array();
				
				foreach ( $_fields as $_key=>$_field ) :

					$fields[$_key] = new stdClass;
					
					$fields[$_key]->name 	= $_key;
					$fields[$_key]->ID 		= $_field['id'];
					$fields[$_key]->title 	= $_field['label'];
					$fields[$_key]->parent 	= $_field['pod_id'];
					$fields[$_key]->type 	= $_field['type'];
				endforeach;	
		
				/**
				 * If we need full list of pods fields, then here is SELECT.
				 */
				/* 
				$fields = $wpdb->get_results( $wpdb->prepare(
					"SELECT p.post_name AS name, p.ID, p.post_title AS title, p.post_parent AS parent, pm.meta_value AS type FROM $wpdb->posts as p 
						LEFT JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID WHERE p.post_type = %s AND p.post_status = 'publish' AND pm.meta_key = 'type'",
					self::$field_type
				), OBJECT_K );
				// */
				
				return $fields;
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