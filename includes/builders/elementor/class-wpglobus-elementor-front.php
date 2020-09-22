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
/**
 * @since 2.5.5
 * @see elementor\core\files\css\post-preview.php
 */
use Elementor\Core\Files\CSS\Post_Preview as Post_Preview;
/**
 * @since 2.5.5
 * @see elementor\core\kits\manager.php
 */
use Elementor\Core\Kits\Manager as Manager;

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
		 * @since 2.4.12
	     */			
		protected static $elementor_edit_mode_meta_key = null;	

		/**
		 * @since 2.4.12
	     */			
		protected static $post_elementor_support_meta_key = null;
		
		/**
		 * @since 2.4.12
	     */	
		protected static $post_elementor_support = null;	
		
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

			/**
			 * @since 2.4.12
			 */	
			if ( ! empty($attrs['elementor_edit_mode_meta_key']) ) {
				self::$elementor_edit_mode_meta_key = $attrs['elementor_edit_mode_meta_key'];
			}
			
			/**
			 * @since 2.4.12
			 */	
			if ( ! empty($attrs['post_support_meta_key']) ) {
				self::$post_elementor_support_meta_key = $attrs['post_support_meta_key'];
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
			 * @since 2.5.5
			 * @see `do_action` in elementor\includes\frontend.php
			 * @see callback function name and using it in elementor\core\kits\manager.php
			 */
			add_action( 'elementor/frontend/after_enqueue_styles', array( __CLASS__, 'frontend_before_enqueue_styles' ), 0 );		
			
			/**
			 * @since 2.1.13
			 * @see elementor\core\files\base.php
			 */
			add_action( 'elementor/files/file_name', array( __CLASS__, 'filter__elementor_files_file_name' ), 5, 4 );
			
			/**
			 * @since 2.2.11
			 * @W.I.P 
			 * @todo maybe we need to use these filters.
			 */
			//add_filter( 'elementor/editor/localize_settings', array( __CLASS__, 'filter__localize_settings' ), 10, 2 );	
			//add_action( 'elementor/editor/after_enqueue_scripts', array( __CLASS__, 'filter__localize_settings' ) );			
			
			/**
			 * @since 2.3.6
			 */
			add_filter( 'wpglobus_plus_publish_template_include_handler', array( __CLASS__, 'filter__template_include_handler' ) );

			/**
			 * @since 2.4.12
			 */				
			add_action( 'template_include', array( __CLASS__, 'on__template_include' ) );
		}

		/**
		 * @since 2.4.12
		 */		
		public static function is_builder_support() {
			if ( is_null( self::$post_elementor_support ) || self::$post_elementor_support ) {
				return true;
			}
			return false;
		}

		/**
		 * @since 2.4.12
		 */			
		public static function on__template_include( $template ) {
			
			if ( ! is_singular() ) {
				return $template;
			}
			
			if ( is_null( self::$post_elementor_support ) ) { 

				/** @global WP_Post $post */
				global $post;
													
				$_support = get_post_meta( $post->ID, self::$post_elementor_support_meta_key, true );
				
				if ( 'off' === $_support ) {
					self::$post_elementor_support = false;
				} else {
					self::$post_elementor_support = true;
				}
				
				if ( self::$post_elementor_support ) {
					
					$_support = get_post_meta( $post->ID, self::$elementor_edit_mode_meta_key, true );
					
					if ( 'builder' !== $_support ) {
						/**
						 * Disable elementor support for post, that doesn't use elementor builder.
						 */
						self::$post_elementor_support = false;
					}		
				}
			}	
			
			return $template;
		}
			
		/**
		 * @since 2.5.5
		 */	
		public static function frontend_before_enqueue_styles() {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return;
			}
	
			if ( ! self::is_builder_support() ) {
				return;
			}
			
			$manager = new Manager();
			
			$kit = $manager->get_kit_for_frontend();

			if ( $kit ) {
				if ( $kit->is_autosave() ) {
					$css_file = Post_Preview::create( $kit->get_id() );
				} else {
					$css_file = Post_CSS::create( $kit->get_id() );
				}

				$handle = 'elementor-post-' . $css_file->get_post_id() .'-' . WPGlobus::Config()->language;
				
				$url = $css_file->get_url();
				
				/**
				 * @since 2.5.5 @W.I.P `enqueue` doesn't work here.
				 * @see `frontend_before_enqueue_styles` in elementor\core\kits\manager.php.
				 */
				// $css_file->enqueue();
				
				wp_register_style(
					$handle,
					$url,
					array(),
					'wpglobus-' . WPGLOBUS_VERSION
					
				);
				wp_enqueue_style($handle);	
			}		
		}
		
		/**
		 * @since 2.1.15
		 */
		public static function on__enqueue_styles() {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return;
			}

			/**
			 * @since 2.4.12
			 */			
			if ( ! self::is_builder_support() ) {
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
		 * Filters the file name.
		 *
		 * @since 2.1.13
		 * @since 2.3.3   Handle multiple file names.
		 *
		 * @param string $file_name CSS file name.
		 * @param object $instance  The file instance, which inherits Elementor\Core\Files
		 */
		public static function filter__elementor_files_file_name( $file_name, $instance ) {

			if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
				return $file_name;
			}
			
			if ( false === strpos( $file_name, self::$file_prefix ) ) {
				return $file_name;
			}

			/**
			 * @since 2.4.12
			 */				
			if ( ! self::is_builder_support() ) {
				return $file_name;
			}
			
			/**
			 * @since 2.3.3 @W.I.P May be check out for global post ID.
			 */
			//global $post;
			//if ( ! $post instanceof WP_Post ) {
				//return $file_name;
			//}
			//$file_unique_part = str_replace( self::$file_prefix, '', $file_name );
			//if ( false === strpos( $file_unique_part, $post->ID . '.' ) ) {
				//return $file_name;
			//}

			static $_file_names = null;
			
			if ( ! is_null( $_file_names ) && ! empty( $_file_names[$file_name] ) ) {
				return $_file_names[$file_name];
			}

			$_file_name = $file_name;
			if ( false !== strpos( $file_name, '.css' ) ) {
				$_file_name = str_replace( '.css', '-' . WPGlobus::Config()->language . '.css', $file_name );
			}
				
			$_file_names[$file_name] = $_file_name;
				
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
	
		/**
		 * @since 2.3.6
		 */	
		public static function filter__template_include_handler($handler) {
			
			/**
			 * @W.I.P @since 2.4.12 Do we need to check is_builder_support?
			 */			
			//if ( ! self::is_builder_support() ) {
				//return $handler;
			//}
			
			/**
			 * Don't fire `template_include` filter with active Elementor
			 * to prevent `The preview could not be loaded` for language in draft status.
			 */
			if ( empty( $_GET['elementor-preview'] ) ) {
				return $handler;
			}
			return false;
		}
	} // end class WPGlobus_Elementor_Front.

endif;

# --- EOF