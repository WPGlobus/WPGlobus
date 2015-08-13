<?php
/**
 * WordPress utilities mocks
 *
 * @package WPGlobus
 */

/**
 * @param string $option_name
 * @return string
 */
function get_option( $option_name ) {
	$option = '';
	if ( 'home' === $option_name ) {
		$option = WPGlobus_Utils__Test::$option_home;
	}

	return $option;
}

/**
 * @param string $string
 * @return string
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * @param string $string
 * @return string
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

/**
 * Set the scheme for a URL
 *
 * @since 3.4.0
 * @param string $url    Absolute url that includes a scheme
 * @param string $scheme Optional. Scheme to give $url. Currently 'http', 'https', 'login', 'login_post', 'admin', or
 *                       'relative'.
 * @return string $url URL with chosen scheme.
 */
function set_url_scheme(
	$url, /** @noinspection PhpUnusedParameterInspection */
	$scheme = null
) {
	return $url;
}

/**
 * @return bool
 */
function is_404() {
	return WPGlobus_Utils__Test::$is_404_response;
}

# --- EOF
