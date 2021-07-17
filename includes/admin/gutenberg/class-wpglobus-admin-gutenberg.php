<?php
/**
 * File: class-wpglobus-admin-gutenberg.php
 *
 * @since 2.7.2
 * @deprecated 2.8.0
 */

if ( ! class_exists('WPGlobus_Admin_Gutenberg') ) :

	/**
	 * Class WPGlobus_Admin_Gutenberg
	 */
	class WPGlobus_Admin_Gutenberg {

		/**
		 * DUMMY_REDIRECT_PAGE constant.
		 */
		const DUMMY_REDIRECT_PAGE = 'wpglobus-standard-widgets';

		/**
		 * REDIRECT_TO_PAGE constant.
		 */		
		const REDIRECT_TO_PAGE = 'widgets.php';

		/**
		 * Static constructor.
		 */
		public static function construct() {

			if ( ! empty( $_GET['page'] ) && self::DUMMY_REDIRECT_PAGE === $_GET['page'] && ! empty( $_GET['redirect-to'] ) ) {
				
				/**
				 * Make redirect from dummy page to http://site/wp-admin/widgets.php.
				 */
				$url  = add_query_arg( 
					array(), 
					admin_url(self::REDIRECT_TO_PAGE) 
				);
				
				self::redirect( $url );
				exit;
			}

			add_action( 'admin_menu', array( __CLASS__, 'on__add_menu' ), 10 );

		}
		
		/**
		 * Add menu item.
		 */
		public static function on__add_menu() {
			
			add_theme_page(
				esc_html__( 'Widgets with WPGlobus', 'wpglobus' ),
				esc_html__( 'Widgets with WPGlobus', 'wpglobus' ),
				'edit_theme_options',
				self::DUMMY_REDIRECT_PAGE . '&redirect-to='.self::REDIRECT_TO_PAGE,
				array( __CLASS__, 'dummy_function' )
			);
		}
		
		/**
		 * Empty callback function.
		 */
		public static function dummy_function(){}
		
		/**
		 * Redirect.
		 */
		protected static function redirect( $location, $status = 302, $x_redirect_by = 'WPGlobus' ) {
			
			global $is_IIS;
		 
			if ( ! $location ) {
				return false;
			}
		 
			if ( $status < 300 || 399 < $status ) {
				wp_die( __( 'HTTP redirect status code must be a redirection code, 3xx.' ) );
			}
		 
			if ( ! $is_IIS && 'cgi-fcgi' !== PHP_SAPI ) {
				status_header( $status ); // This causes problems on IIS and some FastCGI setups.
			}

			if ( is_string( $x_redirect_by ) ) {
				header( "X-Redirect-By: $x_redirect_by" );
			}
		 
			header( "Location: $location", true, $status );
		 
			return true;
		}		
		
	}

endif;

# --- EOF