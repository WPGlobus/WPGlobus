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
	 * Filter @see wp_get_object_terms()
	 * @scope admin
	 * @scope front
	 *
	 * @param string[]|object[] $terms
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
		/**
		 * Don't filter tag names for inline-save ajax action from edit.php page
		 */
		if ( 'admin-ajax.php' == $pagenow && ! empty( $_POST['action'] ) && 'inline-save' == $_POST['action'] ) {
			return $terms;
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

} // class

# --- EOF