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
				background:      #ffffff url(<?php echo esc_url( $url_wpglobus_logo ); ?>) no-repeat;
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
	 * @return string
	 */
	public static function url_addons() {
		return add_query_arg(
			array(
				'tab'    => 'search',
				's'      => 'WPGlobus',
				'source' => 'WPGlobus',
			),
			self_admin_url( 'plugin-install.php' )
		);
	}

	/**
	 * URL of the WPGlobus Settings page.
	 * @return string
	 */
	public static function url_settings() {
		return admin_url( 'admin.php' ) . '?page=wpglobus_options';
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
