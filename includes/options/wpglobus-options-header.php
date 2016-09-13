<?php
/**
 * File: wpglobus-options-header.php
 *
 * @package     WPGlobus\Admin\Options
 * @author      WPGlobus
 */

ob_start();
?>
	<h1>WPGlobus <?php echo esc_html( WPGLOBUS_VERSION ); ?></h1>
	<div class="wpg-bnr wpg-bnr-left">
		<a class="wpg-a-img"
		   href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=SLF8M4YNZHNQN">
			<img src="<?php echo esc_url( WPGlobus::$PLUGIN_DIR_URL ); ?>includes/css/images/btn_donateCC_LG_global.gif"
			     style="margin-top: 27px;"/>
		</a>

		<div class="wpg-text-block">
			<div class="wpg-title"><?php esc_html_e( 'We rely on your support!', 'wpglobus' ); ?></div>

			<div class="wpg-body">
				<?php esc_html_e( 'Please consider a small donation to support the future development.', 'wpglobus' ); ?>
			</div>

			<div class="wpg-footer">
				<?php esc_html_e( 'Thank you!', 'wpglobus' ); ?>
				<br/>
				<?php esc_html_e( 'The WPGlobus Team', 'wpglobus' ); ?>
			</div>
		</div>
	</div>

<?php if ( ! defined( 'WPGLOBUS_PLUS_VERSION' ) ) : ?>
	<?php
	$_url_wpglobus_plus_store = WPGlobus_Utils::url_wpglobus_site() .
	                            'product/wpglobus-plus/';
	$_url_wpglobus_plus_logo  = WPGlobus::$PLUGIN_DIR_URL .
	                            'includes/css/images/wpglobus-plus-logo-150x150.png';
	?>
	<div class="wpg-bnr wpg-bnr-right">
		<a class="wpg-a-img" href="<?php echo esc_url( $_url_wpglobus_plus_store ); ?>">
			<img src="<?php echo esc_url( $_url_wpglobus_plus_logo ); ?>"
			     alt="WPGlobus Plus"/>
		</a>

		<div class="wpg-text-block">
			<div class="wpg-title"><?php esc_html_e( 'WPGlobus Plus!', 'wpglobus' ); ?></div>

			<div class="wpg-body">
				<?php esc_html_e( 'Advanced features and tweaks: URL translation, multilingual SEO analysis, separate publishing and more! ', 'wpglobus' ); ?>
			</div>

			<div class="wpg-footer">
				<a href="<?php echo esc_url( $_url_wpglobus_plus_store ); ?>"
				   style="color: #990000; font-weight: 700;">
					<?php esc_html_e( 'Get WPGlobus Plus now!', 'wpglobus' ); ?>
				</a>
			</div>
		</div>

	</div>
<?php endif; ?>

	<div style="clear:both;"></div>
<?php
return ob_get_clean();

/*EOF*/
