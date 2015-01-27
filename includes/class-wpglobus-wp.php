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
	 * @param string $action
	 *
	 * @return bool
	 */
	public static function is_http_post_action( $action ){
		return ( ! empty( $_POST['action'] ) && $action === $_POST['action'] );
	}


} // class

# --- EOF