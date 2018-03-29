<?php
/**
 * WPGlobus Customize Themes.
 *
 * @package WPGlobus
 * @since   1.9.12
 */

/**
 * Class WPGlobus_Customize_Themes.
 */
if ( ! class_exists('WPGlobus_Customize_Themes') ) :
 
	class WPGlobus_Customize_Themes {
		
		/**
		 * Current theme.
		 */
		protected static $current_theme = null;
		
		/**
		 * Names of disabled themes in lowercase format.
		 *
		 * @var string[]
		 */
		protected static $disabled_themes = array( 
			'customizr',
			'customizr pro',
			'experon'	
		);
		
		/**
		 * Get disabled themes.
		 *
		 * @return string[]
		 */
		public static function disabled_themes() {
			$disabled_themes = self::$disabled_themes;
			return $disabled_themes;
		}
		
		/**
		 * Get current theme name.
		 */
		public static function current_theme() {
			if ( is_null(self::$current_theme) ) {
				self::$current_theme = wp_get_theme();
			}
			return self::get_theme( 'name' );
		}
		
		/**
		 * Get current theme or its property.
		 *
		 * @param string $param
		 *
		 * @return string|WP_Theme
		 */
		public static function get_theme( $param = '' ) {
			if ( is_null(self::$current_theme) ) {
				self::$current_theme = wp_get_theme();
			}
			if ( 'name' === $param ) {
				return self::$current_theme->name;
			}

			return self::$current_theme;

		}
		
		/**
		 * Get current theme in lowercase.
		 *
		 * @return string
		 */		
		public static function get_theme_name_lc() {
			return strtolower( self::get_theme( 'name' ) );
		}
		
	}

endif;
