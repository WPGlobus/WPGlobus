<?php
/**
 * @package   WPGlobus\Admin
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
		 * For Google Analytics
		 */
		$ga_campaign = '?utm_source=wpglobus-admin-about&utm_medium=link&utm_campaign=activate-plugin';

		$url_wpglobus_site             = WPGlobus_Utils::url_wpglobus_site();
		$url_wpglobus_site_home        = $url_wpglobus_site . $ga_campaign;
		$url_wpglobus_site_quick_start = $url_wpglobus_site . 'quick-start/' . $ga_campaign;
		$url_wpglobus_site_faq         = $url_wpglobus_site . 'faq/' . $ga_campaign;
		$url_wpglobus_site_pro_support = $url_wpglobus_site . 'professional-support/' . $ga_campaign;

		WPGlobus_Admin_Page::print_header();

		?>
		<h2 class="nav-tab-wrapper">
			<a href="#" class="nav-tab nav-tab-active">
				<?php esc_html_e( 'Quick Start', 'wpglobus' ); ?>
			</a>
			<a href="<?php echo esc_url( WPGlobus_Admin_Page::url_settings() ); ?>"
			   class="nav-tab">
				<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Settings' ); ?>
				<?php esc_html_e( 'Settings' ); ?>
			</a>
			<a href="<?php echo esc_url( WPGlobus_Admin_Page::url_addons() ); ?>"
			   class="nav-tab">
				<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Add-ons' ); ?>
				<?php esc_html_e( 'Add-ons', 'wpglobus' ); ?>
			</a>
			<a href="<?php echo esc_url( WPGlobus_Utils::url_wpglobus_site() . 'quick-start/' ); ?>"
			   target="_blank"
			   class="nav-tab">
				<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Guide' ); ?>
				<?php esc_html_e( 'Guide', 'wpglobus' ); ?>
			</a>
			<a href="<?php echo esc_url( WPGlobus_Utils::url_wpglobus_site() . 'faq/' ); ?>"
			   target="_blank"
			   class="nav-tab">
				<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'FAQ' ); ?>
				<?php esc_html_e( 'FAQ', 'wpglobus' ); ?>
			</a>
			<a href="<?php echo esc_url( WPGlobus_Admin_HelpDesk::$admin_page_url ); ?>"
			   class="nav-tab">
				<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Helpdesk' ); ?>
				<?php echo esc_html( WPGlobus_Admin_HelpDesk::$page_title ); ?>
			</a>
		</h2>

		<?php if ( ! extension_loaded( 'mbstring' ) ) : ?>
			<div style="background: #fff;border-left: 4px solid #dc3232;margin: 15px 15px 2px;padding: 1px 12px;">
				<h4><?php esc_html_e( 'Attention: the Multibyte String PHP extension (`mbstring`) is not loaded!', 'wpglobus' ); ?></h4>
				<p><?php esc_html_e( 'The mbstring extension is required for the full UTF-8 compatibility and better performance. Without it, some parts of WordPress and WPGlobus may function incorrectly. Please contact your hosting company or systems administrator.', 'wpglobus' ); ?></p>
			</div>
		<?php endif; ?>

		<div class="feature-main feature-section col two-col">
			<div class="col">
				<h4><?php esc_html_e( 'Easy as 1-2-3:', 'wpglobus' ); ?></h4>
				<ul class="wpglobus-checkmarks">
					<li><?php esc_html_e( 'Go to WPGlobus admin menu and choose the countries / languages;', 'wpglobus' ); ?></li>
					<li><?php esc_html_e( 'Enter the translations to the posts, pages, categories, tags and menus using a clean and simple interface.', 'wpglobus' ); ?></li>
					<li><?php esc_html_e( 'Switch languages at the front-end using a drop-down menu with language names and country flags.', 'wpglobus' ); ?></li>
				</ul>
			</div>
			<div class="col last-feature">
				<h4><?php esc_html_e( 'Links:', 'wpglobus' ); ?></h4>
				<ul>
					<li>&bull; <a href="<?php echo esc_url( $url_wpglobus_site_home ); ?>"
					              target="_blank">WPGlobus.com</a></li>
					<li>&bull; <a href="<?php echo esc_url( $url_wpglobus_site_quick_start ); ?>"
					              target="_blank"><?php esc_html_e( 'Guide', 'wpglobus' ); ?></a></li>
					<li>&bull; <a href="<?php echo esc_url( $url_wpglobus_site_faq ); ?>"
					              target="_blank"><?php esc_html_e( 'FAQs', 'wpglobus' ); ?></a></li>
					<li>&bull; <a href="<?php echo esc_url( WPGlobus_Admin_HelpDesk::$admin_page_url ); ?>"
					              target="_blank"><?php esc_html_e( 'Contact Us', 'wpglobus' ); ?></a></li>
					<li>&bull; <a href="https://wordpress.org/support/plugin/wpglobus/reviews/?filter=5"
					              target="_blank"><?php esc_html_e( 'Please give us 5 stars!', 'wpglobus' ); ?></a>
						<span class="wpglobus-stars">&#x2606;&#x2606;&#x2606;&#x2606;&#x2606;</span></li>

				</ul>
			</div>
		</div>

		<hr />

		<ul class="wpglobus-important">

			<li>
				<?php _e( 'WPGlobus only supports the localization URLs in the form of <code>example.com/xx/page/</code>. We do not plan to support subdomains <code>xx.example.com</code> and language queries <code>example.com?lang=xx</code>.', 'wpglobus' ); // WPCS: XSS ok. ?>
			</li>
			<li>
				<?php _e( 'Some themes and plugins are <strong>not multilingual-ready</strong>.', 'wpglobus' );  // WPCS: XSS ok. ?>
				<?php esc_html_e( 'They might display some texts with no translation, or with all languages mixed together.', 'wpglobus' ); ?>
				<?php
				/* translators: %s are used to insert HTML link. Keep them in place. */
				printf(
					esc_html__( 'Please contact the theme / plugin author. If they are unable to assist, consider %s hiring the WPGlobus Team %s to write a custom code for you.', 'wpglobus' ),
					'<a href="' . esc_url( $url_wpglobus_site_pro_support ) . '">',
					'</a>'
				); ?>
			</li>

		</ul>

		<hr />

		<div class="return-to-dashboard">
			<a class="button button-primary"
			   href="<?php echo esc_url( WPGlobus_Admin_Page::url_settings() ); ?>">
				<?php esc_html_e( 'Go to WPGlobus Settings', 'wpglobus' ); ?>
			</a>
		</div>

		<?php
		WPGlobus_Admin_Page::print_footer();
	}
} //class

/*EOF*/
