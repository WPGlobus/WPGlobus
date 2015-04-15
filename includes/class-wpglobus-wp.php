<?php
/**
 * WordPress shortcuts
 * @package WPGlobus
 */

/**
 * Class WPGlobus_WP
 */
class WPGlobus_WP {

	/**
	 * @return bool
	 */
	public static function is_doing_ajax() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX );
	}

	/**
	 * Attempt to check if an AJAX call was originated from admin screen.
	 * @todo There should be other actions. See $core_actions_get in admin-ajax.php
	 *       Can also check $GLOBALS['_SERVER']['HTTP_REFERER']
	 *       and $GLOBALS['current_screen']->in_admin()
	 * @return bool
	 */
	public static function is_admin_doing_ajax() {
		return (
			self::is_doing_ajax() &&
			(
				self::is_http_post_action( 'inline-save' ) ||
				self::is_http_post_action( 'save-widget' ) ||
				self::is_http_get_action( 'ajax-tag-search' )
			)
		);
	}


	/**
	 * @param string|string[] $page
	 *
	 * @return bool
	 */
	public static function is_pagenow( $page ) {
		/**
		 * Set in wp-includes/vars.php
		 * @global string $pagenow
		 */
		global $pagenow;

		return in_array( $pagenow, (array) $page );
	}

	/**
	 * To get the plugin page ID
	 * @example    On wp-admin/index.php?page=woothemes-helper, will return `woothemes-helper`.
	 *
	 * @param string|string[] $page
	 *
	 * @return bool
	 */
	public static function is_plugin_page( $page ) {
		/**
		 * Set in wp-admin/admin.php
		 * @global string $plugin_page
		 */
		global $plugin_page;

		return isset( $plugin_page ) && in_array( $plugin_page, (array) $page );
	}

	/**
	 * @param string|string[] $action
	 *
	 * @return bool
	 */
	public static function is_http_post_action( $action ) {

		$action = (array) $action;

		return ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $action ) );
	}

	/**
	 * @param string|string[] $action
	 *
	 * @return bool
	 */
	public static function is_http_get_action( $action ) {

		$action = (array) $action;

		return ( ! empty( $_GET['action'] ) && in_array( $_GET['action'], $action ) );
	}

	/**
	 * Check if a filter is called by a certain function / class
	 *
	 * @param string $function
	 * @param string $class
	 *
	 * @return bool
	 * @todo Unit test
	 * @todo What if we check class only?
	 * @todo Use the form class::method ?
	 */
	public static function is_filter_called_by( $function, $class = '' ) {
		if ( empty( $function ) ) {
			return false;
		}

		/**
		 * WP calls filters at level 4. This function adds one more level.
		 */
		$trace_level = 5;

		$callers = debug_backtrace();
		if ( empty( $callers[ $trace_level ] ) ) {
			return false;
		}

		/**
		 * First check: if function name matches
		 */
		$maybe = ( $callers[ $trace_level ]['function'] === $function );

		if ( $maybe ) {
			/**
			 * Now check if we also asked for a specific class, and it matches
			 */
			if ( ! empty( $class ) &&
			     ! empty( $callers[ $trace_level ]['class'] ) &&
			     $callers[ $trace_level ]['class'] !== $class
			) {
				$maybe = false;
			}
		}

		return $maybe;
	}

	/**
	 * True if I am in the Admin Panel, not doing AJAX
	 * @return bool
	 */
	public static function in_wp_admin() {
		return ( is_admin() && ! self::is_doing_ajax() );
	}

} // class

# --- EOF