<?php
/**
 * @package WPGlobus\Vendor\All-in-One-SEO-Pack.
 *
 * @since 1.9.17
 */

/**
 * Class WPGlobus_All_in_One_SEO_Pack.
 * @see save_post_data() in all-in-one-seo-pack\aioseop_class.php
 */
class WPGlobus_All_in_One_SEO_Pack {
	
		protected static $meta_fields = array(
			'_aioseop_title'       => '',
			'_aioseop_description' => '',
			'_aioseop_keywords'    => '',
		);	

		protected static $meta_fields_in_processing = array();
		
		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		/**
		 * Constructor.
		 */		
		protected function __construct() {
			
			self::$meta_fields_in_processing = self::$meta_fields;

			/**
			 * @see delete_metadata() in wp-includes\meta.php
			 */			
			add_filter( 'delete_post_metadata', array( __CLASS__, 'filter__get_exist_fields' ), 10, 5 );
			
			/**
			 * @see add_metadata() in wp-includes\meta.php
			 */
			add_filter( 'add_post_metadata', array( __CLASS__, 'filter__rewrite_fields' ), 10, 4 );	
			
			/**
			 * @see 'localization' filter in all-in-one-seo-pack\aioseop_class.php.
			 */
			add_filter( 'localization', array( __CLASS__, 'filter__title' ), 0 );
		}

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
		 * All In One SEO Pack has not filter for title when generate it in get_page_snippet_info() function.
		 * @see '$title = $this->internationalize( get_post_meta( $post->ID, '_aioseop_title', true ) );' string in all-in-one-seo-pack\aioseop_class.php.
		 * Moreover, Gutenberg doesn't fire 'title_edit_pre' filter @see wpglobus\includes\builders\class-wpglobus-builder.php
		 * So, we are using the trick with 'localization' filter.
		 * @todo may be find common solution to add it in WPGlobus_Builder class.
		 */
		public static function filter__title( $text ) {

			if ( 'gutenberg' != WPGlobus::Config()->builder->get_id() ) {
				return $text;
			}
			
			global $post;

			if ( ! WPGlobus_Core::has_translations( $post->post_title ) ) {
				return $text;
			}

			$post->post_title = WPGlobus_Core::text_filter( $post->post_title, WPGlobus::Config()->builder->get_language(), WPGlobus::RETURN_EMPTY );
			
			return $text;
		}
		
		/**
		 * Get exist fields before deleting.
		 */
		public static function filter__get_exist_fields( $check, $object_id, $meta_key, $meta_value, $delete_all ) {

			if ( $delete_all ) {
				return $check;
			}

			if ( isset( self::$meta_fields[ $meta_key ] ) ) {
				
				if ( ! isset( self::$meta_fields_in_processing[ $meta_key ] ) ) {
					return false;
				}
				
				global $wpdb;

				//$value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id = %d;", $meta_key, $object_id ) );
				$value = $wpdb->get_results( $wpdb->prepare( "SELECT meta_value, meta_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id = %d;", $meta_key, $object_id ) );

				if ( $value && $value[0] ) {
					self::$meta_fields[ $meta_key ] = $value[0]->meta_value;
					self::$meta_fields_in_processing[ $meta_key ] = $value[0]->meta_id;
				}
				
			}
			
			return $check;
			
		}
	
		/**
		 * Rewrite fields.
		 */
		public static function filter__rewrite_fields( $check, $object_id, $meta_key, $meta_value ) {

			global $wpdb;
			
			if ( isset( self::$meta_fields[ $meta_key ] ) ) {
				
				if ( ! isset( self::$meta_fields_in_processing[ $meta_key ] ) ) {
					return false;
				}

				$ml_value = array();
				foreach( WPGlobus::Config()->enabled_languages as $language ) {
					$val = WPGlobus_Core::text_filter(self::$meta_fields[ $meta_key ], $language, WPGlobus::RETURN_EMPTY);
					if ( ! empty($val) ) {
						$ml_value[$language] = $val;
					}
				}

				if ( empty($meta_value) ) {
					unset( $ml_value[ WPGlobus::Config()->builder->get_language() ] );
				} else {
					$ml_value[ WPGlobus::Config()->builder->get_language() ] = $meta_value;
				}
				
				$meta_value = WPGlobus_Utils::build_multilingual_string($ml_value);
			
				$meta_value = maybe_serialize( $meta_value );
				
				if ( isset(self::$meta_fields_in_processing[ $meta_key ]) && (int) self::$meta_fields_in_processing[ $meta_key ] > 0 ) {
					
					$mid = (int) self::$meta_fields_in_processing[ $meta_key ];
					
					$result = $wpdb->update( 
						$wpdb->postmeta, 
						array(
							'meta_key'   => $meta_key,
							'meta_value' => $meta_value
						),
						array( 'meta_id' => $mid ),
						array( '%s', '%s' ),
						array( '%d' )
					);					
					
					if ( ! $result ) {
						return false;
					}
					
				} else {
					
					$result = $wpdb->insert( $wpdb->postmeta, array(
						'post_id'    => $object_id,
						'meta_key'   => $meta_key,
						'meta_value' => $meta_value,
					) );
					
					if ( ! $result ) {
						return false;
					}

					$mid = (int) $wpdb->insert_id;					
					
				}
				
				wp_cache_delete( $object_id, 'post_meta' );

				unset( self::$meta_fields_in_processing[ $meta_key ] );
				
				return $mid;
				
			}

			return $check;
			
		}	
	
}

# --- EOF