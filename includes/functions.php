<?php

/**
 * @deprecated 15.01.20 Calls wp_get_object_terms, which is already filtered
 */
//add_filter( 'get_the_terms', 'wpglobus_filter_get_terms', 0 );

/**
 * Admin: now use filter for get_terms_to_edit function. See meta-boxes.php file.
 * @scope admin Edit post: see "Tags" metabox
 *        Does NOT affect the "Categories" metabox
 * @scope front WC breadcrumb
 */
add_filter( 'wp_get_object_terms', 'wpglobus_filter__wp_get_object_terms', 0 );

/**
 * Filter @see wp_get_object_terms()
 * @scope admin
 * @scope front
 *
 * @param string[]|object[] $terms
 *
 * @return array
 */
function wpglobus_filter__wp_get_object_terms( Array $terms ) {
	/**
	 * @internal
	 * Do not need to check for is_wp_error($terms),
	 * because the WP_Error is returned by wp_get_object_terms() before applying filter.
	 * Do not need to check for empty($terms) because foreach won't loop.
	 */

	/**
	 * Don't filter tag names for save or publish post
	 * @todo Check this before add_filter and not here
	 * @todo Describe exactly how to check this visually, and is possible - write the acceptance test
	 * @todo Combine if()s
	 * @todo replace isset with !empty
	 * @todo pagenow can be mixed (?) - we need a function instead of using '===', to avoid notices
	 */
	global $pagenow;
	if ( is_admin() && 'post.php' === $pagenow ) {
		if ( isset( $_POST['save'] ) || isset( $_POST['publish'] ) ) {
			return $terms;
		}
	}
	
	/** @global WPGlobus_Config $WPGlobus_Config */
	global $WPGlobus_Config;

	foreach ( $terms as &$term ) {
		WPGlobus_Core::translate_term( $term, $WPGlobus_Config->language );
	}

	reset( $terms );

	return $terms;
}

/**
 * Filter set title in default_language for correct generate permalink in edit-slug-box at post.php screen
 * @todo move to admin controller
 */
add_filter( 'editable_slug', 'wpg_text_title_filter', 0 );

/**
 * Set editable piece of permalink in default language
 * @see  get_sample_permalink()
 * @todo Examine option when user has 2 languages at front-end (ru, kz) but use 'en' for permalink
 *
 * @param $uri
 *
 * @return string
 */
function wpg_text_title_filter( $uri ) {
	global $WPGlobus_Config;

	return __wpg_text_filter( $uri, $WPGlobus_Config->default_language );
}

/**
 * This translates all taxonomy names, including categories
 * @todo Should cache this and not parse on every page
 *
 * @param array|object $terms
 *
 * @return array|object
 */
function wpglobus_filter_get_terms( $terms ) {

	/**
	 * @todo This condition applies to get_term filter only
	 */
	if ( isset( $_POST ) && isset( $_POST['action'] ) && 'inline-save-tax' == $_POST['action'] ) {
		/**
		 * Don't filter ajax action 'inline-save-tax' from edit-tags.php page.
		 * @see quick_edit() in wpglobus\includes\js\wpglobus.admin.js for working with taxonomy name and description
		 *                   wp_current_filter contains
		 *                   0=wp_ajax_inline-save-tax
		 *                   1=get_term
		 * @see wp_ajax_inline_save_tax()
		 * calling @see get_term()
		 */
		return $terms;
	}

	if ( is_array( $terms ) ) {

		foreach ( $terms as &$term ) {
			if ( is_object( $term ) ) {
				if ( ! empty( $term->name ) ) {
					$term->name = __wpg_text_filter( $term->name );
				}
				if ( ! empty( $term->description ) ) {
					$term->description = __wpg_text_filter( $term->description );
				}
			} else {
				/**
				 * Case ajax-tag-search action from post.php screen
				 * @see function wp_ajax_ajax_tag_search() in wp-admin\includes\ajax-actions.php
				 */
				if ( isset( $term ) ) {
					$term = __wpg_text_filter( $term );
				}
			}
		} // end foreach

	} else {
		/**
		 *  Filter 'get_term' use $terms as object
		 */
		if ( ! empty( $terms->name ) ) {
			$terms->name = __wpg_text_filter( $terms->name );
		}
		if ( ! empty( $terms->description ) ) {
			$terms->description = __wpg_text_filter( $terms->description );
		}
	}

	reset( $terms );

	return $terms;
}

add_filter( 'home_url', 'on_home_url' );

/**
 * Localize home_url
 *
 * @param string $url
 *
 * @return string
 */
function on_home_url( $url ) {
	global $pagenow;

	$ajaxify = false;

	if ( 'post.php' == $pagenow ) {
		/**
		 * Don't convert url for permalink below post title field
		 * For example, we had Постоянная ссылка: http://www.wpg.dev/ru/wordpress-4-1-is-out/
		 * @todo Need will check for other cases using url in post.php, post-new.php screens
		 */
		return $url;
	}

	if ( 'admin-ajax.php' == $pagenow ) {
		/**
		 * Don't convert url for ajax action with $_POST[action] == heartbeat, sample-permalink, add-menu-item
		 * For more info see $_POST array

		 */
		if ( array_key_exists( 'action', $_POST ) && in_array( $_POST['action'], array(
				'heartbeat',
				'sample-permalink',
				'add-menu-item'
			) )
		) {
			return $url;
		}
		$ajaxify = true;
	}

	/**
	 * @todo Need test this code!
	 */
	if ( is_admin() && ! $ajaxify ) {
		return $url;
	}

	return WPGlobus_Utils::get_convert_url( $url );
}

/**
 * Yoast filters
 */
if ( defined( 'WPSEO_VERSION' ) ) {
	add_filter( 'wpseo_title', 'wpg_text_filter', 0 );
	add_filter( 'wpseo_metadesc', 'wpg_text_filter', 0 );

	/**
	 * For translate wpseo meta 'Focus KW' at edit.php page
	 * @todo need to discuss this filter
	 */
	add_filter( 'esc_html', 'wpg_text_filter', 0 );

}

/**
 * Common filters
 */
add_filter( 'the_title', 'wpg_text_filter', 0 );
add_filter( 'the_content', 'wpg_text_filter', 0 );

/**
 * We don't use 'the_excerpt' filter because 'get_the_excerpt' will be run anyway
 * @see  function the_excerpt()
 * @todo look at 'the_excerpt_export' filter where the post excerpt used for WXR exports.
 */
add_filter( 'get_the_excerpt', 'wpg_text_filter', 0 );

add_filter( 'wp_title', 'wpg_text_filter', 0 );
add_filter( 'widget_title', 'wpg_text_filter', 0 );

add_filter( 'single_post_title', 'wpg_text_filter', 0 );

/**
 * @see single_term_title()
 */
add_filter( 'single_cat_title', 'wpg_text_filter', 0 );
add_filter( 'single_tag_title', 'wpg_text_filter', 0 );
add_filter( 'single_term_title', 'wpg_text_filter', 0 );

add_filter( 'get_pages', 'wpg_text_filter', 0 );


/**
 * Set priority to 11 for case ajax-tag-search action from post.php screen
 * @see wp_ajax_ajax_tag_search() in wp-admin\includes\ajax-actions.php
 * Note: this filter is temporarily switched off in @see WPGlobus::_get_terms
 * @todo Replace magic number 11 with a constant
 */
add_filter( 'get_terms', 'wpglobus_filter_get_terms', 11 );

global $pagenow;
if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || in_array( $pagenow, array( 'nav-menus.php' ) ) || ! is_admin() ) {
	add_filter( 'get_term', 'wpglobus_filter_get_terms', 0 );
}


/**
 * Option filters
 */
/**
 * At admin we need see string with language shortcodes
 */
if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! is_admin() ) {
	add_filter( 'option_blogdescription', 'wpg_text_filter', 0 );
}


/**
 * Filters for admin
 */


/**
 * @param mixed $object
 *
 * @return mixed
 */
function wpg_text_filter( $object = '' ) {

	/**
	 * @see function qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage
	 */
	if ( empty( $object ) ) {
		// Nothing to do
		return $object;
	}

	global $WPGlobus_Config;

	/**
	 * Check $object is array of WP_Post objects
	 * for example @see get_pages() function in \wp-includes\post.php
	 * @qa See a list of available pages in the "Parent Page" metabox when editing a page.
	 */
	if ( is_array( $object ) ) {

		foreach ( $object as &$post ) {
			if ( is_object( $post ) && 'WP_Post' == get_class( $post ) ) {
				WPGlobus_Core::translate_wp_post( $post, $WPGlobus_Config->language );
			}
		}

		reset( $object );

		return $object;

	}

	$object = __wpg_text_filter( $object );

	return $object;

}

/**
 * @deprecated 15.01.17
 *
 * @param string $text
 * @param string $language
 * @param string $return
 *
 * @return string
 */
function __wpg_text_filter( $text = '', $language = '', $return = WPGlobus::RETURN_IN_DEFAULT_LANGUAGE ) {
	global $WPGlobus_Config;
	if ( empty( $language ) ) {
		$language = $WPGlobus_Config->language;
	}

	return WPGlobus_Core::text_filter( $text, $language, $return, $WPGlobus_Config->default_language );
}


add_filter( 'locale', 'wpg_locale', 99 );
/**
 * @param Array $locale
 *
 * @return mixed
 */
function wpg_locale(
	/** @noinspection PhpUnusedParameterInspection */
	$locale
) {

	global $WPGlobus_Config;

	// try to figure out the correct locale
	/*
	$locale = array();
	$locale[] = $q_config['locale'][$q_config['language']].".utf8";
	$locale[] = $q_config['locale'][$q_config['language']]."@euro";
	$locale[] = $q_config['locale'][$q_config['language']];
	$locale[] = $q_config['windows_locale'][$q_config['language']];
	$locale[] = $q_config['language'];
	
	// return the correct locale and most importantly set it (wordpress doesn't, which is bad)
	// only set LC_TIME as everything else doesn't seem to work with windows
	setlocale(LC_TIME, $locale);
	// */

	$locale = $WPGlobus_Config->locale[ $WPGlobus_Config->language ];

	/** @todo What about AJAX? */
	if ( is_admin() ) {
		/**
		 * Need to check WPLANG option for WP4.1
		 * @todo There is a WP method
		 * @see  get_locale()
		 */
		$db_locale = get_option( 'WPLANG' );
		if ( ! empty( $db_locale ) ) {
			$locale = $db_locale;
			$WPGlobus_Config->set_language( $locale );
		}
	}

	return $locale;

}

add_action( 'init', 'wpg_init', 2 );
function wpg_init() {

	// check if it isn't already initialized
	if ( defined( 'WPGLOBUS_INIT' ) ) {
		return;
	}

	define( 'WPGLOBUS_INIT', true );

	global $WPGlobus_Config;

	//wp_redirect('http://wpml2.dev/ru/news/hello-world');
	//exit();	

	//wpg_loadConfig();
	/*
	if(isset($_COOKIE['qtrans_cookie_test'])) {
		$q_config['cookie_enabled'] = true;
	} else  {
		$q_config['cookie_enabled'] = false;
	}
	// */

	// init Javascript functions
	//qtrans_initJS();

	// update Gettext Databases if on Backend
	//if(defined('WP_ADMIN') && $q_config['auto_update_mo']) qtrans_updateGettextDatabases();

	// update definitions if necessary
	//if(defined('WP_ADMIN') && current_user_can('manage_categories')) qtrans_updateTermLibrary();

	// extract url information
	//$q_config['url_info'] = wpg_extractURL($_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

	/** @todo check at class-wpglobus.php:103 for set url_info */
	$WPGlobus_Config->url_info =
		WPGlobus_Utils::extract_url( $_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );

	//error_log( print_r( $WPGlobus_Config->url_info, true ));

	/**
	 * Add hack for support AJAX
	 */
	/*
	if ( defined('DOING_AJAX') && DOING_AJAX && isset( $_SERVER['HTTP_REFERER'] ) ) {
		$referer_info = wpg_parseURL( $_SERVER['HTTP_REFERER'] );
		$q_config['url_info'] = wpg_extractURL(
			$referer_info['path'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
	} */
	/* end hack code	*/

	// set test cookie
	//setcookie('qtrans_cookie_test', 'qTranslate Cookie Test', 0, $q_config['url_info']['home'], $q_config['url_info']['host']);

	// check cookies for admin

	/**
	 * Add hack in 1 line for support AJAX
	 * if(defined('WP_ADMIN')) {}
	 */
	/* 
	if(defined('WP_ADMIN') && !(defined('DOING_AJAX') && DOING_AJAX) ) {
		if(isset($_GET['lang']) && wpg_isEnabled($_GET['lang'])) {
			$q_config['language'] = $q_config['url_info']['language'];
			setcookie('qtrans_admin_language', $q_config['language'], time()+60*60*24*30);
		} elseif(isset($_COOKIE['qtrans_admin_language']) && wpg_isEnabled($_COOKIE['qtrans_admin_language'])) {
			$q_config['language'] = $_COOKIE['qtrans_admin_language'];
		} else {
			$q_config['language'] = $q_config['default_language'];
		}
	} else {
		// $q_config['language'] = $q_config['url_info']['language'];
		$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];
	}
	// */

	//$q_config['language'] = apply_filters('qtranslate_language', $q_config['language']);


	/*
	// detect language and forward if needed
	//if($q_config['detect_browser_language'] && $q_config['url_info']['redirect'] && !isset($_COOKIE['qtrans_cookie_test']) && $q_config['url_info']['language'] == $q_config['default_language']) {
		$target = false;
		$preferred_languages = array();
		if(isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && preg_match_all("#([^;,]+)(;[^,0-9]*([0-9\.]+)[^,]*)?#i",$_SERVER["HTTP_ACCEPT_LANGUAGE"], $matches, PREG_SET_ORDER)) {
			$priority = 1.0;
			foreach($matches as $match) {
				if(!isset($match[3])) {
					$pr = $priority;
					$priority -= 0.001;
				} else {
					$pr = floatval($match[3]);
				}
				$preferred_languages[$match[1]] = $pr;
			}
			arsort($preferred_languages, SORT_NUMERIC);
			foreach($preferred_languages as $language => $priority) {
				if(strlen($language)>2) $language = substr($language,0,2);
				if(qtrans_isEnabled($language)) {
					if($q_config['hide_default_language'] && $language == $q_config['default_language']) break;
					$target = qtrans_convertURL(get_option('home'),$language);
					break;
				}
			}
		}
		//$target = apply_filters("qtranslate_language_detect_redirect", $target);
		if($target !== false) {
			//error_log( 'target is HERE' );
			wp_redirect($target);
			exit();
		} else {
			//error_log( 'target is FALSE' );
		}
	}
	// */

	/*
	// Check for WP Secret Key Mismatch
	global $wp_default_secret_key;
	if(strpos($q_config['url_info']['url'],'wp-login.php')!==false && defined('AUTH_KEY') && isset($wp_default_secret_key) && $wp_default_secret_key != AUTH_KEY) {
		global $error;
		$error = __('Your $wp_default_secret_key is mismatching with your AUTH_KEY. This might cause you not to be able to login anymore.','qtranslate');
	}
	*/

	// Filter all options for language tags
	/*
	if(!defined('WP_ADMIN')) {
		$alloptions = wp_load_alloptions();
		foreach($alloptions as $option => $value) {
			add_filter('option_'.$option, 'qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage',0);
		}
	} // */

	// load plugin translations
	//load_plugin_textdomain('qtranslate', false, dirname(plugin_basename( __FILE__ )).'/lang');

	// remove traces of language (or better not?)
	//unset($_GET['lang']);


	$_SERVER['REQUEST_URI'] = $WPGlobus_Config->url_info['url'];
	$_SERVER['HTTP_HOST']   = $WPGlobus_Config->url_info['host'];

	// fix url to prevent xss
	//$q_config['url_info']['url'] = qtrans_convertURL(add_query_arg('lang',$q_config['default_language'],$q_config['url_info']['url']));
}

/*
add_filter( 'the_posts', 'wpg_postsFilter', 0 );
function wpg_postsFilter($posts) {
	if(is_array($posts)) {
		foreach($posts as $post) {
			$post->post_content = __wpg_text_filter($post->post_content);
			
			# @todo make function for translating $post object 	
			#$post = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($post);
		}
	}
	return $posts;
} // */