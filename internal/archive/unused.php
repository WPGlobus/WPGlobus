<?php
/**
 *
 */
class _unused_code_201506 {
	/**
	 * @param string $url
	 * @param string $host
	 * @param string $referer
	 *
	 * @return array
	 */
	public static function extract_url( $url, $host = '', $referer = '' ) {

		$referer_save = $referer;

		$home_url = get_option( 'home' );

		$home         = self::parse_url( $home_url );
		$home['path'] = trailingslashit( $home['path'] );
		$referer      = self::parse_url( $referer );

		$result                     = array();
		$result['language']         = WPGlobus::Config()->default_language;
		$result['url']              = $url;
		$result['original_url']     = $url;
		$result['host']             = $host;
		$result['redirect']         = false;
		$result['internal_referer'] = false;
		$result['home']             = $home['path'];
		$result['schema']           = is_ssl() ? 'https://' : 'http://';

		//		switch ( WPGlobus::Config()->get_url_mode() ) {
		//			case WPGlobus_Config::GLOBUS_URL_PATH:
		// pre url
		$url = substr( $url, strlen( $home['path'] ) );
		if ( $url ) {
			// might have language information
			if ( preg_match( "#^([a-z]{2})(\/|\?|$)#", $url, $match ) ) {
				if ( self::is_enabled( $match[1] ) ) {
					// found language information
					$result['language'] = $match[1];
					$result['url']      = $home['path'] . substr( $url, 3 );
				}
			}
		}
		//				break;
		//			case WPGlobus_Config::GLOBUS_URL_DOMAIN:
		//				// pre domain
		//				if ( $host ) {
		//					if ( preg_match( "#^([a-z]{2}).#i", $host, $match ) ) {
		//						if ( self::is_enabled( $match[1] ) ) {
		//							// found language information
		//							$result['language'] = $match[1];
		//							$result['host']     = substr( $host, 3 );
		//						}
		//					}
		//				}
		//				break;
		//		}

		// check if referer is internal
		if ( $referer['host'] == $result['host'] && self::starts_with( $referer['path'], $home['path'] ) ) {
			// user coming from internal link
			$result['internal_referer'] = true;
		}

		if ( isset( $_GET['lang'] ) && self::is_enabled( $_GET['lang'] ) ) {
			// language override given
			$result['language'] = $_GET['lang'];
			$result['url']      = preg_replace( "#(&|\?)lang=" . $result['language'] . "&?#i", "$1", $result['url'] );
			$result['url']      = preg_replace( "#[\?\&]+$#i", "", $result['url'] );

		} elseif ( $home['host'] == $result['host'] && $home['path'] == $result['url'] ) {

			if ( empty( $referer['host'] ) || ! WPGlobus::Config()->hide_default_language ) {

				$result['redirect'] = true;

			} else {
				// check if activating language detection is possible
				if ( preg_match( "#^([a-z]{2}).#i", $referer['host'], $match ) ) {
					if ( self::is_enabled( $match[1] ) ) {
						// found language information
						$referer['host'] = substr( $referer['host'], 3 );
					}
				}
				if ( ! $result['internal_referer'] ) {
					// user coming from external link
					$result['redirect'] = true;
				}
			}
		}

		/**
		 * If DOING_AJAX, we cannot retrieve the language information from the URL,
		 * because it's always `admin-ajax`. Therefore, we'll rely on the HTTP_REFERER.
		 * @since 1.0.9
		 */
		if ( ! empty( $referer_save ) && WPGlobus_WP::is_doing_ajax() ) {

			$language_in_referer = self::extract_language_from_url( $referer_save );

			if ( ! empty( $language_in_referer ) ) {
				// Found language information
				$result['language'] = $language_in_referer;
			}

		}


		return $result;
	}

	/**
	 * @param string $url
	 *
	 * @return false
	 * @return array
	 * @todo Why not use native PHP method?
	 * @see  parse_url()
	 */
	public static function parse_url( $url ) {

		if ( empty( $url ) ) {
			return false;
		}

		$scheme   = '(?:(\w+)://)';
		$userpass = '(?:(\w+)\:(\w+)@)';
		$host     = '([^/:]+)';
		$port     = '(?:\:(\d*))';
		$path     = '(/[^#?]*)';
		$query    = '(?:\?([^#]+))';
		$fragment = '(?:#(.+$))';

		$r =
			'!' . $scheme . '?' . $userpass . '?' . $host . '?' . $port . '?' . $path . '?' . $query . '?' . $fragment . '?!i';

		preg_match( $r, $url, $out );

		$result = array(
			"scheme"   => ( empty( $out[1] ) ? '' : $out[1] ),
			"host"     => ( empty( $out[4] ) ? '' : $out[4] ) . ( empty( $out[5] ) ? '' : ':' . $out[5] ),
			"user"     => ( empty( $out[2] ) ? '' : $out[2] ),
			"pass"     => ( empty( $out[3] ) ? '' : $out[3] ),
			"path"     => ( empty( $out[6] ) ? '' : $out[6] ),
			"query"    => ( empty( $out[7] ) ? '' : $out[7] ),
			"fragment" => ( empty( $out[8] ) ? '' : $out[8] )
		);

		// Host can be in path in case of url with incorrect scheme. Try to find it in path
		if ( empty( $result['host'] ) ) {
			$www    = '(www\.)';
			$domain = '((?:\w+\.)+\w+)';

			$r2 = '!' . $www . '?' . $domain . $path . '?!i';

			if ( preg_match( $r2, $url, $out2 ) ) {
				$result['host'] = $out2[1] . $out2[2];
				/**
				 * @todo check /wp-admin/edit.php?post_type=product with WPGlobus WC
				 * PHP Notice:  Undefined offset: 3 in class-wpglobus-utils.php
				 */
				$result['path'] = isset( $out2[3] ) ? $out2[3] : '';
			}
		}

		return $result;
	}


}