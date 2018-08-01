<?php
/**
 * Class WPGlobus_Config_Vendor
 *
 * @package WPGlobus\Config
 * @author Alex Gor(alexgff)
 */
 
if ( ! class_exists('WPGlobus_Config_Vendor') ) :

	class WPGlobus_Config_Vendor {
		
		const PLUGIN_CONFIG_FILES = 'configs/*.json';
		
		const PLUGIN_CONFIG_DIR = 'configs/';
		
		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		protected static $config = array();
		
		protected static $post_meta_fields = null;
		
		protected static $wp_options = null;
		
		/**
		 * Array of registered vendors.
		 *
		 * @var string[]
		 */
		protected static $vendors = array();
	
		/**
		 * Constructor.
		 */		
		protected function __construct() {
			
			self::get_config_files();
			self::parse_config();
			
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
		 * Get meta fields.
		 */		
		public static function get_meta_fields() {
			if ( is_null(self::$post_meta_fields) ) {
				return false;
			}
			return self::$post_meta_fields;			
		}

		/**
		 * Get wp_options.
		 */			
		public static function get_wp_options() {
			if ( is_null(self::$wp_options) ) {
				return false;
			}
			return self::$wp_options;
		}
		
		/**
		 * Get config files.
		 */
		public static function get_config_files() {
			
			//$dir = WPGlobus::$PLUGIN_DIR_PATH . self::PLUGIN_CONFIG_FILES;
			
			$config_plugin_dir = WPGlobus::$PLUGIN_DIR_PATH . self::PLUGIN_CONFIG_DIR;

			/**
			 * WPGlobus SEO.
			 */
			if ( function_exists('wpglobus_seo__init') ) {
				self::$vendors[] = 'wpglobus-seo.json';
			}
			
			/**
			 * Yoast SEO.
			 */
			if ( defined('WPSEO_VERSION') )  {
				/**
				 * check 'WPSEO_PREMIUM_PLUGIN_FILE' for premium add-on.
				 */
				self::$vendors[] = 'yoast-seo.json';
			}
			
			foreach( self::$vendors as $file ) {
				
				if ( is_readable( $config_plugin_dir . $file ) ) {
					$file_name = pathinfo( $file, PATHINFO_FILENAME );
					self::$config[$file_name] = json_decode(file_get_contents($config_plugin_dir . $file), true);
				}
				
			}
			
		}

		/**
		 * Parse config files.
		 */
		public static function parse_config() {	

			/**
			 * Parse post meta fields.
			 */
			if ( is_null( self::$post_meta_fields ) ) {
				
				foreach( self::$config as $vendor=>$data ) {
					
					if ( isset( $data['post_meta_fields'] ) && is_array( $data['post_meta_fields'] ) ) :
						foreach( $data['post_meta_fields'] as $_meta=>$_init ) {
							if ( isset( $data['post_meta_fields'][$_meta] ) ) {
								self::$post_meta_fields[] = $_meta;
							}
						}
					endif;
				
				}
				
			}
				
			/**
			 * Parse WP options.
			 */
			if ( is_null( self::$wp_options ) ) {
				
				foreach( self::$config as $vendor=>$data ) {	

					if ( isset( $data['wp_options'] ) && is_array( $data['wp_options'] ) ) :
						foreach( $data['wp_options'] as $_option=>$_init ) {
							if ( isset( $data['wp_options'][$_option] ) ) {
								self::$wp_options[] = $_option;
							}
						}
					endif;
					
				}
				
				if ( ! is_null( self::$wp_options ) ) {
					self::$wp_options = array_unique( self::$wp_options );
				}
				
			}				
			
			
			
		}		
		
	
	}
	
endif;

# --- EOF