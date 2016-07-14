<?php
/**
 * @package WPGlobus/Admin
 */

/**
 * Class WPGlobus_Plugin_Install
 * @since 1.5.9
 */

if ( ! class_exists( 'WPGlobus_Plugin_Install' ) ) :

	/**
	 * Class WPGlobus_Plugin_Install
	 */
	class WPGlobus_Plugin_Install {

		/**
		 * Array of plugin cards.
		 *
		 * @var array
		 */
		static protected $plugin_card = array();

		/**
		 * Array of paid plugins data.
		 *
		 * @var array
		 */
		static protected $paid_plugins = array();

		/**
		 * @var bool $_SCRIPT_DEBUG Internal representation of the define('SCRIPT_DEBUG')
		 */
		static protected $_SCRIPT_DEBUG = false;

		/**
		 * @var string $_SCRIPT_SUFFIX Whether to use minimized or full versions of JS and CSS.
		 */
		static protected $_SCRIPT_SUFFIX = '.min';

		/**
		 * Controller.
		 */
		public static function controller() {

			if ( empty( $_GET['source'] ) ) {
				return;
			}

			if ( empty( $_GET['s'] ) ) {
				return;
			}

			if ( 'WPGlobus' !== $_GET['source'] || 'WPGlobus' !== $_GET['s'] ) {
				return;
			}

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				self::$_SCRIPT_DEBUG  = true;
				self::$_SCRIPT_SUFFIX = '';
			}

			self::$paid_plugins['woocommerce-wpglobus']['dir']      = WP_PLUGIN_DIR . '/woocommerce-wpglobus/woocommerce-wpglobus.php';
			self::$paid_plugins['wpglobus-plus']['dir']             = WP_PLUGIN_DIR . '/wpglobus-plus/wpglobus-plus.php';
			self::$paid_plugins['woocommerce-nets-netaxept']['dir'] = WP_PLUGIN_DIR . '/woocommerce-nets-netaxept/woocommerce-nets-netaxept.php';
			self::$paid_plugins['wpglobus-mobile-menu']['dir']      = WP_PLUGIN_DIR . '/wpglobus-mobile-menu/wpglobus-mobile-menu.php';

			// Enqueue the CSS & JS scripts.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

			add_filter( 'plugins_api_result', array( __CLASS__, 'filter__plugins_api_result' ), 10, 3 );
		}

		/**
		 * Filter api results
		 *
		 * @param stdClass|WP_Error $res    Response object or WP_Error.
		 * @param string            $action The type of information being requested from the Plugin Install API.
		 * @param stdClass          $args   Plugin API arguments.
		 *
		 * @return stdClass|WP_Error
		 */
		public static function filter__plugins_api_result(
			$res,
			/** @noinspection PhpUnusedParameterInspection */
			$action,
			/** @noinspection PhpUnusedParameterInspection */
			$args
		) {

			if ( is_wp_error( $res ) ) {
				return $res;
			}

			if ( empty( $res->plugins ) ) {
				return $res;
			}

			foreach ( $res->plugins as $key => $plugin ) {
				if ( false === strpos( $plugin->slug, 'wpglobus' ) ) {
					unset( $res->plugins[ $key ] );
				}
			}

			foreach ( self::$paid_plugins as $plugin => $plugin_data ) {

				if ( file_exists( $plugin_data['dir'] ) ) {
					self::$paid_plugins[ $plugin ]['plugin_data'] = get_plugin_data( $plugin_data['dir'] );
				} else {
					self::$paid_plugins[ $plugin ]['plugin_data'] = null;
				}

			}

			$paid_plugin                                              = clone $res->plugins[0];
			$slug                                                     = 'wpglobus-mobile-menu';
			$paid_plugin->name                                        = 'WPGlobus Mobile Menu';
			$paid_plugin->slug                                        = $slug;
			$paid_plugin->short_description                           = 'Makes WPGlobus language switcher compatible with mobile devices and narrow screens.';
			$paid_plugin->homepage                                    = 'http://www.wpglobus.com/product/wpglobus-mobile-menu/';
			$paid_plugin->icons['2x']                                 = 'http://www.wpglobus.com/app/uploads/2016/06/wpglobus-logo-400x400.png';
			$paid_plugin->icons['1x']                                 = 'http://www.wpglobus.com/app/uploads/2016/06/wpglobus-logo-400x400.png';
			$paid_plugin->active_installs                             = 0;
			$paid_plugin->version                                     = 0;
			self::$plugin_card[]                                      = 'wpglobus-mobile-menu';
			self::$paid_plugins[ $slug ]['card']                      = $paid_plugin;
			self::$paid_plugins[ $slug ]['extra_data']['product_url'] = 'http://www.wpglobus.com/product/wpglobus-mobile-menu/';
			self::$paid_plugins[ $slug ]['extra_data']['details_url'] = 'http://www.wpglobus.com/product/wpglobus-mobile-menu/';
			array_unshift( $res->plugins, $paid_plugin );

			$paid_plugin                                              = clone $res->plugins[0];
			$slug                                                     = 'woocommerce-nets-netaxept';
			$paid_plugin->name                                        = 'WooCommerce Nets Netaxept';
			$paid_plugin->slug                                        = $slug;
			$paid_plugin->short_description                           = 'With this add-on, you will be able to translate the Nets payment methods titles and descriptions to multiple languages.';
			$paid_plugin->homepage                                    = 'http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/';
			$paid_plugin->icons['2x']                                 = 'http://www.wpglobus.com/app/uploads/2016/06/woocommerce-wpglobus-netaxeptcw-logo-300x300.jpg';
			$paid_plugin->icons['1x']                                 = 'http://www.wpglobus.com/app/uploads/2016/06/woocommerce-wpglobus-netaxeptcw-logo-300x300.jpg';
			$paid_plugin->active_installs                             = 0;
			$paid_plugin->version                                     = 0;
			self::$plugin_card[]                                      = 'woocommerce-nets-netaxept';
			self::$paid_plugins[ $slug ]['card']                      = $paid_plugin;
			self::$paid_plugins[ $slug ]['extra_data']['product_url'] = 'http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/';
			self::$paid_plugins[ $slug ]['extra_data']['details_url'] = 'http://www.wpglobus.com/product/multilingual-woocommerce-nets-netaxept/';
			array_unshift( $res->plugins, $paid_plugin );

			$paid_plugin                                              = clone $res->plugins[0];
			$slug                                                     = 'wpglobus-plus';
			$paid_plugin->name                                        = 'WPGlobus Plus';
			$paid_plugin->slug                                        = $slug;
			$paid_plugin->short_description                           = 'With WPGlobus Plus, you will be able to hold incomplete translations as "drafts", translate URLs (post/page "slugs"), customize the menu language switcher layout and more.';
			$paid_plugin->homepage                                    = 'http://www.wpglobus.com/product/wpglobus-plus/';
			$paid_plugin->icons['2x']                                 = 'http://www.wpglobus.com/app/uploads/2015/08/wpglobus-plus-logo-300x300.png';
			$paid_plugin->icons['1x']                                 = 'http://www.wpglobus.com/app/uploads/2015/08/wpglobus-plus-logo-300x300.png';
			$paid_plugin->active_installs                             = 0;
			$paid_plugin->version                                     = 0;
			self::$plugin_card[]                                      = 'wpglobus-plus';
			self::$paid_plugins[ $slug ]['card']                      = $paid_plugin;
			self::$paid_plugins[ $slug ]['extra_data']['product_url'] = 'http://www.wpglobus.com/product/wpglobus-plus/';
			self::$paid_plugins[ $slug ]['extra_data']['details_url'] = 'http://www.wpglobus.com/extensions/wpglobus-plus/';
			array_unshift( $res->plugins, $paid_plugin );

			$paid_plugin                                              = clone $res->plugins[0];
			$slug                                                     = 'woocommerce-wpglobus';
			$paid_plugin->name                                        = 'WooCommerce WPGlobus';
			$paid_plugin->slug                                        = $slug;
			$paid_plugin->short_description                           = 'Makes WooCommerce-based online stores truly multilingual by allowing translating products, categories, tags and attributes to multiple languages.';
			$paid_plugin->homepage                                    = 'http://www.wpglobus.com/product/woocommerce-wpglobus/';
			$paid_plugin->icons['2x']                                 = WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/woocommerce-wpglobus-logo-300x300.png';
			$paid_plugin->icons['1x']                                 = WPGlobus::$PLUGIN_DIR_URL . 'includes/css/images/woocommerce-wpglobus-logo-300x300.png';
			$paid_plugin->active_installs                             = 0;
			$paid_plugin->version                                     = 0;
			self::$plugin_card[]                                      = 'woocommerce-wpglobus';
			self::$paid_plugins[ $slug ]['card']                      = $paid_plugin;
			self::$paid_plugins[ $slug ]['extra_data']['product_url'] = 'http://www.wpglobus.com/product/woocommerce-wpglobus/';
			self::$paid_plugins[ $slug ]['extra_data']['details_url'] = 'http://www.wpglobus.com/extensions-archive/woocommerce/';
			array_unshift( $res->plugins, $paid_plugin );

			$res->info['results'] = count( $res->plugins );

			return $res;

		}

		/**
		 * Enqueue admin JS scripts.
		 *
		 * @param string $hook_page The current admin page.
		 */
		static public function enqueue_scripts( $hook_page ) {

			if ( 'plugin-install.php' === $hook_page ) {

				wp_register_script(
					'wpglobus-plugin-install',
					dirname( plugin_dir_url( __FILE__ ) ) . '/js/wpglobus-plugin-install' . self::$_SCRIPT_SUFFIX . '.js',
					array( 'jquery' ),
					WPGLOBUS_VERSION,
					true
				);
				wp_enqueue_script( 'wpglobus-plugin-install' );
				wp_localize_script(
					'wpglobus-plugin-install',
					'WPGlobusPluginInstall',
					array(
						'version'    => WPGLOBUS_VERSION,
						'hookPage'   => $hook_page,
						'pluginCard' => self::$plugin_card,
						'pluginData' => self::$paid_plugins
					)
				);
			}
		}
	}

endif;
/*EOF*/
