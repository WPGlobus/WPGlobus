<?php
/**
 * Copied from JT - TODO
 */

/**
 * JT Internationalization
 */

if ( class_exists( '\WPGlobus' ) ) {

	/**
	 * Language translation filters
	 * @since 15.03.09
	 */
	add_filter( 'jt_current_language', function ( $text ) {
		return WPGlobus_Filters::filter__text( $text );
	} );

	add_filter( 'jt_specific_language', function ( $text, $language ) {
		return WPGlobus_Core::text_filter( $text, $language );
	}, 0, 2 );

	add_filter( 'jt_default_language', function ( $text ) {
		return WPGlobus_Filters::filter__text_default_language( $text );
	} );

	add_filter( 'jt_localize_url', function ( $url ) {
		return \WPGlobus_Utils::localize_url( $url );
	} );

	/**
	 * Use this in the code instead of calling WPGlobus directly
	 * @example $languages = apply_filters( 'jt_enabled_languages', array() );
	 */
	add_filter( 'jt_enabled_languages', function ( Array $languages ) {
		$enabled_languages = WPGlobus::Config()->enabled_languages;
		if ( ! empty( $enabled_languages ) ) {
			$languages = $enabled_languages;
		}

		return $languages;
	} );

	/** @noinspection PhpUnusedParameterInspection */
	add_filter( 'jt_config_current_language', function ( $ignore ) {
		return WPGlobus::Config()->language;
	} );

	/** @noinspection PhpUnusedParameterInspection */
	add_filter( 'jt_config_default_language', function ( $ignore ) {
		return WPGlobus::Config()->default_language;
	} );

	/**
	 * Post and Taxonomy types where WPGlobus interface should be disabled
	 * (Do not need anymore: see WPGlobus settings)
	 */
	//is_admin() && add_filter( 'wpglobus_disabled_entities', function ( Array $disabled_entities ) {
	//	$disabled_entities[] = 'custom-design';
	//
	//	return $disabled_entities;
	//} );

	/**
	 * Show some links only in English interface
	 */
	is_admin() || add_action( 'wp', function () {
		add_filter( 'jt-display-link-to-blog', function ( $yes_no ) {
			return $yes_no && ( WPGlobus::Config()->language === 'en' );
		} );
		add_filter( 'jt-display-link-to-jobs', function ( $yes_no ) {
			return $yes_no && ( WPGlobus::Config()->language === 'en' );
		} );
	} );

	/**
	 * Add Bootstrap class to the language switcher dropdown menu
	 */
	is_admin() || add_filter( 'wp_nav_menu_primary-menu_items', function ( $items ) {
		return str_replace( 'class="sub-menu', 'class="sub-menu dropdown-menu', $items );
	} );

	/**
	 *    Filter for convert url with qTranslate
	 * @todo AJAX?
	 */
	add_filter( 'tivwp_qt_convertURL', function ( $url = '' ) {
		return \WPGlobus_Utils::localize_url( $url );
		//	global $q_config;
		//	if ( empty( $q_config['translate_cap'] ) ) {
		//		return $url;
		//	}
		//	if ( function_exists('qtrans_convertURL') ) {
		//		if ( $GLOBALS['woocommerce_qt']->mode == 2 ) {
		//			if ( defined('DOING_AJAX') && DOING_AJAX ) {
		//
		//				if ( isset( $_SERVER['HTTP_REFERER'] ) ) {
		//					global $q_config;
		//					$pos = strpos( $_SERVER['HTTP_REFERER'], $q_config['url_info']['host'] ) + strlen($q_config['url_info']['host'] ) + 1;
		//					$ajax_lang = substr( $_SERVER['HTTP_REFERER'], $pos, 2 );
		//					if ( qtrans_isEnabled($ajax_lang) && $ajax_lang != qtrans_getLanguage() ) {
		//						$q_config['language'] = $ajax_lang;
		//					}
		//				}
		//
		//			}
		//			$url = qtrans_convertURL( $url );
		//		}
		//	}
		//	return $url;
	} );


	/**
	 *    Filter for fix breadcrumb link to home page with qTranslate
	 * @todo Not using
	 */
	0 && add_filter( 'woocommerce_breadcrumb_home_url', function ( $home_url = '' ) {
		if ( ! isset( $GLOBALS['woocommerce_qt']->mode )
		     || ! isset( $GLOBALS['woocommerce_qt']->default_language )
		     || ! isset( $GLOBALS['woocommerce_qt']->current_language )
		) {

			return $home_url;
		}

		if ( $GLOBALS['woocommerce_qt']->current_language == $GLOBALS['woocommerce_qt']->default_language ) {
			return $home_url;
		}

		if ( $GLOBALS['woocommerce_qt']->mode == 1 ) {
			$home_url = add_query_arg( 'lang', $GLOBALS['woocommerce_qt']->current_language, $home_url );
		} elseif ( $GLOBALS['woocommerce_qt']->mode == 2 ) {
			$home_url = untrailingslashit( $home_url );
			$home_url = str_replace( str_replace( array( 'https:', 'http:' ), '', $home_url ), str_replace( array(
					'https:',
					'http:'
				), '', $home_url ) . '/' . $GLOBALS['woocommerce_qt']->current_language, $home_url . '/' );
		}

		return $home_url;
	} );

	/**
	 * Fix: Add [qtrans_url url] shortcode to translate urls

	 */
	add_shortcode( 'qtrans_url', function ( $attrs ) {
		return tivwp_localize_url( $attrs[0] );
	} );

	/**
	 * Urls can be passed through esc_attr
	 * 2014-11-19 (tivnet) This is all wrong.
	 */
	//add_filter('attribute_escape', function($text){
	//
	//	$url_info = @parse_url($text);
	//
	//	if ( isset( $url_info['path'] ) && !empty( $url_info['path'] ) ) {
	//		return apply_filters('tivwp_qt_convertURL', $text);
	//	}
	//
	//	return $text;
	//});

	/**
	 * Urls can be passed through esc_url, esc_url_raw
	 * @todo Does not work. Admin crashes.
	 */
	0 && add_filter( 'clean_url', function ( $url ) {
		if ( false === strpos( $url, 'wp-admin' ) && false === strpos( $url, 'wp-login' ) ) {
			return apply_filters( 'tivwp_qt_convertURL', $url );
		}

		return $url;
	} );

	/**
	 * Translate strings for WooCommerce Product Add-ons plugin
	 * for translation purposes need to fill  array $fields
	 * for each field in WC Product Add-ons plugin
	 * @todo Not using
	 */
	0 && add_filter( 'wc_get_product_addons', function ( $addons ) {
		$fields             = array();
		$fields[0]['name']  = __( 'Customize this design', WC_ONE_THEME_TEXT_DOMAIN );
		$fields[0]['label'] =
			__( 'Please write how you want this design to be customized in the box below and click ‘Add to Cart’. A customization fee will apply', WC_ONE_THEME_TEXT_DOMAIN );

		$i = 0;
		foreach ( $addons as $key => $addon ) {
			$addons[ $key ]['name']                = $fields[ $i ]['name'];
			$addons[ $key ]['options'][0]['label'] = $fields[ $i ]['label'];
			$i ++;
		}

		return $addons;
	} );

	/**
	 * Rewritten 'wp_redirect' filter
	 * @see    woocommerce-qtml\woocommerce-qtml.php:211
	 * @todo   Need to refactor? No wcqt now.
	 */
	0 && add_filter( 'wp_redirect', function ( $location ) {
		/** @global array $GLOBALS */
		if ( ! isset( $GLOBALS['woocommerce_qt']->mode )
		     || ! isset( $GLOBALS['woocommerce_qt']->default_language )
		     || ! isset( $GLOBALS['woocommerce_qt']->current_language )
		) {

			return $location;
		}

		if ( $GLOBALS['woocommerce_qt']->mode == 1 && ( ! is_admin() || is_ajax() ) && strpos( $location, 'wp-admin' ) === false ) {
			if ( strpos( $location, 'lang=' ) === false ) {
				$lang     = $GLOBALS['woocommerce_qt']->current_language;
				$lang     = rawurlencode( $lang );
				$arg      = array( 'lang' => $lang );
				$location = add_query_arg( $arg, $location );
			}
		} elseif ( $GLOBALS['woocommerce_qt']->mode == 2 && ( ! is_admin() || is_ajax() ) && strpos( $location, 'wp-admin' ) === false ) {
			foreach ( $GLOBALS['woocommerce_qt']->enabled_languages as $language ) {
				if ( strpos( urldecode( $location ), '/' . $language . '/' ) > 0 ) {
					return $location;
				} else {
					if ( strpos( $location, 'logout' ) === false && $GLOBALS['woocommerce_qt']->default_language != $GLOBALS['woocommerce_qt']->current_language ) {
						$location = qtrans_convertURL( $location );
						$location = str_replace( '&amp;', '&', $location );
					}

					return $location;
				}
			}
		}

		return $location;
	}, 99 );

	/**
	 * Add language manage for registered/unregistered users

	 */
	//tivwp_start_translation();
	/**
	 * @deprecated
	 */
	function tivwp_start_translation() {
		//	if ( ! is_admin() ) {
		//
		//		if ( ! isset($q_config['default_language']) || empty($q_config['default_language']) ) {
		//			/** qTranslate plugin not activated  */
		//			return;
		//		}
		//
		//		$q_config['translate_cap'] = false;
		//
		//		$TIVWP_options = get_option('TIVWP_options');
		//
		//		if ( class_exists('kgTranslator') && isset( $TIVWP_options['tivwp_options_translate_cap'] ) && 1 == $TIVWP_options['tivwp_options_translate_cap']  ) {
		//
		//			$q_config['translate_cap'] = true;
		//
		//			/** @class $kgTranslator */
		//			$enabled_languages = \kgTranslator::get_enabled_languages();
		//
		//			if ( ! in_array( $q_config['language'], $enabled_languages ) ) {
		//
		//				$q_config['enabled_languages'] = $enabled_languages;
		//				$q_config['default_language']  = $enabled_languages[0];
		//				$q_config['language'] = $enabled_languages[0];
		//
		//				wp_redirect( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		//				exit;
		//			}
		//		} else {
		//
		//			$q_config['enabled_languages'] 	 = array();
		//			$q_config['enabled_languages'][] = $q_config['default_language'];
		//
		//			if ( $q_config['language'] != $q_config['default_language'] ) {
		//
		//				$q_config['language'] = $q_config['default_language'];
		//
		//				wp_redirect( $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
		//				exit;
		//
		//			}
		//
		//		}
		//	}
	}

	/** @todo  What is this? */
	if ( 0 ) {
		$TIVWP_options = get_option( 'TIVWP_options' );

		if ( false && isset( $TIVWP_options['tivwp_options_translate_cap'] ) && 1 == $TIVWP_options['tivwp_options_translate_cap'] ) {

			/**
			 * Handle redirect on client side.
			 * @see  app/plugins/fabthat/TIVWP/js/main.js
			 * @todo Currently only pre-path mode is supported. Support multiple mods
			 */
			add_action( 'wp_footer', function () {
				global $q_config;
				?>
				<script type="text/javascript">

					var qt_default_language = '<?php echo $q_config['default_language']; ?>';
					var qt_language = '<?php echo $q_config['language']; ?>';

					redirect.add_filter(function (url) {
						if (qt_language != qt_default_language) {
							var url_info = parse_url(url);
							return url.replace(url_info.path, '/' + qt_language + url_info.path);
						}
						return url;
					});

				</script>
			<?php
			}, 100, 0 );
		}

		if ( false && isset( $TIVWP_options['tivwp_options_translate_url_check'] ) && 1 == $TIVWP_options['tivwp_options_translate_url_check'] ) {

			/**
			 * Check if all urls are with right language
			 */
			add_action( 'wp_footer', function () {
				global $q_config;

				if ( $q_config['language'] != $q_config['default_language'] ): ?>
					<script type="text/javascript">

						var qtrans_language = '/<?php echo $q_config['language'];?>';

						function ckurllang() {

							var count = 0;

							jQuery('a').each(function () {

								var ok =
									this.href.search('jewelrythis.com') != -1
									&& this.href.search(qtrans_language) == -1
									&& this.href.search('/wp') == -1
									&& this.className.search('qtrans_flag') == -1
									&& this.href.search('add-to-cart') == -1
									&& this.href.search('add-to-cart') == -1
									&& this.href.search('download_file') == -1
									&& this.href.search('app/uploads') == -1;

								if (ok) {
									jQuery(this).css('font-size', '32px').css('color', 'red').css('display', '');
									console.error(this.href, this);
									count++;
								}
							});

							if (count) {
								console.error('Found ' + count + ' wrong links. Open browser console for details');
							}
							else {
								console.log('All links on this page are OK');
							}
						}

						jQuery(window).ready(ckurllang);
					</script>
				<?php endif;
			}, 100, 0 );
		}
	}

	/**
	 * @deprecated
	 * Ensure that URL is localized
	 * Currently uses qTranslate, if active
	 * @example www.example.com/{locale}/path/
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	function tivwp_localize_url( $url = '' ) {
		return WPGlobus_Utils::localize_url( $url );
	}

	/**
	 * @param string $lang
	 * @param string $text
	 *
	 * @return string
	 * @deprecated
	 */
	function qtrans_use( $lang, $text ) {

		if ( is_array( $text ) ) {
			// handle arrays recursively
			foreach ( $text as $key => $t ) {
				$text[ $key ] = qtrans_use( $lang, $text[ $key ] );
			}

			return $text;
		}

		return WPGlobus_Core::text_filter( $text, $lang );
	}

	/**
	 * @param string $url
	 * @param string $lang
	 * @param bool   $forceadmin
	 * @param string $force_scheme
	 *
	 * @return string
	 * @deprecated
	 */
	function qtrans_convertURL(
		$url = '', $lang = '', /** @noinspection PhpUnusedParameterInspection */
		$forceadmin = false, /** @noinspection PhpUnusedParameterInspection */
		$force_scheme = 'https'
	) {
		return WPGlobus_Utils::get_convert_url( $url, $lang );
	}

	/**
	 * @param string $ignore
	 *
	 * @return string[]
	 * @deprecated
	 */
	function qtrans_getAvailableLanguages(
		/** @noinspection PhpUnusedParameterInspection */
		$ignore = ''
	) {
		return WPGlobus::Config()->enabled_languages;
	}

} else {
	error_log( basename( __FILE__ ) . ': WPGlobus plugin must be active.' );
}

# --- EOF