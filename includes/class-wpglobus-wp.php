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
	 * @param string $page
	 *
	 * @return bool
	 */
	public static function is_pagenow( $page ) {
		/**
		 * Set in wp-includes/vars.php
		 * @global string $pagenow
		 */
		global $pagenow;

		return $pagenow === $page;
	}

	/**
	 * @param string|string[] $action
	 *
	 * @return bool
	 */
	public static function is_http_post_action( $action ) {
		if ( ! is_array( $action ) ) {
			$action = [ $action ];
		}

		return ( ! empty( $_POST['action'] ) && in_array( $_POST['action'], $action ) );
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


} // class

# --- EOF