<?php
/**
 * WPGlobus_Upgrade actions/filters.
 * @package WPGlobus
 */


/**
 * Class WPGlobus_Upgrade
 */
class WPGlobus_Upgrade {

	/**
	 * Display warning about new release with no backward compatibility with the "WPGlobus Mini"
	 * @todo Remove this completely in the stable version.
	 */
	public static function action__mini_warning() {

		if ( isset( WPGlobus::Config()->version['wpglobus_mini_warning'] )
		     && WPGlobus::Config()->version['wpglobus_mini_warning']
		) {
			$message =
				sprintf( __( 'Updated from WPGlobus Mini. Please, read instructions at %s', 'wpglobus' ),
					'<a href="' . admin_url()
					. 'admin.php?page=wpglobus-about#wpglobus-mini">WPGlobus About</a>' );

			$hide = sprintf( __( '<a href="%s">Hide Notice</a>', 'wpglobus' ),
				'?wpglobus_mini_warning=hide' );
			?>
			<div class="error">
				<p><span style="color:#f00" class="dashicons dashicons-admin-site"></span>&nbsp;<?php echo $message; ?>
					<span style="float:right;"><?php echo $hide; ?></a></span></p>
			</div>
		<?php
		}

	}

	/**
	 *  Hide the "WPGlobus Mini" warning
	 */
	public static function action__mini_hide_warning() {

		if ( ! empty( $_GET['wpglobus_mini_warning'] ) && 'hide' === $_GET['wpglobus_mini_warning'] ) {
			delete_option( WPGlobus_Config::$option_versioning );

			$version = array();

			$version['current_version'] = WPGLOBUS_VERSION;

			update_option( WPGlobus_Config::$option_versioning, $version );

			wp_redirect( $_SERVER['HTTP_REFERER'] );
			exit();
		}
	}

} // class

# --- EOF