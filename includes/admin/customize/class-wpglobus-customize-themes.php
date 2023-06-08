<?php
/**
 * File: class-wpglobus-customize-themes.php
 *
 * WPGlobus Customize Themes.
 *
 * @since   1.9.12
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Customize_Themes.
 */
if ( ! class_exists( 'WPGlobus_Customize_Themes' ) ) :

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
			'experon',
			'gwangi', // @since 2.3.12
			'newyork city', // @since 2.5.21
			'mesmerize', // @since 2.8.11
			'highlight', // @since 2.8.11
			'enfold', // @since 2.8.11
		);

		/**
		 * Get disabled themes.
		 *
		 * @return string[]
		 */
		public static function disabled_themes() {
			return self::$disabled_themes;
		}

		/**
		 * Get current theme name.
		 */
		public static function current_theme() {
			if ( is_null( self::$current_theme ) ) {
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
			if ( is_null( self::$current_theme ) ) {
				self::$current_theme = wp_get_theme();
			}
			if ( 'name' === $param ) {
				return self::$current_theme->get( 'Name' );
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
