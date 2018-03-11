<?php
/**
 * Show a sidebar menu item.
 *
 * @since 1.7.8
 */

/**
 * Class WPGlobus_Admin_Menu
 */
class WPGlobus_Admin_Menu {

	/**
	 * Static constructor.
	 */
	public static function construct() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ), PHP_INT_MAX );
	}

	public static function add_menu() {
		$icon_class = 'dashicons dashicons-before dashicons-admin-plugins';
		$menu_title = __( 'Add-ons', 'wpglobus' );
		add_submenu_page(
			WPGlobus::OPTIONS_PAGE_SLUG,
			$menu_title,
			'<span class="' . esc_attr( $icon_class )
			. '" style="vertical-align:middle"></span> '
			. $menu_title,
			'administrator',
			WPGlobus_Admin_Page::url_addons(true)
		);

	}
}
