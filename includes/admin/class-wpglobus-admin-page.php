<?php
/**
 * File: class-wpglobus-admin-page.php
 *
 * @package WPGlobus\Admin
 */

/**
 * Helpers for building admin pages.
 */
class WPGlobus_Admin_Page {

	/**
	 * Print page header.
	 */
	public static function print_header() {
		$url_wpglobus_logo = WPGlobus::internal_images_url() . '/wpglobus-logo-180x180.png';
		?>
		<style>
			.wp-badge.wpglobus-badge {
				background: #ffffff url(<?php echo esc_url( $url_wpglobus_logo ); ?>) no-repeat;
				background-size: contain;
			}
		</style>

		<?php echo '<div class="wrap about-wrap wpglobus-about-wrap">'; ?>

		<h1 class="wpglobus"><span class="wpglobus-wp">WP</span>Globus
			<span class="wpglobus-version"><?php echo esc_html( WPGLOBUS_VERSION ); ?></span>
		</h1>

		<div class="wpglobus-motto"><?php esc_html_e( 'Multilingual Everything!', 'wpglobus' ); ?></div>
		<div class="about-text">
			<?php esc_html_e( 'WPGlobus is a family of WordPress plugins assisting you in making multilingual WordPress blogs and sites.', 'wpglobus' ); ?>
		</div>

		<div class="wp-badge wpglobus-badge"></div>
		<?php
	}

	/**
	 * Print page header.
	 */
	public static function print_footer() {
		echo '</div>';
	}

	/**
	 * URL of the WPGlobus Add-ons page.
	 *
	 * @param bool $relative Set it to true to get the URL relative to the admin.
	 *
	 * @return string
	 */
	public static function url_addons( $relative = false ) {

		$url = add_query_arg(
			array(
				'tab'    => 'search',
				's'      => 'WPGlobus',
				'source' => 'WPGlobus',
			),
			self_admin_url( 'plugin-install.php' )
		);

		if ( $relative ) {
			$url = str_replace( self_admin_url(), '', $url );
		}

		return $url;

	}

	/**
	 * URL of the WPGlobus Settings page.
	 *
	 * @return string
	 */
	public static function url_settings() {
		return add_query_arg( 'page', WPGlobus::OPTIONS_PAGE_SLUG, admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the WPGlobus Helpdesk page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_helpdesk() {
		return add_query_arg( 'page', WPGlobus::PAGE_WPGLOBUS_HELPDESK, admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the Clean-up Tool page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_clean_up_tool() {
		return add_query_arg( 'page', WPGlobus::PAGE_WPGLOBUS_CLEAN, admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the About page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_about() {
		return add_query_arg( 'page', WPGlobus::PAGE_WPGLOBUS_ABOUT, admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the Admin Central page.
	 *
	 * @param string $central_tab_id Tab on the page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_admin_central( $central_tab_id = '' ) {
		return add_query_arg( 'page', WPGlobus::PAGE_WPGLOBUS_ADMIN_CENTRAL . '#' . $central_tab_id, admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the Options Panel.
	 *
	 * @param string $tab Tab on the page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_options_panel( $tab = WPGlobus_Options::DEFAULT_TAB ) {

		return add_query_arg( array(
			'page' => WPGlobus::OPTIONS_PAGE_SLUG,
			'tab'  => $tab,
		), admin_url( 'admin.php' ) );
	}

	/**
	 * URL of the WPGlobus-Plus Panel.
	 *
	 * @param string $tab Tab on the page.
	 *
	 * @return string
	 * @since 1.9.10
	 */
	public static function url_wpglobus_plus_panel( $tab = 'modules' ) {
		if ( class_exists( 'WPGlobusPlus', false ) ) {
			return add_query_arg( array(
				'page' => WPGlobusPlus::WPGLOBUS_PLUS_OPTIONS_PAGE,
				'tab'  => $tab,
			), admin_url( 'admin.php' ) );
		} else {
			return admin_url();
		}
	}

	/**
	 * Print icon for navigation tab item.
	 *
	 * @param string $icon_class A Dashicon CSS class or our internal alias.
	 *
	 * @link https://developer.wordpress.org/resource/dashicons/
	 */
	public static function nav_tab_icon_e( $icon_class ) {

		static $aliases = array(
			'faq'      => 'dashicons-editor-help',
			'settings' => 'dashicons-admin-settings',
			'add-ons'  => 'dashicons-admin-plugins',
			'guide'    => 'dashicons-book-alt',
			'helpdesk' => 'dashicons-format-chat',
			'globe'    => 'dashicons-admin-site',
		);

		$icon_class = strtolower( $icon_class );

		if ( isset( $aliases[ $icon_class ] ) ) {
			// An alias passed.
			$icon_class = $aliases[ $icon_class ];
		}

		echo '<span class="dashicons ' . esc_attr( $icon_class ) .
			 '" style="vertical-align: middle"></span>';
	}
}

/* EOF */
