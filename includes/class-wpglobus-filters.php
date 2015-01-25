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

		/** @global WPGlobus_Config $WPGlobus_Config */
		global $WPGlobus_Config;

		foreach ( $terms as &$term ) {
			WPGlobus_Core::translate_term( $term, $WPGlobus_Config->language );
		}

		reset( $terms );

		return $terms;
	}

} // class

# --- EOF