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


} // class

# --- EOF