<?php
/**
 * Options panel heading
 *
 * @package WPGlobus/Admin
 */
ob_start();
?>
	<h1 style="width: 204px; float: left; margin: 0; padding: 0;">
		WPGlobus <?php echo WPGLOBUS_VERSION; ?>
	</h1>
	<div style="float:left;width:400px;">
		<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=SLF8M4YNZHNQN"
		   style="float: left; display: block; height: 100px; border: 1px solid silver; background-color: white; margin-right: 1em;">
			<img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG_global.gif" style="margin-top: 22px;"/>
		</a>

		<div style="margin-bottom: 0.5em;">
			<strong><?php esc_html_e( 'We rely on your support!', 'wpglobus' ); ?></strong></div>
		<?php esc_html_e( 'Please consider a small donation to support the future development.', 'wpglobus' ); ?>
		<br/>
		<em><?php esc_html_e( 'Thank you!', 'wpglobus' ); ?></em>
		<br/>
		<em><?php esc_html_e( 'The WPGlobus Team', 'wpglobus' ); ?></em>
	</div>

<?php if ( ! defined( 'WPGLOBUS_PLUS_VERSION' ) ): ?>
	<div style="float:right;width:400px;">
		<a href="http://www.wpglobus.com/shop/extensions/wpglobus-plus/"
		   style="float: left; display: block; height: 100px;">
			<img src="http://www.wpglobus.com/app/uploads/2015/08/wpglobus-plus-logo-150x150.png"
			     alt="WPGlobus Plus"
			     style="height: 100px; width: 100px; border: 1px solid silver; margin-right: 1em;"/>
		</a>

		<div style="margin-bottom: 0.5em;">
			<strong><?php esc_html_e( 'WPGlobus Plus!', 'wpglobus' ); ?></strong>
		</div>
		<?php esc_html_e( 'Advanced features and tweaks: URL translation, multilingual SEO analysis, separate publishing and more! ', 'wpglobus' ); ?>
		<br/>
		<a href="http://www.wpglobus.com/shop/extensions/wpglobus-plus/"
		   style="color: #990000; font-weight: 700;">
			<?php esc_html_e( 'Get WPGlobus Plus now!', 'wpglobus' ); ?>
		</a>
	</div>
<?php endif; ?>

	<div style="clear:both;"></div>
<?php
return ob_get_clean();

# --- EOF
