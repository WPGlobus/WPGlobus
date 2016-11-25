<?php
/**
 * WordPress utilities mocks
 *
 * @package WPGlobus\Unit-Tests
 */

/**
 * @param string $option_name
 *
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
 *
 * @return string
 */
function trailingslashit( $string ) {
	return untrailingslashit( $string ) . '/';
}

/**
 * @param string $string
 *
 * @return string
 */
function untrailingslashit( $string ) {
	return rtrim( $string, '/\\' );
}

/**
 * Set the scheme for a URL
 *
 * @since 3.4.0
 *
 * @param string $url    Absolute url that includes a scheme
 * @param string $scheme Optional. Scheme to give $url. Currently 'http', 'https', 'login', 'login_post', 'admin', or
 *                       'relative'.
 *
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

/**
 * @param string $tag   The name of the filter hook.
 * @param mixed  $value The value on which the filters hooked to `$tag` are applied on.
 * @param mixed  $arg1  Additional variables passed to the functions hooked to `$tag`.
 * @param mixed  $arg2  Additional variables passed to the functions hooked to `$tag`.
 *
 * @return mixed Modified `$value`
 */
function apply_filters( $tag, $value, $arg1 = null, $arg2 = null ) {

	if ( 'wpglobus_pre_domain_tld' === $tag && 'http://www.example.special-public-suffix.it' === $arg1 ) {
		$value = 'example.special-public-suffix.it';
	}

	return $value;
}

/**
 * @param string $sz
 *
 * @return string
 */
function esc_url( $sz ) {
	return $sz;
}

# --- EOF
