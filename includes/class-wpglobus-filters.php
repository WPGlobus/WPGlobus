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

	/** @var string[] Meta keys where data can be multilingual  */
	protected static $multilingual_meta_keys = array();

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
	 * This filter is needed to display correctly the posts with the '--- MORE ---' separator
	 * in archives.
	 * Without it, the post content is truncated at the beginning of <!--more-->, thus keeping
	 * only the first language.
	 * *
	 * 'the_posts' filter is used by @see get_posts(), which is commonly used in all sorts of places,
	 * including, for instance, @see wp_get_associated_nav_menu_items while deleting a post.
	 * So, to minimize potential side effects, we limit the scope to main queries, or
	 * when the 'wpglobus_force_filter__the_posts' is set
	 * (@see WPGlobus_QA::_test_get_posts for example).
	 *
	 * @param array    $posts
	 * @param WP_Query $query
	 *
	 * @return array
	 * @since 1.0.14
	 */
	public static function filter__the_posts( $posts, &$query ) {

		if ( $query->is_main_query() || $query->get( 'wpglobus_force_filter__the_posts' ) ) {
			foreach ( $posts as $post ) {
				WPGlobus_Core::translate_wp_post( $post, WPGlobus::Config()->language,
					WPGlobus::RETURN_IN_DEFAULT_LANGUAGE );
			}
		}

		return $posts;

	}

	/**
	 * This is similar to the @see filter__text filter,
	 * but it returns text in the DEFAULT language.
	 *
	 * @param string $text
	 *
	 * @return string
	 * @since 1.0.8
	 */
	public static function filter__text_default_language( $text ) {

		return WPGlobus_Core::text_filter(
			$text,
			WPGlobus::Config()->default_language,
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

		/**
		 * @todo Example of a "stopper" filter
		 *       if ( apply_filters( 'wpglobus_do_filter__get_terms', true ) ) {}
		 *       Because it might affect the performance, this is a to-do for now.
		 */

		foreach ( $terms as &$_term ) {
			WPGlobus_Core::translate_term( $_term, WPGlobus::Config()->language );
		}
		unset( $_term );

		reset( $terms );

		return $terms;
	}

	/**
	 * Filter @see get_the_terms
	 * @scope admin
	 *
	 * @param object[]|WP_Error $terms List of attached terms, or WP_Error on failure.
	 *
	 * @return array
	 */
	public static function filter__get_the_terms( $terms ) {

		/**
		 * @internal 15.01.31
		 * Theoretically, we should not have this filter because @see get_the_terms
		 * calls @see wp_get_object_terms, which is already filtered.
		 * However, there is a case when the terms are retrieved from @see get_object_term_cache,
		 * and when we do a Quick Edit / inline-save, we ourselves write raw terms to the cache.
		 * As of now, we know only one such case, so we activate this filter only in admin,
		 * and only on the 'single_row' call
		 * @todo     Keep watching this
		 */

		if ( ! is_wp_error( $terms ) && WPGlobus_Utils::is_function_in_backtrace( 'single_row' ) ) {

			foreach ( $terms as &$_term ) {
				WPGlobus_Core::translate_term( $_term, WPGlobus::Config()->language );
			}
			unset( $_term );

			reset( $terms );
		}

		return $terms;
	}

	/**
	 * Filter @see wp_get_object_terms()
	 * @scope admin
	 * @scope front
	 *
	 * @param string[]|object[] $terms An array of terms for the given object or objects.
	 *
	 * @return array
	 */
	public static function filter__wp_get_object_terms( Array $terms ) {

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
		 * Don't filter term names for trash and un-trash single post
		 * @see we check post.php page instead of edit.php because redirect
		 */
		if ( is_admin() && WPGlobus_WP::is_pagenow( 'post.php' ) && isset( $_GET['action'] ) && ( 'trash' == $_GET['action'] || 'untrash' == $_GET['action'] )
		) {
			return $terms;
		}

		/**
		 * Don't filter term names bulk trash and untrash posts
		 */
		if ( is_admin() && WPGlobus_WP::is_pagenow( 'edit.php' ) && isset( $_GET['action'] ) && ( 'trash' == $_GET['action'] || 'untrash' == $_GET['action'] )
		) {
			return $terms;
		}

		/**
		 * Don't filter term names for bulk edit post from edit.php page
		 */
		if ( is_admin() && WPGlobus_Utils::is_function_in_backtrace( 'bulk_edit_posts' ) ) {
			return $terms;
		}

		/**
		 * Don't filter term names for inline-save ajax action from edit.php page
		 * @see wp_ajax_inline_save
		 * ...except when the same AJAX refreshes the table row @see WP_Posts_List_Table::single_row
		 * -
		 * @qa  At the "All posts" admin page, do Quick Edit on any post. After update, categories and tags
		 *     must not show multilingual strings with delimiters.
		 * @qa  At Quick Edit, enter an existing tag. After save, check if there is no additional tag
		 *     on the "Tags" page. If a new tag is created then the "is tag exists" check was checking
		 *     only a single language representation of the tag, while there is a multilingual tag in the DB.
		 */
		if ( WPGlobus_WP::is_http_post_action( 'inline-save' ) &&
		     WPGlobus_WP::is_pagenow( 'admin-ajax.php' )
		) {
			if ( ! WPGlobus_Utils::is_function_in_backtrace( 'single_row' ) ) {
				return $terms;
			}

		}

		/**
		 * Don't filter term names for heartbeat autosave
		 */
		if ( WPGlobus_WP::is_http_post_action( 'heartbeat' ) &&
		     WPGlobus_WP::is_pagenow( 'admin-ajax.php' ) &&
		     ! empty( $_POST['data']['wp_autosave'] )
		) {
			return $terms;
		}

		/**
		 * Don't filter term name at time generate checklist categories in metabox
		 */
		if ( is_admin() &&
		     WPGlobus_WP::is_pagenow( 'post.php' ) &&
		     empty( $_POST ) &&
		     WPGlobus_Utils::is_function_in_backtrace( 'wp_terms_checklist' )
		) {
			return $terms;
		}

		foreach ( $terms as &$_term ) {
			WPGlobus_Core::translate_term( $_term, WPGlobus::Config()->language );
		}
		unset( $_term );

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

		if (
			WPGlobus_WP::is_filter_called_by( 'get_sample_permalink' ) ||
			WPGlobus_WP::is_filter_called_by( 'wp_insert_post' ) ||
			WPGlobus_WP::is_filter_called_by( 'wp_update_term' )
		) {
			/**
			 * @internal_note: the DEFAULT language, not the current one
			 */
			$title = WPGlobus_Core::text_filter(
				$title, WPGlobus::Config()->default_language );
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
				array(
					'heartbeat',
					'sample-permalink',
					'add-menu-item',
				)
			)
			) {
				$need_to_process = false;
			}
		}

		if ( $need_to_process ) {
			$url = WPGlobus_Utils::localize_url( $url );
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

		foreach ( $pages as &$_page ) {
			WPGlobus_Core::translate_wp_post( $_page, WPGlobus::Config()->language );
		}
		unset( $_page );

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

		/**
		 * Special case: in admin area, show everything in the language of admin interface.
		 * (set in the General Settings in WP 4.1)
		 */
		/**
		 * @internal
		 * We need to exclude is_admin when it's a front-originated AJAX,
		 * so we are doing a "hack" checking @see WPGlobus_WP::is_admin_doing_ajax.
		 */
		if (
			is_admin() &&
			( ! WPGlobus_WP::is_doing_ajax() || WPGlobus_WP::is_admin_doing_ajax() )
		) {
			/**
			 * @todo is_multisite
			 * @todo Pre-WP4, WPLANG constant from wp-config
			 */
			$WPLANG = get_option( 'WPLANG' );
			if ( empty( $WPLANG ) ) {
				$WPLANG = 'en_US';
			}
			WPGlobus::Config()->set_language( $WPLANG );

		}

		if ( is_admin() ) {
			/**
			 * Checking case for set locale which does not set in WPGlobus
			 */
			if ( WPGlobus::Config()->is_enabled_locale( $locale ) ) {
				$locale = WPGlobus::Config()->locale[ WPGlobus::Config()->language ];
			}
		} else {
			$locale = WPGlobus::Config()->locale[ WPGlobus::Config()->language ];
		}

		return $locale;

	}

	/**
	 * Filter @see wp_setup_nav_menu_item in wp-includes\nav-menu.php for more info
	 * @since 1.0.0
	 *
	 * @param WP_Post[] $object
	 *
	 * @return WP_Post[]
	 */
	public static function filter__nav_menu_item( $object ) {
		/**
		 * This filter is used at nav-menus.php page for .field-move elements
		 */
		if ( is_object( $object ) && 'WP_Post' == get_class( $object ) ) {

			if ( ! empty( $object->title ) ) {
				$object->title = WPGlobus_Core::text_filter( $object->title, WPGlobus::Config()->language );
			}
			if ( ! empty( $object->description ) ) {
				$object->description = WPGlobus_Core::text_filter( $object->description, WPGlobus::Config()->language );
			}

		}

		return $object;
	}

	/**
	 * Filter @see nav_menu_description
	 * @since 1.0.0
	 *
	 * @param string $description
	 *
	 * @return string
	 */
	public static function filter__nav_menu_description( $description ) {
		/**
		 * This filter for translate menu item description
		 */
		if ( ! empty( $description ) ) {
			$description = WPGlobus_Core::text_filter( $description, WPGlobus::Config()->language );
		}

		return $description;
	}

	/**
	 * Filter @see heartbeat_received
	 * @since 1.0.1
	 *
	 * @param array  $response
	 * @param array  $data
	 * @param string $screen_id
	 *
	 * @return array
	 */
	public static function filter__heartbeat_received(
		$response, $data,
		/** @noinspection PhpUnusedParameterInspection */
		$screen_id
	) {

		if ( false !== strpos( $_SERVER['HTTP_REFERER'], 'wpglobus=off' ) ) {
			/**
			 * Check $_SERVER['HTTP_REFERER'] for wpglobus toggle is off because wpglobus-admin.js doesn't loaded in this mode
			 */
			return $response;
		}

		if ( ! empty( $data['wp_autosave'] ) ) {

			if ( empty( $data['wp_autosave']['post_id'] ) || (int) $data['wp_autosave']['post_id'] == 0 ) {
				/**
				 * wp_autosave may come from edit.php page
				 */
				return $response;
			}

			if ( empty( $data['wpglobus_heartbeat'] ) ) {
				/**
				 * Check for wpglobus key
				 */
				return $response;
			}

			$title_wrap     = false;
			$content_wrap   = false;
			$post_title_ext = '';
			$content_ext    = '';

			foreach ( WPGlobus::Config()->enabled_languages as $language ) {
				if ( $language == WPGlobus::Config()->default_language ) {

					$post_title_ext .= WPGlobus::add_locale_marks( $data['wp_autosave']['post_title'], $language );
					$content_ext .= WPGlobus::add_locale_marks( $data['wp_autosave']['content'], $language );

				} else {

					if ( ! empty( $data['wp_autosave'][ 'post_title_' . $language ] ) ) {
						$title_wrap = true;
						$post_title_ext .= WPGlobus::add_locale_marks( $data['wp_autosave'][ 'post_title_' . $language ], $language );
					}

					if ( ! empty( $data['wp_autosave'][ 'content_' . $language ] ) ) {
						$content_wrap = true;
						$content_ext .= WPGlobus::add_locale_marks( $data['wp_autosave'][ 'content_' . $language ], $language );
					}

				}
			}

			if ( $title_wrap ) {
				$data['wp_autosave']['post_title'] = $post_title_ext;
			}

			if ( $content_wrap ) {
				$data['wp_autosave']['content'] = $content_ext;
			}

			/**
			 * Filter before autosave
			 * @since 1.0.2
			 *
			 * @param array $data ['wp_autosave'] Array of post data.
			 */
			$data['wp_autosave'] = apply_filters( 'wpglobus_autosave_post_data', $data['wp_autosave'] );

			$saved = wp_autosave( $data['wp_autosave'] );

			if ( is_wp_error( $saved ) ) {
				$response['wp_autosave'] = array( 'success' => false, 'message' => $saved->get_error_message() );
			} elseif ( empty( $saved ) ) {
				$response['wp_autosave'] = array( 'success' => false, 'message' => __( 'Error while saving.' ) );
			} else {
				/* translators: draft saved date format, see http://php.net/date */
				$draft_saved_date_format = __( 'g:i:s a' );
				/* translators: %s: date and time */
				$response['wp_autosave'] = array(
					'success' => true,
					'message' => sprintf( __( 'Draft saved at %s.' ), date_i18n( $draft_saved_date_format ) )
				);
			}

		}

		return $response;
	}

	/**
	 * Filter @see wp_nav_menu_objects
	 * @since 1.0.2
	 *
	 * @param array $object
	 *
	 * @return array
	 */
	public static function filter__nav_menu_objects( $object ) {

		if ( is_array( $object ) ) {
			foreach ( $object as &$post ) {
				if ( ! empty( $post->attr_title ) ) {
					$post->attr_title = WPGlobus_Core::text_filter( $post->attr_title, WPGlobus::Config()->language );
				}
			}
		}

		return $object;

	}

	/**
	 * Translate widget strings (besides the title handled by the `widget_title` filter)
	 * @see   WP_Widget::display_callback
	 * @scope front
	 *
	 * @param string[] $instance
	 *
	 * @return string[]
	 * @since 1.0.6
	 */
	public static function filter__widget_display_callback( $instance ) {

		foreach ( $instance as &$widget_setting ) {

			/** @noinspection ReferenceMismatchInspection */
			if ( ! empty( $widget_setting ) && is_string( $widget_setting ) ) {
				$widget_setting =
					WPGlobus_Core::text_filter( $widget_setting, WPGlobus::Config()->language );
			}
		}

		return $instance;
	}

	/**
	 * Filter @see comment_moderation_text,
	 * @see   comment_moderation_subject
	 * @since 1.0.6
	 *
	 * @param string $text
	 * @param int    $comment_id
	 *
	 * @return string
	 */
	public static function filter__comment_moderation( $text, $comment_id ) {

		$comment = get_comment( $comment_id );
		$post    = get_post( $comment->comment_post_ID );
		$title   = WPGlobus_Core::text_filter( $post->post_title, WPGlobus::Config()->language );

		return str_replace( $post->post_title, $title, $text );

	}

	/**
	 * Filter @see wp_trim_words
	 * @qa    At the /wp-admin/index.php page is a Quick Draft metabox
	 *      which shows 3 last post drafts. This filter lets post content in default language.
	 * @since 1.0.14
	 *
	 * @param string $text          The trimmed text.
	 * @param int    $num_words     The number of words to trim the text to.
	 * @param string $more          An optional string to append to the end of the trimmed text, e.g. &hellip;.
	 * @param string $original_text The text before it was trimmed.
	 *
	 * @return string
	 */
	public static function filter__wp_trim_words(
		/** @noinspection PhpUnusedParameterInspection */
		$text, $num_words, $more, $original_text
	) {

		$text = WPGlobus_Core::text_filter( $original_text, WPGlobus::Config()->language );

		if ( null === $more ) {
			$more = __( '&hellip;' );
		}

		$text = wp_strip_all_tags( $text );
		/* translators: If your word count is based on single characters (East Asian characters),
		   enter 'characters'. Otherwise, enter 'words'. Do not translate into your own language. */
		if ( 'characters' == _x( 'words', 'word count: words or characters?' ) && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
			$text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
			preg_match_all( '/./u', $text, $words_array );
			$words_array = array_slice( $words_array[0], 0, $num_words + 1 );
			$sep         = '';
		} else {
			$words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
			$sep         = ' ';
		}
		if ( count( $words_array ) > $num_words ) {
			array_pop( $words_array );
			$text = implode( $sep, $words_array );
			$text = $text . $more;
		} else {
			$text = implode( $sep, $words_array );
		}

		return $text;

	}

	/**
	 * Register the WPGlobus widgets
	 * @wp-hook widgets_init
	 * @since   1.0.7
	 */
	public static function register_widgets() {
		register_widget( 'WPGlobusWidget' );
	}

	/**
	 * Do something on admin_init hook.
	 * @todo Note: runs on admin-ajax and admin-post, too
	 */
	public static function action__admin_init() {
		/**
		 * Display blog name correctly on the WooThemes Helper page
		 * wp-admin/index.php?page=woothemes-helper
		 */
		if ( WPGlobus_WP::is_plugin_page( 'woothemes-helper' ) ) {
			add_filter( 'option_blogname', array( 'WPGlobus_Filters', 'filter__text' ), 0 );
		}
	}

	/**
	 * Specify meta keys where the meta data can be multilingual
	 * @example
	 * <code>
	 *  add_filter( 'wpglobus_multilingual_meta_keys',
	 *      function ( $multilingual_meta_keys ) {
	 *          $multilingual_meta_keys['slides'] = true;
	 *          return $multilingual_meta_keys;
	 *      }
	 *  );
	 * </code>
	 * @since 1.2.1
	 */
	public static function set_multilingual_meta_keys() {
		self::$multilingual_meta_keys = apply_filters(
			'wpglobus_multilingual_meta_keys', self::$multilingual_meta_keys
		);
	}

	/**
	 * Translate meta data
	 * @see \WPGlobus_Filters::set_multilingual_meta_keys
	 *
	 * @param string|array $value     Null is passed. We set the value.
	 * @param int          $object_id Post ID
	 * @param string       $meta_key  Passed by the filter. We need only one key.
	 * @param string|array $single    Meta value, or an array of values.
	 *
	 * @return string|array
	 */
	public static function filter__postmeta( $value, $object_id, $meta_key, $single ) {

		/**
		 * @todo Currently, only single values are supported
		 */
		if ( ! $single ) {
			return $value;
		}

		/**
		 * Will process only if the `meta_key` is one of the explicitly set.
		 */
		if ( ! isset( self::$multilingual_meta_keys[ $meta_key ] ) ) {
			return $value;
		}

		/**
		 * May be called many times on one page. Let's cache.
		 */
		static $_cache;
		if ( isset( $_cache[ $meta_key ][ $object_id ] ) ) {
			return $_cache[ $meta_key ][ $object_id ];
		}

		/** @global wpdb $wpdb */
		global $wpdb;
		$meta_value = $wpdb->get_var( $wpdb->prepare(
			"SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s AND post_id = %d LIMIT 1;",
			$meta_key, $object_id ) );

		if ( $meta_value ) {

			if ( is_serialized( $meta_value ) ) {
				/**
				 * @todo Refactor this. Write a `filter__array` method.
				 */
				$_meta_array = unserialize( $meta_value );
				foreach ( $_meta_array as &$_value ) {
					if ( is_array( $_value ) ) {
						foreach ( $_value as &$_deep_value ) {
							/**
							 * @todo Assuming that the array had max. two levels, which is wrong.
							 */
							$_deep_value = WPGlobus_Filters::filter__text( $_deep_value );
						}
						unset( $_deep_value );
					} else {
						$_value = WPGlobus_Filters::filter__text( $_value );
					}
				}
				unset( $_value );
				$value = $_meta_array;

				/**
				 * If single is requested, the following code is executed by
				 * @see get_metadata
				 * <code>
				 * if ( $single && is_array( $check ) )
				 *      return $check[0];
				 * </code>
				 * Therefore, we need to return the entire `$value` as the first element of
				 * an array.
				 */
				if ( $single ) {
					$value = array( $value );
				}

			} else {
				$value = WPGlobus_Filters::filter__text( $meta_value );
			}
		}

		/**
		 * Save to cache, even if we did not do anything
		 */
		$_cache[ $meta_key ][ $object_id ] = $value;

		return $value;
	}

	/**
	 * Localize feed url
	 * @since 1.5.3
	 *
	 * @scope front
	 */
	public static function fetch_feed_options( $obj ) {

		/**
		 * Filter to disable localize feed url.
		 * @since 1.5.3
		 *
		 * @param boolean			True is value by default.
		 * @param SimplePie object  $obj.
		 * @return boolean
		 */
		if ( apply_filters( 'wpglobus_localize_feed_url', true, $obj ) ) {
			if ( ! empty( $obj->feed_url ) ) {
				$obj->feed_url = WPGlobus_Utils::localize_url( $obj->feed_url );
			}

		}
	}


} // class

# --- EOF
