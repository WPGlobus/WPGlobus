<?php

/**
 * Class WPGlobus_Utils
 */
class WPGlobus_Utils {

	/**
	 * Localize URL by inserting language prefix
	 *
	 * @param string          $url      URL to localize
	 * @param string          $language Language code
	 * @param WPGlobus_Config $config   Alternative configuration (i.e. Unit Test mock object)
	 *
	 * @return string
	 */
	public static function localize_url( $url = '', $language = '', WPGlobus_Config $config = null ) {

		/**
		 * Use the global configuration is alternative not passed
		 */
		if ( is_null( $config ) ) {
			// @codeCoverageIgnoreStart
			$config = WPGlobus::Config();
		}
		// @codeCoverageIgnoreEnd

		/**
		 * In Admin-Settings-General:
		 * WordPress Address (URL) is site_url()
		 * Site Address (URL) is home_url
		 * We need home_url, and we cannot use the @home_url function,
		 * because it will filter back here causing endless loop.
		 * @todo Multisite?
		 */
		$home_url = get_option( 'home' );

		/**
		 * Use the current language if not passed
		 */
		$language = empty( $language ) ? $config->language : $language;

		/**
		 * `hide_default_language` means "Do not use language code in the default URL"
		 * So, no /en/page/, just /page/
		 */
		if ( $language === $config->default_language && $config->hide_default_language ) {
			$language_url_prefix = '';
		} else {
			/**
			 * Language prefix looks like '/ru'
			 */
			$language_url_prefix = '/' . $language;
		}

		/**
		 * For the following regex, we need home_url without prefix
		 * http://www.example.com becomes example.com
		 */
		$home_domain_tld = self::domain_tld( $home_url );

		/**
		 * Regex to replace current language prefix with the requested one.
		 * @example ^(https?:\/\/(?:.+\.)?example\.com)(?:\/?(?:en|ru|pt))?($|\/$|[\/#\?].*$)
		 */

		/**
		 * The "host+path" part of the URL (captured)
		 * We ignore http(s) and domain prefix, but we must match the domain-tld, so any external URLs
		 * are not localized.
		 */
		$re_host_part = '(https?:\/\/(?:.+\.)?' .
		                str_replace( '.', '\.', $home_domain_tld ) .
		                str_replace( '/', '\/', parse_url( $home_url, PHP_URL_PATH ) )
		                . ')';

		/**
		 * The "language" part (optional, not captured, will be thrown away)
		 */
		$re_language_part = '(?:\/?(?:' . join( '|', $config->enabled_languages ) . '))?';

		/**
		 * The rest of the URL. Can be:
		 * - Nothing or trailing slash, or
		 * - Slash, hash or question and optionally anything after
		 * *
		 * Using 'or' regex to capture things like '/rush' or '/designer/' correctly,
		 * and not extract '/ru' or '/de' from them,
		 */
		$re_trailer = '(\/?|[\/#\?].*)';

		$re = '!^' . $re_host_part . $re_language_part . $re_trailer . '$!';

		/**
		 * Replace the existing (or empty) language prefix with the requested one
		 */
		$localized_url = preg_replace( $re, '\1' . $language_url_prefix . '\2', $url );

		return $localized_url;
	}

	/**
	 * Extract language from URL
	 * http://example.com/ru/page/ returns 'ru'
	 *
	 * @param string          $url
	 * @param WPGlobus_Config $config Alternative configuration (i.e. Unit Test mock object)
	 *
	 * @return string
	 */
	public static function extract_language_from_url( $url = '', WPGlobus_Config $config = null ) {

		$language = '';

		if ( ! is_string( $url ) ) {
			return $language;
		}

		/**
		 * Use the global configuration is alternative not passed
		 */
		if ( is_null( $config ) ) {
			// @codeCoverageIgnoreStart
			$config = WPGlobus::Config();
		}
		// @codeCoverageIgnoreEnd

		$path = parse_url( $url, PHP_URL_PATH );

		$path_home = untrailingslashit( parse_url( get_option( 'home' ), PHP_URL_PATH ) );

		/**
		 * Regex to find the language prefix.
		 * @example !^/(en|ru|pt)/!
		 */
		$re = '!^' . $path_home .
		      '/(' . join( '|', $config->enabled_languages ) . ')(?:/|$)' . '!';

		if ( preg_match( $re, $path, $match ) ) {
			// Found language information
			$language = $match[1];
		}

		return $language;

	}

	/**
	 * Check if was called by a specific function (could be any levels deep).
	 * Note: does not check if the function is in a class method.
	 *
	 * @param string $function_name
	 *
	 * @return bool
	 */
	public static function is_function_in_backtrace( $function_name ) {
		$function_in_backtrace = false;

		foreach ( debug_backtrace() as $_ ) {
			if ( ! empty( $_['function'] ) && $_['function'] === $function_name ) {
				$function_in_backtrace = true;
				break;
			}
		}

		return $function_in_backtrace;
	}

	/**
	 * Strip the prefix from the host name
	 * http://www.example.com becomes example.com
	 *
	 * @param string $url
	 *
	 * @return string
	 * @since 1.0.12
	 */
	public static function domain_tld( $url ) {
		$domain_tld = parse_url( $url, PHP_URL_HOST );

		if ( ! $domain_tld ) {
			/**
			 * parse_url failed
			 * Let's return the original url.
			 * Works if URL passed without scheme (just 'www.example.com' and not 'http://www.example.com' )
			 */
			return $url;
		}

		$host_components = explode( '.', $domain_tld );

		if ( is_numeric( $host_components[0] ) ) {
			/**
			 * It's an IP address. Do nothing.
			 */
		} else {
			/**
			 * Strip all prefixes
			 * @todo example.co.uk becomes just co.uk
			 *       This does not break the algorithm of @see localize_url, but still needs to be fixed.
			 */

			$num_components = count( $host_components );
			if ( $num_components > 2 ) {
				$domain_tld = $host_components[ $num_components - 2 ]
				              . '.'
				              . $host_components[ $num_components - 1 ];
			}
		}

		return $domain_tld;
	}

	/**
	 * Convert array of local texts to multilingual string (with WPGlobus delimiters)
	 *
	 * @param string[] $translations
	 *
	 * @return string
	 */
	public static function build_multilingual_string( $translations ) {
		$sz = '';
		foreach ( $translations as $language => $text ) {
			$sz .= WPGlobus::add_locale_marks( $text, $language );
		}

		return $sz;
	}

	/**
	 * Returns the current URL.
	 * @since 1.1.1
	 * There is no method of getting the current URL in WordPress.
	 * Various snippets published on the Web use a combination of home_url and add_query_arg.
	 * However, none of them work when WordPress is installed in a subfolder.
	 * The method below looks valid. There is a theoretical chance of HTTP_HOST tampered, etc.
	 * However, the same line of code is used by the WordPress core, for example in
	 * @see   wp_admin_canonical_url
	 * so we are going to use it, too
	 * *
	 * Note that #hash is always lost because it's a client-side parameter.
	 * We might add it using a JavaScript call.
	 */
	public static function current_url() {
		return set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
	}

	/**
	 * Build hreflang metas
	 * @since 1.1.1
	 *
	 * @param WPGlobus_Config $config Alternative configuration (i.e. Unit Test mock object)
	 *
	 * @return string[] Array of rel-alternate link tags
	 */
	public static function hreflangs( WPGlobus_Config $config = null ) {

		/**
		 * Use the global configuration is alternative not passed
		 */
		if ( is_null( $config ) ) {
			// @codeCoverageIgnoreStart
			$config = WPGlobus::Config();
		}
		// @codeCoverageIgnoreEnd

		$hreflangs = array();

		$ref_source = self::localize_url( self::current_url(), '%%lang%%', $config );

		foreach ( $config->enabled_languages as $language ) {
			$hreflang = str_replace( '_', '-', $config->locale[ $language ] );
			if ( $config->hide_default_language && $language == $config->default_language ) {
				$ref = str_replace( '%%lang%%/', '', $ref_source );
			} else {
				$ref = str_replace( '%%lang%%', $language, $ref_source );
			}
			$hreflangs[ $language ] = '<link rel="alternate" hreflang="' . $hreflang . '" href="' . $ref . '"/>';

		}

		return $hreflangs;
	}

	/**
	 * @todo The methods below are not used by the WPGlobus plugin. Need to check if they are used by any add-on.
	 *       Marking them as deprecated so they will pop-up on code inspection.
	 */

	/**
	 * @deprecated
	 * @codeCoverageIgnore
	 * Return true if language is in array of enabled languages, otherwise false
	 *
	 * @param string $language
	 *
	 * @return bool
	 */
	public static function is_enabled( $language ) {
		return in_array( $language, WPGlobus::Config()->enabled_languages );
	}

	/**
	 * @deprecated
	 * @codeCoverageIgnore
	 * Return true if language is in array of opened languages, otherwise false
	 *
	 * @param string $language
	 *
	 * @return bool
	 */
	public static function is_open( $language ) {
		return in_array( $language, WPGlobus::Config()->open_languages );
	}

	/**
	 * @deprecated
	 * @codeCoverageIgnore
	 *
	 * @param string $s
	 * @param string $n
	 *
	 * @return bool
	 */
	public static function starts_with( $s, $n ) {
		if ( strlen( $n ) > strlen( $s ) ) {
			return false;
		}
		if ( $n == substr( $s, 0, strlen( $n ) ) ) {
			return true;
		}

		return false;
	}


} // class

# --- EOF