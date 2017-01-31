<?php
/**
 * Show a sidebar menu item.
 * This is a workaround because the WPGlobus options panel is made by ReduxFramework.
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
		add_action( 'admin_footer', array( __CLASS__, '_add_ons' ),
			// Below the Help Desk
			PHP_INT_MAX - 10
		);
	}

	/**
	 * "Add-ons" menu.
	 */
	public static function _add_ons() {
		$icon_class     = 'dashicons dashicons-before dashicons-admin-plugins';
		$menu_title     = __( 'Add-ons', 'wpglobus' );
		$menu_tooltip   = __( 'View free and premium WPGlobus extensions', 'wpglobus' );
		$admin_page_url = WPGlobus_Admin_Page::url_addons();
		?>
		<script>
            jQuery(function ($) {
                $('#toplevel_page_wpglobus_options')
                    .find("ul")
                    .prepend($("<li>")
                        .append($("<a>")
                            .attr({
                                href: "<?php echo $admin_page_url; // WPCS: XSS ok. ?>",
                                title: "<?php echo esc_js( $menu_tooltip ); ?>"
                            })
                            .html(' <?php echo esc_js( $menu_title ); ?>')
                            .prepend($("<span>")
                                .attr({"class": "<?php echo esc_js( $icon_class ); ?>"})
                            )
                        )
                    );
            });
		</script>
		<?php
	}
}
