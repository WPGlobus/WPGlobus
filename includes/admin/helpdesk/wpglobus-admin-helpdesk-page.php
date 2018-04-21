<?php
/**
 * File: view-page.php
 *
 * @package WPGlobus\Admin\HelpDesk
 * @global string[] $data
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

WPGlobus_Admin_Page::print_header();

?>

	<h2 class="nav-tab-wrapper wp-clearfix">
		<a href="#" class="nav-tab nav-tab-active">
			<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Helpdesk' ); ?>
			<?php echo esc_html( WPGlobus_Admin_HelpDesk::$page_title ); ?>
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
		<a href="<?php echo esc_url( WPGlobus_Utils::url_wpglobus_site() ); ?>"
		   target="_blank"
		   class="nav-tab">
			<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'globe' ); ?>
			<?php echo esc_html( 'WPGlobus.com' ); ?>
		</a>
	</h2>
	<div class="feature-main feature-section col two-col">
		<div class="col">
			<p><em>
					<?php esc_html_e( 'Thank you for using WPGlobus!', 'wpglobus' ); ?>
					<?php esc_html_e( 'Our Support Team is here to answer your questions or concerns.', 'wpglobus' ); ?>
				</em></p>
			<p>&bull; <a href="#" class="wpglobus_admin_hs_beacon_toggle"><?php esc_html_e( 'Click here to open the Contact Form.', 'wpglobus' ); ?></a></p>
			<p>&bull; <?php esc_html_e( 'Type in your name, email, subject and the detailed message.', 'wpglobus' ); ?></p>
			<p>&bull; <?php esc_html_e( 'If you can make a screenshot demonstrating the problem, please attach it.', 'wpglobus' ); ?></p>
			<p class="highlight"><?php esc_html_e( 'Please note: we will receive some debug data together with your request. See the "Technical Information" table for the details.', 'wpglobus' ); ?></p>

			<h4><?php esc_html_e( 'To help us serve you better:', 'wpglobus' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'Please check if the problem persists if you switch to a standard WordPress theme.', 'wpglobus' ); ?></li>
				<li><?php esc_html_e( 'Try deactivating other plugins to see if any of them conflicts with WPGlobus.', 'wpglobus' ); ?></li>
			</ul>
			<hr />
			<p><em><?php esc_html_e( 'Sincerely Yours,', 'wpglobus' ); ?></em></p>
			<p><em><?php esc_html_e( 'The WPGlobus Team', 'wpglobus' ); ?></em></p>
		</div>
		<div class="col last-feature">
			<h4><?php esc_html_e( 'Technical Information', 'wpglobus' ); ?></h4>
			<table class="widefat striped">
				<tbody>
				<?php
				foreach ( $data as $key => $value ) {
					if ( in_array( $key, array( 'name', 'email' ), true ) ) {
						continue;
					}
					echo '<tr><th>' . esc_html( $key ) .
					     '</th><td>' . esc_html( $value ) .
					     '</td></tr>';
				}
				?>
				</tbody>
			</table>
		</div>
	</div>
<?php

WPGlobus_Admin_Page::print_footer();

/* EOF */
