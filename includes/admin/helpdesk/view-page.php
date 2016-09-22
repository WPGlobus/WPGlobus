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
$url_wpglobus_logo = WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/wpglobus-logo-180x180.png';
?>
	<style>
		.wp-badge.wpglobus-badge {
			background:      #ffffff url(<?php echo esc_url( $url_wpglobus_logo ); ?>) no-repeat;
			background-size: contain;
		}
	</style>
	<div class="wrap about-wrap wpglobus-about-wrap">
	<h1 class="wpglobus"><span class="wpglobus-wp">WP</span>Globus
		<span class="wpglobus-version"><?php echo esc_html( WPGLOBUS_VERSION ); ?></span>
	</h1>

	<div class="wpglobus-motto"><?php esc_html_e( 'Multilingual Everything!', 'wpglobus' ); ?></div>
	<div class="about-text">
		<?php esc_html_e( 'WPGlobus is a family of WordPress plugins assisting you in making multilingual WordPress blogs and sites.', 'wpglobus' ); ?>
	</div>

	<div class="wp-badge wpglobus-badge"></div>
	<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active">
			<?php echo esc_html( self::$page_title ); ?>
		</a>
	</h2>
	<div class="feature-main feature-section col two-col">
		<div class="col">
			<p><em><?php esc_html_e( 'Thank you for using WPGlobus!', 'wpglobus' ); ?></em></p>
			<p><?php esc_html_e( 'Our Support Team is here to answer your questions or concerns.', 'wpglobus' ); ?></p>
			<p><?php esc_html_e( 'Click the blue round button at the bottom right to open the Contact Form. Fill in your name, email, subject and the message.', 'wpglobus' ); ?></p>
			<p><?php esc_html_e( 'If you made a screenshot showing the problem, please attach it.', 'wpglobus' ); ?></p>
			<p><strong>► <?php esc_html_e( 'Please note we will receive some debug data together with your request. See the "Technical Information" table for the details.', 'wpglobus' ); ?></strong></p>

			<h4><?php esc_html_e( 'To help us serve you better:', 'wpglobus' ); ?></h4>
			<ul>
				<li><?php esc_html_e( 'Please check if the problem persists if you switch to a standard WordPress theme.', 'wpglobus' ); ?></li>
				<li><?php esc_html_e( 'Try deactivating other plugins to see if any of them conflicts with WPGlobus.', 'wpglobus' ); ?></li>
			</ul>
			<hr/>
			<p><em><?php esc_html_e( 'Sincerely Yours,', 'wpglobus' ); ?></em></p>
			<p><em><?php esc_html_e( 'The WPGlobus Team', 'wpglobus' ); ?></em></p>
		</div>
		<div class="col last-feature">
			<h4><?php esc_html_e( 'Technical Information', 'wpglobus' ); ?></h4>
			<table class="widefat striped">
				<tbody>
				<?php
				foreach ( $data as $key => $value ) {
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
/* EOF */
