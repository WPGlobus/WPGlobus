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
	 * CSS class for the menu icon.
	 * @var string
	 */
	const ICON_CLASS = 'dashicons dashicons-before dashicons-format-chat';

	/**
	 * Admin page title.
	 * @var string
	 */
	public static $page_title;
	/**
	 * Admin menu title.
	 * @var string
	 */
	protected static $menu_title;
	/**
	 * Admin menu tooltip.
	 * @var string
	 */
	protected static $menu_tooltip;
	/**
	 * Admin page URL.
	 * @var string
	 */
	public static $admin_page_url;

	/**
	 * Static "constructor".
	 */
	public static function construct() {
		self::set_hooks();
	}

	/**
	 * Set class variables.
	 */
	public static function set_vars() {
		self::$page_title   = __( 'WPGlobus Help Desk', 'wpglobus' );
		self::$menu_title   = __( 'Help Desk', 'wpglobus' );
		self::$menu_tooltip = __( 'Contact WPGlobus Support', 'wpglobus' );

		self::$admin_page_url = admin_url( 'admin.php?page=' ) .
		                        WPGlobus::PAGE_WPGLOBUS_HELPDESK;
	}

	/**
	 * Setup actions and filters.
	 */
	protected static function set_hooks() {
		add_action( 'admin_init', array( __CLASS__, 'set_vars' ) );
		add_action( 'admin_menu', array( __CLASS__, 'add_menu' ) );
		add_action( 'admin_footer', array( __CLASS__, 'show_submenu' ), PHP_INT_MAX );
	}

	/**
	 * Add a hidden admin menu item.
	 * It will become visible in @see WPGlobus_Admin_HelpDesk::show_submenu
	 */
	public static function add_menu() {
		add_submenu_page(
			null,
			'',
			'',
			'administrator',
			WPGlobus::PAGE_WPGLOBUS_HELPDESK,
			array( __CLASS__, 'helpdesk_page' )
		);
	}

	/**
	 * Make the admin menu item visible.
	 * This is a workaround because the WPGlobus options panel is made by ReduxFramework.
	 */
	public static function show_submenu() {
		?>
		<script>
			jQuery(function ($) {
				$('#toplevel_page_wpglobus_options')
					.find("ul")
					.prepend($("<li>")
						.append($("<a>")
							.attr({
								href: "<?php echo esc_url( self::$admin_page_url ); ?>",
								title: "<?php echo esc_js( self::$menu_tooltip ); ?>"
							})
							.html(' <?php echo esc_js( self::$menu_title ); ?>')
							.prepend($("<span>")
								.attr({"class": "<?php echo esc_js( self::ICON_CLASS ); ?>"})
							)
						)
					);
			});
		</script>
		<?php
	}

	/**
	 * The admin page.
	 */
	public static function helpdesk_page() {
		$data = self::get_data();

		include dirname( __FILE__ ) . '/wpglobus-admin-helpdesk-page.php';

		// Split one-cell formatted list of plugins into the separate rows.
		$active_plugins = explode( ', ', $data['active_plugins'] );
		unset( $data['active_plugins'] );
		foreach ( $active_plugins as $active_plugin ) {
			list( $name, $version ) = explode( ':', $active_plugin );
			$data[ $name ] = $version;
		}
		?>

		<script>
			<?php require dirname( __FILE__ ) . '/beacon-loader.min.js'; ?>
			HS.beacon.config({
				icon: 'message',
				attachment: 1,
				poweredBy: 0
			});

			jQuery(function ($) {
				HS.beacon.ready(function () {
					//noinspection JSUnresolvedFunction
					HS.beacon.identify(<?php echo wp_kses( wp_json_encode( $data ), array() );?>);
				});

				// Set a special class for the menu item.
				$(".wpglobus_admin_hs_beacon_toggle").on("click", function (e) {
					e.preventDefault();
					HS.beacon.toggle();
				});
			});
		</script>
		<?php
	}

	/**
	 * Collect data for the beacon.
	 * @return array
	 */
	protected static function get_data() {
		$user  = wp_get_current_user();
		$theme = wp_get_theme();

		/**
		 * @see php_uname can be disabled in php.ini for security reasons
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
			'name'              => WPGlobus_Filters::filter__text( $user->display_name ),
			'email'             => $user->user_email,
			'home_url'          => home_url(),
			'site_url'          => site_url(),
			'REMOTE_ADDR'       => $_SERVER['REMOTE_ADDR'],
			'SERVER_PORT'       => $_SERVER['SERVER_PORT'],
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
}

/* EOF */
