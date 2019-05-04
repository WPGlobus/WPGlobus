<?php
/**
 * File: class-wpglobus-elementor-front.php
 *
 * @package WPGlobus\Builders\Elementor
 * @author  Alex Gor(alexgff)
 */

/**
 * @see elementor\core\files\css\post.php
 */
use Elementor\Core\Files\CSS\Post as Post_CSS;

if ( ! class_exists( 'WPGlobus_Elementor_Front' ) ) :

	/**
	 * Class WPGlobus_Elementor_Front.
	 */
	class WPGlobus_Elementor_Front {

		public static $file_prefix = 'post-';
		
		/**
		 * @since 2.1.15
		 */
		protected static $post_css_meta_key = null;

		/**
		 * @since 2.1.15
		 */
		protected static $elementor_data_meta_key = null;	
		
		/**
		 * @since 2.1.15
		 */
		protected static $elementor_css_meta_key = null;	
		
		/**
		 * Init.
		 */
		public static function init($attrs) {
			
			if ( ! empty($attrs['post_css_meta_key']) ) {
				self::$post_css_meta_key = $attrs['post_css_meta_key'];
			}

			if ( ! empty($attrs['elementor_data_meta_key']) ) {
				self::$elementor_data_meta_key = $attrs['elementor_data_meta_key'];
			}

			if ( ! empty($attrs['elementor_css_meta_key']) ) {
				self::$elementor_css_meta_key = $attrs['elementor_css_meta_key'];
			}
			
			add_filter( 'get_post_metadata', array( __CLASS__, 'filter__get_metadata' ), 5, 4 );

			/**
			 * @since 2.1.15
			 */
			add_filter( 'update_post_metadata', array( __CLASS__, 'filter__update_metadata' ), 5, 5 );		

			/**
			 * @since 2.1.15
			 */			
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'on__enqueue_styles' ), 20 );
			
			/**
			 * @todo may be use elementor action instead of `wp_enqueue_scripts`.
			 */
			//add_action( 'elementor/frontend/after_enqueue_styles', array( __CLASS__, 'on__enqueue_styles' ) );		
			
			/**
			 * @since 2.1.13
			 * @see elementor\core\files\base.php
			 */
			add_action( 'elementor/files/file_name', array( __CLASS__, 'filter__elementor_files_file_name' ), 5, 4 );
		}

		/**
		 * @since 2.1.15
		 */
		public static function on__enqueue_styles() {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return;
			}
			
			$css_file = new Post_CSS( get_the_ID() );
			
			/**
			 * @see elementor\core\files\css\post.php
			 */
			$handle = 'elementor-post-' . $css_file->get_post_id() .'-' . WPGlobus::Config()->language;
			
			/**
			 * @see elementor\core\files\base.php
			 */
			$url = $css_file->get_url();
			
			wp_register_style(
				$handle,
				$url,
				array(),
				'wpglobus-' . WPGLOBUS_VERSION
				
			);
			wp_enqueue_style($handle);	
		}
		
		/**
		 * Filters the file name
		 *
		 * @since 2.1.13
		 *
		 * @param string $file_name
		 * @param object $instance  The file instance, which inherits Elementor\Core\Files
		 */
		public static function filter__elementor_files_file_name( $file_name, $instance ) {
			
			static $_file_name = null;
			
			if ( ! is_null( $_file_name ) ) {
				return $_file_name;
			}

			if ( false === strpos( $file_name, self::$file_prefix ) ) {
				return $file_name;
			} else {
				// case when $file_name == 'post-ID.css'
				if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
					$_file_name = $file_name;
				} else {
					if ( false !== strpos( $file_name, '.css' ) ) {
						$_file_name = str_replace( '.css', '-' . WPGlobus::Config()->language . '.css', $file_name );
					}
				}
			}

			return $_file_name;
		}
		
		/**
		 * Get meta callback.
		 *
		 * @scope front.
		 * @param $check
		 * @param $object_id
		 * @param $meta_key
		 * @param $single
		 *
		 * @return string
		 */
		public static function filter__get_metadata(
			$check, $object_id, $meta_key, /** @noinspection PhpUnusedParameterInspection */
			$single
		) {

			if ( self::$elementor_data_meta_key === $meta_key ) {

				$meta_cache = wp_cache_get( $object_id, 'post_meta' );

				if ( isset( $meta_cache[ $meta_key ] ) && isset( $meta_cache[ $meta_key ][0] ) ) {

					/** @noinspection PhpUnusedLocalVariableInspection */
					$_value = '';

					if ( WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {
						$_value = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );
					} else {
						$_value = $meta_cache[ $meta_key ][0];
					}

					return $_value;

				}
				
			} elseif ( self::$elementor_css_meta_key === $meta_key ) {
				
				if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
					return $check;
				}
				
				$meta_cache = wp_cache_get( $object_id, 'post_meta' );
				
				if ( isset( $meta_cache[ self::$post_css_meta_key ] ) && isset( $meta_cache[ self::$post_css_meta_key ][0] ) ) {

					$_value = '';
					
					$has_translations = WPGlobus_Core::has_translations( $meta_cache[ self::$post_css_meta_key ][0] );

					if ( $has_translations ) {
						$_value = WPGlobus_Core::text_filter( $meta_cache[ self::$post_css_meta_key ][0], WPGlobus::Config()->language, WPGlobus::RETURN_EMPTY );
					}

					if ( ! empty($_value) ) {
						$_value = maybe_unserialize( $_value );
						
						/**
						 * @todo may be better return value for default language.
						 */
						//if ( isset( $meta_cache[ self::$elementor_css_meta_key ] ) && isset( $meta_cache[ self::$elementor_css_meta_key ][0] ) ) {
							//$_value = maybe_unserialize( $meta_cache[ self::$elementor_css_meta_key ] );
						//}
					}

					return $_value;
				}

			}

			return $check;
		}
		
		/**
		 * @since 2.1.15
		 */
		public static function filter__update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {

			if ( self::$elementor_css_meta_key != $meta_key ) {
				return $check;
			}

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return $check;
			}
			
			$meta_cache = wp_cache_get( $object_id, 'post_meta' );
			
			if ( isset( $meta_cache[ self::$post_css_meta_key ] ) && isset( $meta_cache[ self::$post_css_meta_key ][0] ) ) {
				
				$new_value  = $check;
				$_tr 		= array();

				foreach( WPGlobus::Config()->enabled_languages as $language ) {
					$_s = WPGlobus_Core::text_filter( $meta_cache[ self::$post_css_meta_key ][0], $language, WPGlobus::RETURN_EMPTY );
					if ( ! empty( $_s ) ) {
						$_tr[$language] = $_s;
					}
				}
				
				/**
				 * Fix for $meta_value[0].
				 */
				$meta_value[0] = '';
					
				$_tr[WPGlobus::Config()->language] = maybe_serialize($meta_value);
				$new_value = WPGlobus_Utils::build_multilingual_string($_tr);
				
				if ( $new_value != $check ) {
					
					global $wpdb;
					
					$meta_value = $new_value;
					$table = _get_meta_table('post');
					$data  = compact( 'meta_value' );
					$where = array(
						'post_id'  => $object_id,
						'meta_key' => self::$post_css_meta_key
					);

					$result = $wpdb->update( $table, $data, $where );
					
					if ( ! $result ) {
						return false;
					}

				}
				
				return $new_value;
				
			} else {
				
				/**
				 * $meta_cache[ $meta_key ] is undefined.
				 */ 
				
				global $wpdb;
				
				$_tr = array();
				$_tr[WPGlobus::Config()->language] = maybe_serialize($meta_value);
				$new_value = WPGlobus_Utils::build_multilingual_string($_tr);

				$table = _get_meta_table('post');

				$result = $wpdb->insert(
					$table, 
					array( 
						'post_id'  	 => $object_id, 
						'meta_key' 	 => self::$post_css_meta_key,
						'meta_value' => $new_value
					), 
					array( 
						'%d',
						'%s',
						'%s'
					) 
				);
				
				if ( ! $result ) {
					return false;
				}
				
				return $new_value;
			}
			
			return $check;

		}	

	} // end class WPGlobus_Elementor_Front.

endif;
