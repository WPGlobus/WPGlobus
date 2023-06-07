<?php

/**
 * Class WPGlobus_Redirect
 *
 * @since   1.8.0
 */
class WPGlobus_Redirect {

	/**
	 * Constructor.
	 */
	public static function construct() {
		add_action( 'wp', array( __CLASS__, 'on__init' ), 1 );
	}

	/**
	 * Init action.
	 */
	public static function on__init() {

		if ( empty( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ) { // WPCS: input var ok, sanitization ok.
			// No language information in browser.
			return;
		}

		$cookie_name = WPGlobus::_COOKIE;

		if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
			/**
			 * First visit.
			 */
			$browser_language = substr( WPGlobus_WP::get_server_element( 'HTTP_ACCEPT_LANGUAGE' ), 0, 2 );
			/* @noinspection SummerTimeUnsafeTimeManipulationInspection */
			setcookie( $cookie_name, $browser_language, time() + 3600 * 24 * 365, '/' );
			self::redirect( $browser_language );

			return;
		}

	}

	/**
	 * Redirect to specified language.
	 *
	 * @param string $language
	 */
	public static function redirect( $language ) {

		if ( WPGlobus::Config()->language === $language ) {
			// Already in that language.
			return;
		}

		if ( ! in_array( $language, WPGlobus::Config()->enabled_languages, true ) ) {
			// No such language.
			return;
		}

		// Convert the current URL to the requested language and redirect.
		$current_url = WPGlobus_Utils::current_url();
		$redirect_to = WPGlobus_Utils::localize_url( $current_url, $language );

		/**
		 * Filter the `$redirect_to` URL.
		 * Returning a false value cancels redirect.
		 *
		 * @since 1.8.0
		 *
		 * @param string $redirect_to URL redirect to.
		 * @param string $language    Language redirect to.
		 *
		 * @return string|false
		 */
		$redirect_to = apply_filters( 'wpglobus_first_visit_redirect', $redirect_to, $language );

		if ( ! $redirect_to ) {
			return;
		}

		/**
		 * Todo This is for the old versions of WPGlobus Plus that do not have the above filter.
		 */
		if ( class_exists( 'WPGlobusPlus_Publish' )
			 && version_compare( WPGLOBUS_PLUS_VERSION, '1.1.31', '<' )
			 && is_singular()
		) {
			$post_status = get_post_meta( get_the_ID(), WPGlobusPlus_Publish::LANGUAGE_POST_STATUS, true );
			if ( isset( $post_status[ $language ] ) && 'draft' === $post_status[ $language ] ) {
				// This language is set to draft by WPGlobus Plus.
				return;
			}
		}

		wp_safe_redirect( $redirect_to );
		exit;
	}
}
