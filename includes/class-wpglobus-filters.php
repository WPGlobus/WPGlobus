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

} // class

# --- EOF