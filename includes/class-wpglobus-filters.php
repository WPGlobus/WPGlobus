<?php
/**
 * Filters and actions
 * Only methods here. The add_filter calls are in the Controller
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Filters
 */
class WPGlobus_Filters {

	/**
	 * This is the basic filter used to extract the text portion in the current language from a string.
	 * Applied to the main WP texts, such as post title, content and excerpt.
	 *
	 * @param string $text
	 *
	 * @return string
	 */
	public static function filter__text( $text ) {

		return WPGlobus_Core::text_filter(
			$text,
			WPGlobus::Config()->language,
			null,
			WPGlobus::Config()->default_language
		);

	}


	/**
	 * Filter @see get_terms
	 * @scope admin
	 * @scope front
	 *
	 * @param string[]|object[] $terms
	 *
	 * @return array
	 */
	public static function filter__get_terms( Array $terms ) {

		foreach ( $terms as &$term ) {
			WPGlobus_Core::translate_term( $term, WPGlobus::Config()->language );
		}

		reset( $terms );

		return $terms;
	}

	/**
	 * Filter @see get_the_terms
	 *
	 * @param object[]|WP_Error $terms List of attached terms, or WP_Error on failure.
	 *
	 * @return array
	 */
	public static function filter__get_the_terms( $terms ) {

		if ( WPGlobus_WP::is_http_post_action( 'inline-save' ) && WPGlobus_WP::is_pagenow( 'admin-ajax.php' ) ) {
			return $terms;
		}

		if ( ! is_wp_error( $terms ) ) {

			foreach ( $terms as &$term ) {
				WPGlobus_Core::translate_term( $term, WPGlobus::Config()->language );
			}

			reset( $terms );
		}

		return $terms;
	}

	/**
	 * Filter @see wp_get_object_terms()
	 * @scope admin
	 * @scope front
	 *
	 * @param string[]|object[] $terms      An array of terms for the given object or objects.
	 * @param int[]|int         $object_ids Object ID or array of IDs.
	 * @param string[]|string   $taxonomies A taxonomy or array of taxonomies.
	 * @param array             $args       An array of arguments for retrieving terms for
	 *                                      the given object(s).
	 *
	 * @return array
	 */
	public static function filter__wp_get_object_terms( Array $terms, $object_ids, $taxonomies, $args ) {
		//		return apply_filters( 'wp_get_object_terms', $terms, $object_ids, $taxonomies, $args );
		/**
		 * @internal
		 * Do not need to check for is_wp_error($terms),
		 * because the WP_Error is returned by wp_get_object_terms() before applying filter.
		 * Do not need to check for empty($terms) because foreach won't loop.
		 */

		/**
		 * Don't filter term names when saving or publishing posts
		 * @todo Check this before add_filter and not here
		 * @todo Describe exactly how to check this visually, and is possible - write the acceptance test
		 */
		if (
			is_admin() &&
			WPGlobus_WP::is_pagenow( 'post.php' ) &&
			( ! empty( $_POST['save'] ) || ! empty( $_POST['publish'] ) )
		) {
			return $terms;
		}

		/**
		 * Don't filter tag names for inline-save ajax action from edit.php page
		 * @see wp_ajax_inline_save
		 * when called @see edit_post to save
		 * but OK to filter when the same AJAX refreshes the table row
		 */
		if ( WPGlobus_WP::is_http_post_action( 'inline-save' ) && WPGlobus_WP::is_pagenow( 'admin-ajax.php' ) ) {
			$callers = debug_backtrace();
			$_c      = [ ];
			$_cs     = '';
			foreach ( $callers as $_ ) {
				$_c[] = $_['function'];
				$_cs .= $_['function'] . "\n";
			}
			unset( $_ );
			if ( in_array( 'single_row', $_c ) ) {
				$a = 'a';
			} else {
				return $terms;
			}
			//			if(in_array('edit_post', $_c)){
			//				$a='a';
			////				return $terms;
			//			}
			//			if(in_array('get_post', $_c)){
			//				$a='a';
			////				return $terms;
			//			}
			////			if (
			////				( ! empty( $callers[10] ) && $callers[10]['function'] === 'wp_ajax_inline_save' )
			////				and
			////				( ! empty( $callers[9] ) && $callers[9]['function'] === 'edit_post' )
			////			) {
			//			$a='a';
			//				return $terms;
			////			}


		}

		foreach ( $terms as &$term ) {
			WPGlobus_Core::translate_term( $term, WPGlobus::Config()->language );
		}

		reset( $terms );

		return $terms;
	}

	/**
	 * This filter is needed to build correct permalink (slug, post_name)
	 * using only the main part of the post title (in the default language).
	 * -
	 * Because 'sanitize_title' is a commonly used function, we have to apply our filter
	 * only on very specific calls. Therefore, there are (ugly) debug_backtrace checks.
	 * -
	 * Case 1
	 * When a draft post is created,
	 * the post title is converted to the slug in the @see get_sample_permalink function,
	 * using the 'sanitize_title' filter.
	 * -
	 * Case 2
	 * When the draft is published, @see wp_insert_post calls
	 * @see               sanitize_title to set the slug
	 * -
	 * @see               WPGLobus_QA::_test_post_name
	 * -
	 * @see               WPSEO_Metabox::localize_script
	 * @todo              Check what's going on in localize_script of WPSEO?
	 * @todo              What if there is no EN language? Only ru and kz but - we cannot use 'en' for permalink
	 * @todo              check guid
	 *
	 * @param string $title
	 *
	 * @return string
	 */
	public static function filter__sanitize_title( $title ) {

		$ok_to_filter = false;

		$callers = debug_backtrace();
		if ( isset( $callers[4]['function'] ) ) {
			if ( $callers[4]['function'] === 'get_sample_permalink' ) {
				/**
				 * Case 1
				 */
				$ok_to_filter = true;
			} elseif (
				/**
				 * Case 2
				 */
				$callers[4]['function'] === 'wp_insert_post'
				/** @todo This is probably not required. Keeping it until stable version */
				// and ( isset( $callers[5]['function'] ) and $callers[5]['function'] === 'wp_update_post' )
			) {
				$ok_to_filter = true;
			}

		}

		if ( $ok_to_filter ) {
			/**
			 * @internal Note: the DEFAULT language, not the current one
			 */
			$title = WPGlobus_Core::text_filter( $title, WPGlobus::Config()->default_language );
		}

		return $title;
	}

	/**
	 * Filter @see get_term()
	 *
	 * @param string|object $term
	 *
	 * @return string|object
	 */
	public static function filter__get_term( $term ) {

		if ( WPGlobus_WP::is_http_post_action( 'inline-save-tax' ) ) {
			/**
			 * Don't filter ajax action 'inline-save-tax' from edit-tags.php page.
			 * See quick_edit() in includes/js/wpglobus.admin.js
			 * for and example of working with taxonomy name and description
			 * wp_current_filter contains
			 * 0=wp_ajax_inline-save-tax
			 * 1=get_term
			 * @see wp_ajax_inline_save_tax()
			 */
			// do nothing
		} else {
			WPGlobus_Core::translate_term( $term, WPGlobus::Config()->language );
		}

		return $term;

	}

	/**
	 * Localize home_url
	 * Should be processed on:
	 * - front
	 * - AJAX, except for several specific actions
	 *
	 * @param string $url
	 *
	 * @return string
	 */
	public static function filter__home_url( $url ) {

		/**
		 * @internal note
		 * Example of URL in admin:
		 * When admin interface is not in default language, we still should not see
		 * any permalinks with language prefixes.
		 * For that, we could check if we are at the 'post.php' screen:
		 * if ( 'post.php' == $pagenow ) ....
		 * However, we do not need it, because we disallowed almost any processing in admin.
		 */

		/**
		 * 1. Do not work in admin
		 */
		$need_to_process = ( ! is_admin() );

		if ( WPGlobus_WP::is_pagenow( 'admin-ajax.php' ) ) {
			/**
			 * 2. But work in AJAX, which is also admin
			 */
			$need_to_process = true;

			/**
			 * 3. However, don't convert url for these AJAX actions:
			 */
			if ( WPGlobus_WP::is_http_post_action(
				[
					'heartbeat',
					'sample-permalink',
					'add-menu-item',
				]
			)
			) {
				$need_to_process = false;
			}
		}

		if ( $need_to_process ) {
			$url = WPGlobus_Utils::get_convert_url( $url );
		}

		return $url;
	}

	/**
	 * Filter @see get_pages
	 * @qa See a list of available pages in the "Parent Page" metabox when editing a page.
	 *
	 * @param WP_Post[] $pages
	 *
	 * @return WP_Post[]
	 */
	public static function filter__get_pages( $pages ) {

		foreach ( $pages as &$page ) {
			WPGlobus_Core::translate_wp_post( $page, WPGlobus::Config()->language );
		}

		reset( $pages );

		return $pages;
	}

	/**
	 * Filter for @see get_locale
	 *
	 * @param string $locale
	 *
	 * @return string
	 * @todo    Do we need to do setlocale(LC_???, $locale)? (*** NOT HERE )
	 * @see     setlocale
	 * @link    http://php.net/manual/en/function.setlocale.php
	 * @example echo setlocale(LC_ALL, 'Russian'); => Russian_Russia.1251
	 */
	public static function filter__get_locale(
		/** @noinspection PhpUnusedParameterInspection */
		$locale
	) {

		if ( is_admin() && ! WPGlobus_WP::is_doing_ajax() ) {
			/**
			 * If in admin, set the language according to the WPLANG option.
			 * @todo is_multisite
			 * @todo Pre-WP4, WPLANG constant from wp-config
			 */
			$WPLANG = get_option( 'WPLANG' );
			if ( empty( $WPLANG ) ) {
				$WPLANG = 'en_US';
			}
			WPGlobus::Config()->set_language( $WPLANG );

		}

		$locale = WPGlobus::Config()->locale[ WPGlobus::Config()->language ];

		return $locale;

	}

	/**
	 * To translate Yoast columns
	 * @see   WPSEO_Metabox::column_content
	 * @scope admin
	 *
	 * @param string $text
	 *
	 * @return string
	 * @todo  Check pull request
	 * https://github.com/Yoast/wordpress-seo/pull/1946
	 */
	public static function filter__wpseo_columns( $text ) {

		if ( WPGlobus_WP::is_filter_called_by( 'column_content', 'WPSEO_Metabox' ) ) {

			$text = WPGlobus_Core::text_filter(
				$text,
				WPGlobus::Config()->language,
				null,
				WPGlobus::Config()->default_language
			);
		}

		return $text;
	}

	/**
	 * @todo To discuss
	 */
	public static function action__init_url_info() {

		// check if it isn't already initialized
		if ( defined( 'WPGLOBUS_INIT' ) ) {
			return;
		}

		define( 'WPGLOBUS_INIT', true );

		/** @todo check at class-wpglobus.php:103 for set url_info */
		WPGlobus::Config()->url_info =
			WPGlobus_Utils::extract_url( $_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '' );


		$_SERVER['REQUEST_URI'] = WPGlobus::Config()->url_info['url'];
		$_SERVER['HTTP_HOST']   = WPGlobus::Config()->url_info['host'];

	}



} // class

# --- EOF