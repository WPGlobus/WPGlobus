<?php
/**
 * @package WPGlobus
 * @since 1.8
 */

/**
 * Class WPGlobus_Redirect
 */
class WPGlobus_Redirect {
	
		/**
		 * Constructor.
		 */
		public static function construct() {
			add_action( 'init', array( __CLASS__, 'on__init' ), 1 );
		}
		
		/**
		 * Init action.
		 */
		public static function on__init() {
			
			if ( empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) {
				return;
			}
		
			$cookiename = WPGLOBUS::_COOKIE;
		
			if( ! isset( $_COOKIE[$cookiename] ) ) {
				/**
				 * First visit.
				 */
				$browser_language = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
				setcookie( $cookiename,  $browser_language, time()+3600*24*365, '/' );
				self::redirect( $browser_language );
				return;
			}
			
		}		
		
		/**
		 * Redirect to specified language.
		 */
		public static function redirect( $language ) {

			if ( $language == WPGlobus::Config()->default_language ) {
				return;
			}
			
			if ( ! in_array( $language, WPGlobus::Config()->enabled_languages ) ) {
				return;
			}
			
			$url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			$url = WPGlobus_Utils::localize_url( $url, $language );			
			wp_redirect( $url ); 
			exit;			
			
		}
		
}
# --- EOF