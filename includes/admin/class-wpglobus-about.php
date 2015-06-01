<?php
/**
 * @package   WPGlobus/Admin
 */

/**
 * Class WPGlobus_About
 */
class WPGlobus_About {

	/**
	 * Output the about screen.
	 */
	public static function about_screen() {

		if ( WPGlobus::Config()->language == 'en' ) {
			$language = '';
		} else {
			$language = WPGlobus::Config()->language . '/';
		}

		$url_wpglobus_site = WPGlobus::URL_WPGLOBUS_SITE . $language;

		$url_wpglobus_site_home =
			$url_wpglobus_site .
			'?utm_source=wpglobus-admin-about&utm_medium=link&utm_campaign=active-plugins';

		$url_wpglobus_site_contact =
			$url_wpglobus_site .
			'pg/contact-us' .
			'?utm_source=wpglobus-admin-about&utm_medium=link&utm_campaign=active-plugins';

		/**
		 * @quirk
		 * Keeping this "wrap" only to display admin notice(s)
		 */
		?>
		<div class="wrap">

			<h2><?php
				/**
				 * @quirk
				 * This should be H2, so that it goes above the WP admin notices
				 */
				echo __( 'About WPGlobus', 'wpglobus' );
				?></h2>


			<div class="wrap about-wrap">

				<div class="about-text">
					<?php printf( __( 'Thank you for installing WPGlobus!', 'wpglobus' ), WPGLOBUS_VERSION ); ?>
				</div>

				<h2 class="nav-tab-wrapper">
					<a href="#" class="nav-tab nav-tab-active">
						<?php printf( __( 'Version %s' ), WPGLOBUS_VERSION ); ?>
					</a>
					<a href="admin.php?page=wpglobus_options" class="nav-tab">
						<?php _e( 'Settings' ); ?>
					</a>
					<a href="admin.php?page=wpglobus-addons" class="nav-tab">
						<?php _e( 'Add-ons', 'wpglobus' ); ?>
					</a>
					<a href="<?php echo esc_url( $url_wpglobus_site_contact ); ?>"
					   class="nav-tab">
						<?php _e( 'Feedback' ); ?>
					</a>
				</h2>

				<div class="feature-main feature-section col three-col">

					<div style="margin-top: 2em;">
						<img src="<?php echo WPGlobus::$PLUGIN_DIR_URL .
						                     'includes/css/images/wpglobus-logo-180x180.png'; ?>"
						     alt="WPGlobus logo"/>
					</div>

					<div>
						<h4><?php _e( 'What is WPGlobus', 'wpglobus' ); ?></h4>

						<p><?php _e( 'WPGlobus is a globalization (multi-lingual, internationalization, localization, ...) WordPress plugin.', 'wpglobus' ); ?></p>

						<p><?php _e( 'Our goal is to let WordPress support multiple languages, countries and currencies (for e-commerce).', 'wpglobus' ); ?></p>

						<p><?php printf( __( 'For more information, please visit %s.', 'wpglobus' ),
								'<a href="' . esc_url( $url_wpglobus_site_home ) . '">WPGlobus.com</a>' ); ?></p>
					</div>


					<div class="last-feature">
						<h4><?php _e( 'Feature Highlights', 'wpglobus' ); ?></h4>

						<ul class="wpglobus-checkmarks">
							<li><?php _e( 'Add multiple languages and countries to your site.', 'wpglobus' ); ?></li>
							<li><?php _e( 'Translate posts, pages, categories, tags and menus using a clean and simple interface.', 'wpglobus' ); ?></li>
							<li><?php _e( 'Switch languages at the front-end using a drop-down menu with language names and country flags.', 'wpglobus' ); ?></li>
							<li><?php printf( __( 'Continue using the %s - yes, WPGlobus supports the WP-SEO titles and descriptions in multiple languages!', 'wpglobus' ), '<a href="https://yoast.com/wordpress/plugins/seo/">WordPress SEO Plugin by Yoast</a>' ); ?></li>
						</ul>
					</div>
				</div>

				<hr/>

				<h3><?php _e( 'Important', 'wpglobus' ); ?></h3>
				<ul class="wpglobus-important">
					<li>
						<?php _e( "The plugin is currently in <strong>BETA</strong> stage! We will do our best to fix all problems you'll discover. Please be patient. Your contributions (on GitHub) are highly appreciated!", 'wpglobus' ); ?>
					</li>

					<li>
						<?php _e( "WPGlobus only supports the localization URLs in the form of <code>example.com/xx/page/</code>. We do not plan to support subdomains <code>xx.example.com</code> and language queries <code>example.com?lang=xx</code>.", 'wpglobus' ); ?>
					</li>

				</ul>

				<hr/>

				<div class="return-to-dashboard">
					<a href="admin.php?page=wpglobus_options">
						<?php _e( 'Go to WPGlobus Settings', 'wpglobus' ); ?>
					</a>
				</div>
			</div>

		</div>

	<?php
	}

} //class

# --- EOF