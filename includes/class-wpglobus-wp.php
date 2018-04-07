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
	 * CSS classes for admin notices
	 * @example
	 * <code>
	 *  echo '<div class="notice ' . WPGlobus_WP::ADMIN_NOTICE_WARNING . '">';
	 * </code>
	 */

	const ADMIN_NOTICE_SUCCESS = 'notice-success';
	const ADMIN_NOTICE_ERROR = 'notice-error';
	const ADMIN_NOTICE_INFO = 'notice-info';
	const ADMIN_NOTICE_WARNING = 'notice-warning';

	/**
	 * Check if doing AJAX call.
	 *
	 * @return bool
	 * @since 1.9.13 - also checks for WC AJAX.
	 */
	public static function is_doing_ajax() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || self::is_doing_wc_ajax();
	}

	/**
	 * Check if doing WooCommerce AJAX call.
	 *
	 * @return bool
	 * @since 1.9.13
	 */
	public static function is_doing_wc_ajax() {
		return ( ! empty( $_GET['wc-ajax'] ) );
	}

	/**
	 * Attempt to check if an AJAX call was originated from admin screen.
	 * @todo There should be other actions. See $core_actions_get in admin-ajax.php
	 *       Can also check $GLOBALS['_SERVER']['HTTP_REFERER']
	 *       and $GLOBALS['current_screen']->in_admin()
	 *
	 * @todo add $action parameter for case to check for it only
	 * @return bool
	 */
	public static function is_admin_doing_ajax() {
		return (
			self::is_doing_ajax() &&
			(
				self::is_http_post_action( 'inline-save' ) ||
				self::is_http_post_action( 'save-widget' ) ||
				self::is_http_post_action( 'customize_save' ) ||
				self::is_http_get_action( 'ajax-tag-search' )
			)
		);
	}


	/**
	 * To get the current admin page
	 * (Set in wp-includes/vars.php)
	 * @return string $page
	 * @since 1.2.0
	 */
	public static function pagenow() {
		/**
		 * @global string $pagenow
		 */
		global $pagenow;

		return ( isset( $pagenow ) ? $pagenow : '' );
	}

	/**
	 * @param string|string[] $page
	 *
	 * @return bool
	 */
	public static function is_pagenow( $page ) {
		return in_array( self::pagenow(), (array) $page );
	}

	/**
	 * To get the plugin page ID
	 * @example    On wp-admin/index.php?page=woothemes-helper, will return `woothemes-helper`.
	 * @return string
	 * @since      1.2.0
	 */
	public static function plugin_page() {
		/**
		 * Set in wp-admin/admin.php
		 * @global string $plugin_page
		 */
		global $plugin_page;

		return ( isset( $plugin_page ) ? $plugin_page : '' );
	}

	/**
	 * @param string|string[] $page
	 *
	 * @return bool
	 */
	public static function is_plugin_page( $page ) {
		return in_array( self::plugin_page(), (array) $page );
	}

	/**
	 * @param string|string[] $action
	 *
	 * @return bool
	 */
	public static function is_http_post_action( $action ) {

		$action = (array) $action;

		return ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $action, true ) ); // WPCS: input var ok, sanitization ok.
	}

	/**
	 * @param string|string[] $action
	 *
	 * @return bool
	 */
	public static function is_http_get_action( $action ) {

		$action = (array) $action;

		return ( ! empty( $_GET['action'] ) && in_array( $_GET['action'], $action, true ) ); // WPCS: input var ok, sanitization ok.
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
	 * @todo Check multiple functions and classes (array)
	 */
	public static function is_filter_called_by( $function, $class = '' ) {
		if ( empty( $function ) ) {
			return false;
		}

		/**
		 * WP calls filters at level 4. This function adds one more level.
		 */
		$trace_level = 5;

		if ( version_compare( $GLOBALS['wp_version'], '4.6.999', '>' ) ) {
			/**
			 * Starting with WordPress 4.7, WP_Hook adds one more level.
			 * @since 1.7.0
			 */
			$trace_level = 6;
		}

		if ( version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
			/**
			 * In PHP 7, `call_user_func_array` no longer appears in the trace
			 * as a separate call.
			 * @since 1.5.4
			 */
			$trace_level --;
		}

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
	 * Check if was called by a specific function (could be any levels deep).
	 *
	 * @param callable|string $method Function name or array(class,function).
	 *
	 * @return bool True if Function is in backtrace.
	 */
	public static function is_function_in_backtrace( $method ) {
		$function_in_backtrace = false;

		// Parse callable into class and function.
		if ( is_string( $method ) ) {
			$function_name = $method;
			$class_name    = '';
		} elseif ( is_array( $method ) && isset( $method[0], $method[1] ) ) {
			list( $class_name, $function_name ) = $method;
		} else {
			return false;
		}

		// Traverse backtrace and stop if the callable is found there.
		foreach ( debug_backtrace() as $_ ) {
			if ( isset( $_['function'] ) && $_['function'] === $function_name ) {
				$function_in_backtrace = true;
				if ( $class_name && isset( $_['class'] ) && $_['class'] !== $class_name ) {
					$function_in_backtrace = false;
				}
				if ( $function_in_backtrace ) {
					break;
				}
			}
		}

		return $function_in_backtrace;
	}

	/**
	 * To call @see is_function_in_backtrace with the array of parameters.
	 *
	 * @param callable[] $callables Array of callables.
	 *
	 * @return bool True if any of the pair is found in the backtrace.
	 */
	public static function is_functions_in_backtrace( Array $callables ) {
		foreach ( $callables as $callable ) {
			if ( self::is_function_in_backtrace( $callable ) ) {
				return true;
			}
		}

		return false;
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
