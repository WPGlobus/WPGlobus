<?php
/**
 * File: class-wpglobus-wp.php
 *
 * WordPress shortcuts.
 *
 * @package      WPGlobus
 * @noinspection PhpUnused
 */

/**
 * Class WPGlobus_WP
 */
class WPGlobus_WP {

	/**
	 * CSS classes for admin notices
	 *
	 * @example
	 * <code>
	 *  echo '<div class="notice ' . WPGlobus_WP::ADMIN_NOTICE_WARNING . '">';
	 * </code>
	 */

	const ADMIN_NOTICE_SUCCESS = 'notice-success';
	const ADMIN_NOTICE_ERROR   = 'notice-error';
	const ADMIN_NOTICE_INFO    = 'notice-info';
	const ADMIN_NOTICE_WARNING = 'notice-warning';

	/**
	 * Check if doing AJAX call.
	 *
	 * @since 1.9.13 - also checks for WC AJAX.
	 * @return bool
	 */
	public static function is_doing_ajax() {
		return ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || self::is_doing_wc_ajax();
	}

	/**
	 * Check if doing WooCommerce AJAX call.
	 *
	 * @since 1.9.13
	 * @return bool
	 */
	public static function is_doing_wc_ajax() {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return ( ! empty( $_GET['wc-ajax'] ) );
	}

	/**
	 * Attempt to check if an AJAX call was originated from admin screen.
	 *
	 * @return bool
	 * @todo add $action parameter for case to check for it only
	 * @todo There should be other actions. See $core_actions_get in admin-ajax.php
	 *       Can also check $GLOBALS['_SERVER']['HTTP_REFERER']
	 *       and $GLOBALS['current_screen']->in_admin()
	 */
	public static function is_admin_doing_ajax() {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return (
			self::is_doing_ajax() &&
			(
				self::is_http_post_action( 'inline-save' ) ||
				self::is_http_post_action( 'save-widget' ) ||
				self::is_http_post_action( 'customize_save' ) ||
				self::is_http_get_action( 'ajax-tag-search' ) ||
				(
					self::is_http_post_action( 'WPGlobus_process_ajax' )
					&& isset( $_POST['order']['action'] )
					&& 'wpglobus_select_lang' === $_POST['order']['action']
				)
			)
		);
	}

	/**
	 * To get the current admin page
	 * (Set in wp-includes/vars.php)
	 *
	 * @since 1.2.0
	 * @return string $page
	 */
	public static function pagenow() {
		/**
		 * Global.
		 *
		 * @global string $pagenow
		 */
		global $pagenow;

		return ( isset( $pagenow ) ? $pagenow : '' );
	}

	/**
	 * Is pagenow = $page?
	 *
	 * @param string|string[] $page Page
	 *
	 * @return bool
	 */
	public static function is_pagenow( $page ) {
		return in_array( self::pagenow(), (array) $page, true );
	}

	/**
	 * To get the plugin page ID
	 *
	 * @since      1.2.0
	 * @return string
	 * @example    On wp-admin/index.php?page=woothemes-helper, will return `woothemes-helper`.
	 */
	public static function plugin_page() {
		/**
		 * Set in wp-admin/admin.php
		 *
		 * @global string $plugin_page
		 */
		global $plugin_page;

		return ( isset( $plugin_page ) ? $plugin_page : '' );
	}

	/**
	 * Is plugin_page = $page?
	 *
	 * @param string|string[] $page Page.
	 *
	 * @return bool
	 */
	public static function is_plugin_page( $page ) {
		return in_array( self::plugin_page(), (array) $page, true );
	}

	/**
	 * Is http_post_action == $action?
	 *
	 * @param string|string[] $action Action.
	 *
	 * @return bool
	 */
	public static function is_http_post_action( $action ) {

		$action = (array) $action;

		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $action, true ) );
	}

	/**
	 * Is http_get_action == $action?
	 *
	 * @param string|string[] $action Action.
	 *
	 * @return bool
	 */
	public static function is_http_get_action( $action ) {

		$action = (array) $action;

		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return ( ! empty( $_GET['action'] ) && in_array( $_GET['action'], $action, true ) );
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
			 *
			 * @since 1.7.0
			 */
			$trace_level = 6;
		}

		if ( version_compare( PHP_VERSION, '7.0.0', '>=' ) ) {
			/**
			 * In PHP 7, `call_user_func_array` no longer appears in the trace
			 * as a separate call.
			 *
			 * @since 1.5.4
			 */
			$trace_level --;
		}

		$fn      = 'debug_backtrace';
		$callers = $fn();
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
		$fn = 'debug_backtrace';
		foreach ( $fn() as $_ ) {
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
	public static function is_functions_in_backtrace( array $callables ) {
		foreach ( $callables as $callable ) {
			if ( self::is_function_in_backtrace( $callable ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * True if I am in the Admin Panel, not doing AJAX
	 *
	 * @return bool
	 */
	public static function in_wp_admin() {
		return ( is_admin() && ! self::is_doing_ajax() );
	}

	/**
	 * True if doing REST API request.
	 *
	 * @since 2.8.9
	 *
	 * @return bool
	 */
	public static function is_rest_api_request() {

		if ( self::is_doing_ajax() ) {
			return false;
		}

		/**
		 * See wp-includes\rest-api.php
		 * See wp-includes\load.php
		 */
		if ( defined( 'REST_REQUEST' ) || wp_is_json_request() ) {
			return true;
		}

		return false;
	}

	/**
	 * Get sanitized_value from array.
	 *
	 * @since 2.12.1
	 *
	 * @param array  $array The array.
	 * @param string $key   The key.
	 *
	 * @return array|string The value.
	 */
	protected static function get_sanitized_value( array $array, $key ) {
		$value = '';

		if ( array_key_exists( $key, $array ) ) {
			$value = $array[ $key ];
		}

		if ( '' !== $value ) {
			$value = wp_unslash( $value );

			if ( is_string( $value ) ) {
				$value = sanitize_text_field( $value );
			}
		}

		return $value;
	}

	/**
	 * Get a $_GET parameter value.
	 *
	 * @since 2.12.1
	 *
	 * @param string $key Parameter name.
	 *
	 * @return array|string
	 */
	public static function get_http_get_parameter( $key ) {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return self::get_sanitized_value( $_GET, $key );
	}

	/**
	 * Get a $_POST parameter value.
	 *
	 * @since 2.12.1
	 *
	 * @param string $key Parameter name.
	 *
	 * @return array|string
	 */
	public static function get_http_post_parameter( $key ) {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return self::get_sanitized_value( $_POST, $key );
	}

	/**
	 * Get a $_REQUEST parameter value.
	 *
	 * @since 2.12.1
	 *
	 * @param string $key Parameter name.
	 *
	 * @return array|string
	 */
	public static function get_http_request_parameter( $key ) {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return self::get_sanitized_value( $_REQUEST, $key );
	}

	/**
	 * Run strpos on a $_REQUEST parameter value.
	 *
	 * @since 2.12.1
	 *
	 * @param string $key    Parameter in $_REQUEST
	 * @param string $needle String to search for
	 *
	 * @return bool
	 */
	public static function is_strpos_http_request( $key, $needle ) {

		return false !== strpos( self::get_http_request_parameter( $key ), $needle );
	}

	/**
	 * Extend $allowedposttags for kses.
	 *
	 * @since 2.12.1
	 * @return array
	 */
	public static function allowed_post_tags_extended() {
		global $allowedposttags;
		if ( ! is_array( $allowedposttags ) ) {
			// Impossible?
			return array();
		}

		$allowed_post_tags = $allowedposttags;

		$allowed_post_tags['input'] = array(
			'name'          => true,
			'id'            => true,
			'type'          => true,
			'checked'       => true,
			'class'         => true,
			'data-order'    => true,
			'data-language' => true,
			'disabled'      => true,
		);

		return $allowed_post_tags;
	}

	/**
	 * Method get_fs.
	 *
	 * @since 2.12.1
	 * @return WP_Filesystem_Direct|null
	 */
	public static function get_fs() {
		/**
		 * WP_Filesystem
		 *
		 * @global WP_Filesystem_Direct $wp_filesystem
		 */
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			if ( ! WP_Filesystem() ) {
				return null;
			}
		}

		return $wp_filesystem;
	}

	/**
	 * Method fs_get_contents replaces {@see file_get_contents()}.
	 * phpcs: WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents: file_get_contents() is discouraged.
	 *
	 * @since 2.12.1
	 *
	 * @param string $file Name of the file to read.
	 *
	 * @return string|false Read data on success, false on failure.
	 */
	public static function fs_get_contents( $file ) {
		$contents = false;

		$fs = self::get_fs();
		if ( $fs && $fs->is_readable( $file ) ) {
			$contents = $fs->get_contents( $file );
		}

		return $contents;
	}

	/**
	 * Method fs_put_contents replaces {@see file_put_contents()}.
	 *
	 * @since 2.12.1
	 *
	 * @param string    $file     Remote path to the file where to write the data.
	 * @param string    $contents The data to write.
	 * @param int|false $mode     Optional. The file permissions as octal number, usually 0644.
	 *                            Default false.
	 * @return bool True on success, false on failure.
	 */
	public static function fs_put_contents( $file, $contents, $mode = false ) {

		$fs = self::get_fs();
		if ( $fs ) {
			return $fs->put_contents( $file, $contents, $mode );
		}

		return false;
	}

	/**
	 * Returns sanitized $_SERVER['REQUEST_URI'].
	 *
	 * @since 2.12.1
	 *
	 * @param string $default Default to return when unset.
	 *
	 * @return string
	 */
	public static function request_uri( $default = '' ) {
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
			// Something abnormal.
			return $default;
		}

		return esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	/**
	 * True if a parameter exists in $_GET.
	 *
	 * @since 2.12.1
	 *
	 * @param string $name The parameter name.
	 *
	 * @return bool
	 */
	public static function is_parameter_in_http_get( $name ) {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return array_key_exists( $name, $_GET );
	}

	/**
	 * True if a parameter exists in $_POST.
	 *
	 * @since 2.12.1
	 *
	 * @param string $name The parameter name.
	 *
	 * @return bool
	 */
	public static function is_parameter_in_http_post( $name ) {
		// PHPCS: WordPress.Security.NonceVerification.Missing is invalid in the context of this method.
		0 && wp_verify_nonce( '' );

		return array_key_exists( $name, $_POST );
	}

	/**
	 * Returns sanitized $_SERVER element.
	 *
	 * @since 2.12.1
	 *
	 * @param string $key     The element name.
	 * @param string $default Default to return when unset.
	 *
	 * @return string
	 */
	public static function get_server_element( $key, $default = '' ) {
		if ( ! isset( $_SERVER[ $key ] ) ) {
			// Something abnormal. Maybe WP-CLI.
			return $default;
		}

		return sanitize_text_field( wp_unslash( $_SERVER[ $key ] ) );
	}

	/**
	 * Returns sanitized $_SERVER['HTTP_REFERER'].
	 *
	 * @since 2.12.1
	 *
	 * @param string $default Default to return when unset.
	 *
	 * @return string
	 */
	public static function http_referer( $default = '' ) {
		return self::get_server_element( 'HTTP_REFERER', $default );
	}

	/**
	 * Returns sanitized $_SERVER['QUERY_STRING'].
	 *
	 * @since 2.12.1
	 *
	 * @param string $default Default to return when unset.
	 *
	 * @return string
	 */
	public static function query_string( $default = '' ) {
		return self::get_server_element( 'QUERY_STRING', $default );
	}

	/**
	 * Returns sanitized $_SERVER['HTTP_HOST'].
	 *
	 * @since 2.12.1
	 *
	 * @param string $default Default to return when unset.
	 *
	 * @return string
	 */
	public static function http_host( $default = 'localhost' ) {
		return self::get_server_element( 'HTTP_HOST', $default );
	}
}
