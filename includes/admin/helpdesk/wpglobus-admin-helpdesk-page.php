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

$tech_info = '';
foreach ( $data as $key => $value ) {
	if ( in_array( $key, array( 'name', 'email' ), true ) ) {
		continue;
	}
	$tech_info .= $key . ' = ' . $value . "\n";
}

$subject = empty( $_POST['subject'] ) ? '' : $_POST['subject']; // phpcs:ignore WordPress.CSRF.NonceVerification

$details = empty( $_POST['details'] ) ? '' : $_POST['details']; // phpcs:ignore WordPress.CSRF.NonceVerification

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
	<p><em>
			<?php esc_html_e( 'Thank you for using WPGlobus!', 'wpglobus' ); ?>
			<?php esc_html_e( 'Our Support Team is here to answer your questions or concerns.', 'wpglobus' ); ?>
		</em></p>
	<h4><?php esc_html_e( 'To help us serve you better:', 'wpglobus' ); ?></h4>
	<ol>
		<li><?php esc_html_e( 'Please check if the problem persists if you switch to a standard WordPress theme.', 'wpglobus' ); ?></li>
		<li><?php esc_html_e( 'Try deactivating other plugins to see if any of them conflicts with WPGlobus.', 'wpglobus' ); ?></li>
	</ol>
	<p>
		&bull; <?php printf( esc_html( 'Please fill in and submit the form below. Alternatively, please email %s.', 'wpglobus' ), '<a href="mailto:support@wpglobus.com">support@wpglobus.com</a>' ); ?></p>
	<hr/>

	<form action="<?php echo esc_url( WPGlobus_Admin_Page::url_helpdesk() ); ?>" method="post">

		<table class="form-table">
			<tbody>
			<tr class="form-field">
				<th><label for="name"><?php esc_html_e( 'Name' ); ?>:</label></th>
				<td><input required="required" type="text" name="name" id="name"
							value="<?php echo esc_attr( $data['name'] ); ?>" data-lpignore="true"/></td>
			</tr>
			<tr class="form-field">
				<th><label for="email"><?php esc_html_e( 'Email' ); ?>:</label></th>
				<td>
					<input required="required" type="email" name="email" id="email"
							value="<?php echo esc_attr( $data['email'] ); ?>" data-lpignore="true"/>
					<p class="description">
						<strong>
							<?php esc_html_e( 'Please make sure the email address is correct.', 'wpglobus' ); ?>
						</strong>
					</p>
				</td>
			</tr>
			<tr class="form-field">
				<th><label for="subject"><?php esc_html_e( 'Subject', 'wpglobus' ); ?>:</label></th>
				<td>
					<input required="required" type="text" name="subject" id="subject"
							value="<?php echo esc_attr( $subject ); ?>" data-lpignore="true"/>
					<p class="description">
						<?php esc_html_e( 'Short description of the problem', 'wpglobus' ); ?>
					</p>
				</td>
			</tr>
			<tr class="form-field">
				<th><label for="details"><?php esc_html_e( 'Detailed description', 'wpglobus' ); ?>:</label></th>
				<td>
					<textarea required="required" name="details" id="details" rows="10"><?php echo esc_attr( $details ); ?></textarea>
				</td>
			</tr>
			<tr class="form-field">
				<th><label for="info"><?php esc_html_e( 'Technical Information', 'wpglobus' ); ?>:</label></th>
				<td>
					<textarea name="info" id="info" rows="10"
							style="font-family: monospace; font-size: 10px; background-color: #eee; white-space: nowrap; overflow: hidden"
							data-gramm_editor="false"
							spellcheck="false"><?php echo esc_html( $tech_info ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'This information helps us to find the problem source', 'wpglobus' ); ?>
					</p>
				</td>
			</tr>
			</tbody>
		</table>

		<?php wp_nonce_field( WPGlobus_Admin_HelpDesk::NONCE_ACTION ); ?>

		<button class="button-primary" type="submit" name="send_email" id="send_email">
			<?php WPGlobus_Admin_Page::nav_tab_icon_e( 'Helpdesk' ); ?>
			<?php esc_html_e( 'Submit' ); ?>
		</button>

	</form>

<?php

WPGlobus_Admin_Page::print_footer();
