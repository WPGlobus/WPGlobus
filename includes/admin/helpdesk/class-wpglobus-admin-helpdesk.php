<?php
/**
 * File: class-wpglobus-admin-helpdesk.php
 *
 * @package WPGlobus\Admin\HelpDesk
 */


/**
 * Class WPGlobus_Admin_HelpDesk.
 * The Contact Support form.
 */
class WPGlobus_Admin_HelpDesk {

	/**
	 * Nonce.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'wpglobus-helpdesk';

	/**
	 * Email address of the Support.
	 *
	 * @var string
	 */
	const EMAIL_SUPPORT = 'support@wpglobus.com';

	/**
	 * Admin page title.
	 *
	 * @var string
	 */
	public static $page_title;
	/**
	 * Admin menu title.
	 *
	 * @var string
	 */
	protected static $menu_title;

	/**
	 * @var string
	 */
	protected static $name;

	/**
	 * @return string
	 */
	public static function getName() {
		return self::$name;
	}

	/**
	 * @var string
	 */
	protected static $email;

	/**
	 * @return string
	 */
	public static function getEmail() {
		return self::$email;
	}

	/**
	 * @var string
	 */
	protected static $submission_status = 'success';

	/**
	 * @return string
	 */
	public static function getSubmissionStatus() {
		return self::$submission_status;
	}

	/**
	 * @var string
	 */
	protected static $submission_message = '';

	/**
	 * @return string
	 */
	public static function getSubmissionMessage() {
		return self::$submission_message;
	}

	/**
	 * Static "constructor".
	 */
	public static function construct() {
		self::set_vars();
		self::set_hooks();
	}

	/**
	 * Set class variables.
	 */
	public static function set_vars() {
		self::$page_title = __( 'WPGlobus Help Desk', 'wpglobus' );
		self::$menu_title = __( 'Help Desk', 'wpglobus' );
	}

	/**
	 * Setup actions and filters.
	 */
	protected static function set_hooks() {
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ), PHP_INT_MAX );
	}

	/**
	 * Add admin menu item.
	 */
	public static function add_menu() {
		add_submenu_page(
			WPGlobus::OPTIONS_PAGE_SLUG,
			self::$page_title,
			'<span class="' . esc_attr( WPGlobus_Admin_Page::nav_tab_icon( 'Helpdesk' ) ) . '"></span> '
			. self::$menu_title,
			'administrator',
			WPGlobus::PAGE_WPGLOBUS_HELPDESK,
			array( __CLASS__, 'helpdesk_page' )
		);
	}

	/**
	 * The admin page.
	 */
	public static function helpdesk_page() {
		/** @noinspection PhpUnusedLocalVariableInspection */
		$data = self::get_data();

		self::handle_submit();

		/*
		 * Prepare data for the view.
		 */

		$active_plugins = explode( ', ', $data['active_plugins'] );
		unset( $data['active_plugins'] );

		$tech_info = '';
		foreach ( $data as $key => $value ) {
			$tech_info .= $key . ' = ' . $value . "\n";
		}

		// Split one-cell formatted list of plugins into the separate rows.
		foreach ( $active_plugins as $active_plugin ) {
			list( $name, $version ) = explode( ':', $active_plugin );

			$tech_info .= $name . ' = ' . $version . "\n";
		}

		/** @noinspection PhpUnusedLocalVariableInspection */
		$subject = empty( $_POST['subject'] ) ? '' : sanitize_text_field( $_POST['subject'] ); // phpcs:ignore WordPress.CSRF.NonceVerification

		/** @noinspection PhpUnusedLocalVariableInspection */
		$details = empty( $_POST['details'] ) ? '' : sanitize_textarea_field( $_POST['details'] ); // phpcs:ignore WordPress.CSRF.NonceVerification

		// Render view.
		include dirname( __FILE__ ) . '/wpglobus-admin-helpdesk-page.php';

	}

	/**
	 * Handle the form submit.
	 */
	protected static function handle_submit() {
		if ( ! empty( $_POST ) ) {
			check_admin_referer( self::NONCE_ACTION );

			if (
				empty( $_POST['name'] )
				|| empty( $_POST['email'] )
				|| empty( $_POST['subject'] )
				|| empty( $_POST['details'] )
			) {
				self::$submission_status  = 'error';
				self::$submission_message = __( 'Email not sent. Please fill in the entire form.', 'wpglobus' );

				return;
			}

			self::$name  = sanitize_text_field( $_POST['name'] );
			self::$email = sanitize_email( $_POST['email'] );

			if ( ! self::$name || ! self::$email ) {
				self::$submission_status  = 'error';
				self::$submission_message = __( 'Email not sent. Please verify that your name and email are entered correctly.', 'wpglobus' );

				return;
			}

			$message = sanitize_textarea_field( $_POST['details'] );
			if ( ! empty( $_POST['info'] ) ) {
				$message .= "\n-----\n" . sanitize_textarea_field( $_POST['info'] );
			}

			$headers = array(
				'from: ' . self::$name . ' <' . self::$email . '>',
				'reply-to: ' . self::$email,
				'cc: ' . self::$email,
			);

			add_action( 'wp_mail_failed', array( __CLASS__, 'action__wp_mail_failed' ) );

			if ( wp_mail( self::EMAIL_SUPPORT, $_POST['subject'], $message, $headers ) ) :

				self::$submission_status  = 'success';
				self::$submission_message = __( 'Email sent.', 'wpglobus' );

			endif;

			remove_action( 'wp_mail_failed', array( __CLASS__, 'action__wp_mail_failed' ) );

		}
	}

	/**
	 * Collect technical data.
	 *
	 * @return array
	 */
	protected static function get_data() {
		$user        = wp_get_current_user();
		self::$name  = WPGlobus_Filters::filter__text( $user->display_name );
		self::$email = $user->user_email;

		$theme = wp_get_theme();

		/**
		 * @see   php_uname can be disabled in php.ini for security reasons
		 * disable_functions=php_uname
		 * @since 1.7.13
		 */
		$OS = 'Unknown';
		if ( function_exists( 'php_uname' ) ) {
			$OS = implode( ' ', array(
				php_uname( 's' ),
				php_uname( 'r' ),
				php_uname( 'v' ),
			) );
		}

		$data = array(
			'home_url'          => home_url(),
			'site_url'          => site_url(),
			'REMOTE_ADDR'       => sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ),
			// WPCS: input var ok, sanitization ok.
			'SERVER_PORT'       => sanitize_text_field( wp_unslash( $_SERVER['SERVER_PORT'] ) ),
			// WPCS: input var ok, sanitization ok.
			'OS'                => $OS,
			'PHP_SAPI'          => PHP_SAPI,
			'PHP_VERSION'       => PHP_VERSION,
			'loaded_extensions' => implode( ', ', get_loaded_extensions() ),
			'wp_version'        => $GLOBALS['wp_version'],
			'is_multisite'      => is_multisite() ? 'Y' : 'N',
			'theme'             => $theme->display( 'Name' ) . ' ' . $theme->display( 'ThemeURI' ) . ' by ' . $theme->get( 'Author' ) . ' ' . $theme->get( 'AuthorURI' ) . ( is_child_theme() ? '; child of ' . $theme->display( 'Template' ) : '' ),
			'enabled_languages' => implode( ', ', WPGlobus::Config()->enabled_languages ),
		);

		// The list of plugins is formatted here for display on the admin page,
		// to fit into one table cell.
		$active_plugins = array();
		foreach ( wp_get_active_and_valid_plugins() as $plugin ) {
			$plugin_data = get_plugin_data( $plugin );
			$plugin_file = str_replace( trailingslashit( WP_PLUGIN_DIR ), '', dirname( $plugin ) );

			$active_plugins[] = $plugin_file . ':' . $plugin_data['Version'];
		}
		$data['active_plugins'] = implode( ', ', $active_plugins );

		return $data;

	}

	/**
	 * Print admin notice if sending failed.
	 *
	 * @param WP_Error $error
	 */
	public static function action__wp_mail_failed( WP_Error $error ) {
		self::$submission_status  = 'error';
		self::$submission_message = $error->get_error_message();
	}
}
