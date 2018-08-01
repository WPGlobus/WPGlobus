<?php
/**
 * Class WPGlobus_WP_Options
 *
 * @package WPGlobus\WP_Options
 * @author Alex Gor(alexgff)
 */
 
if ( ! class_exists('WPGlobus_WP_Options') ) :

	class WPGlobus_WP_Options {
		
		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		/**
		 * Constructor.
		 */		
		protected function __construct($wp_options = array()) {
			
			if ( empty($wp_options) || ! is_array($wp_options) ) {
				return;
			}
			
			if ( is_admin() ) {
				foreach( $wp_options as $option ) {
					add_filter( 'option_'.$option, array( __CLASS__, 'filter__translate_option' ) );
				}
			}

		}
		
		/**
		 * Get instance of this class.
		 *
		 * @return object
		 */
		public static function get_instance($wp_options) {
			if ( ! ( self::$instance instanceof self ) ) {
				self::$instance = new self($wp_options);
			}
			return self::$instance;
		}
		
		/**
		 * Callback to translate option.
		 */
		public static function filter__translate_option($option) {
			if ( ! is_string($option) ) {
				return $option;
			}
			
			if ( ! WPGlobus::Config()->builder->is_running() || empty( WPGlobus::Config()->builder->get_language() ) ) {
				return $option;
			}
			
			$option = WPGlobus_Core::text_filter( $option, WPGlobus::Config()->builder->get_language() );
			return $option;
		}
			
	}
	
endif;

# --- EOF