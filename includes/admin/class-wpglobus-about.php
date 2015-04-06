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
				
				<?php 
				if ( WPGlobus::Config()->language == WPGlobus::Config()->default_language ) {
					$language = '';
				} else {
					$language = WPGlobus::Config()->language . '/';
				}	
				?>

				<h2 class="nav-tab-wrapper">
					<a href="admin.php?page=wpglobus-about" class="nav-tab nav-tab-active">
						<?php _e( 'What&#8217;s New' ); ?>
					</a><a href="admin.php?page=wpglobus_options" class="nav-tab">
						<?php _e( 'Settings' ); ?>
					</a><a href="<?php echo WPGlobus::URL_WPGLOBUS_SITE . $language; ?>contact-us/" class="nav-tab">
						<?php _e( 'Feedback' ); ?>
					</a><a href="admin.php?page=wpglobus-about#wpglobus-mini" class="nav-tab">
						<?php _e( 'Breaking changes!', 'wpglobus' ); ?>
					</a><a href="admin.php?page=wpglobus-addons" class="nav-tab">
						<?php _e( 'Add-ons', 'wpglobus' ); ?>
					</a>
				</h2>

				<div class="feature-main feature-section col three-col">

					<div style="text-align: center;">
						<h4><?php printf( __( 'Version %s', 'wpglobus' ), WPGLOBUS_VERSION ); ?></h4>
						<img
							src="<?php echo WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/wpglobus-logo-180x180.png'; ?>"
							alt="WPGlobus logo"/>
					</div>

					<div>
						<h4><?php _e( 'What is WPGlobus', 'wpglobus' ); ?></h4>

						<p><?php _e( 'WPGlobus is a globalization (multi-lingual, internationalization, localization, ...) WordPress plugin.', 'wpglobus' ); ?></p>

						<p><?php _e( 'Our goal is to let WordPress support multiple languages, countries and currencies (for e-commerce).', 'wpglobus' ); ?></p>

						<p><?php printf( __( 'For more information, please visit %s.', 'wpglobus' ), '<a href="http://www.wpglobus.com">WPGlobus.com</a>' ); ?></p>
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

					<li>
						<?php _e( 'The ALPHA version (0.1.x, called internally <em>"WPGlobus Mini"</em>) is no longer supported. See the note below for more details.', 'wpglobus' ); ?>
					</li>
				</ul>

				<hr/>

				<p id="wpglobus-mini">&nbsp;</p>

				<h3><?php _e( 'Warning! WPGlobus Mini (0.x.x) is no longer supported!', 'wpglobus' ); ?></h3>

				<p><?php _e( "The versions 0.x.x provided the existing users of other language plugins with a language-switcher menu dropdown. We are going to keep the language switcher, but we have dropped the support of subdomains and language queries in our plugin. If your site is using subdomains or <code>?lang=xx</code>, and you'd like to keep that behavior, please do not use WPGlobus starting from the version 1.0.0.", 'wpglobus' ); ?>
				</p>

				<p><?php
					printf(
						__( 'To downgrage, please download the version 0.1.1 using %s.', 'wpglobus' ),
						'<a href="https://downloads.wordpress.org/plugin/wpglobus.0.1.1.zip">this link</a>' );
					?></p>

				<hr/>

				<div class="return-to-dashboard">
					<a href="admin.php?page=wpglobus_options">
						<?php _e( 'Go to WPGlobus Settings', 'wpglobus' ); ?>
					</a>
				</div>
			</div>

		</div>

		<?php
		/**
		 * @quirk
		 * Make the page longer to display the '#wpglobus-mini nicely'
		 */
		?>
		<div style="height: 20em">&nbsp;</div>
	<?php
	}

} //class

# --- EOF