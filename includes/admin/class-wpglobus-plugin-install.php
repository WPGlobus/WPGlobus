<?php
/**
 * File: class-wpglobus-plugin-install.php
 *
 * @package WPGlobus\Admin
 * @since   1.5.9
 */

if ( ! class_exists( 'WPGlobus_Plugin_Install' ) ) :

	/**
	 * Class WPGlobus_Plugin_Install
	 */
	class WPGlobus_Plugin_Install {

		/**
		 * Fake version for paid plugins to prevent the "Update Now" button from appearing.
		 *
		 * @var string
		 */
		const FAKE_VERSION = '999';

		/**
		 * Fake active installs for paid plugins.
		 *
		 * @var int
		 */
		const FAKE_ACTIVE_INSTALLS = 0;

		/**
		 * Fake "Compatible with your version of WordPress" for paid plugins.
		 *
		 * @var string
		 */
		protected static $fake_compatible_with = '';

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
		 * Array of free plugins data.
		 *
		 * @var array
		 */
		static protected $free_plugins = array();

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

			self::$fake_compatible_with = $GLOBALS['wp_version'];

			self::$plugin_card['free'] = array();
			self::$plugin_card['paid'] = array();

			self::_setup_premium_add_ons();

			// Enqueue the CSS & JS scripts.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

			add_filter( 'plugins_api_result', array( __CLASS__, 'filter__plugins_api_result' ), 10, 3 );
		}

		/**
		 * List of the premium WPGlobus extensions.
		 */
		protected static function _setup_premium_add_ons() {

			self::$paid_plugins = array(
				'woocommerce-wpglobus'      => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/woocommerce-wpglobus/woocommerce-wpglobus.php',
					'image_file' => 'woocommerce-wpglobus-logo-300x300.png',
					'order'      => 9,
				),
				'wpglobus-plus'             => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/wpglobus-plus/wpglobus-plus.php',
					'image_file' => 'wpglobus-plus-logo-300x300.png',
					'order'      => 8,
				),
				'wpglobus-mobile-menu'      => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/wpglobus-mobile-menu/wpglobus-mobile-menu.php',
					'image_file' => 'wpglobus-mobile-menu-logo-400x400.png',
					'order'      => 2,
				),
				'wpglobus-language-widgets' => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/wpglobus-language-widgets/wpglobus-language-widgets.php',
					'image_file' => 'wpglobus-lw-logo-400x400.png',
					'order'      => 4,
				),
				'wpglobus-menu-visibility'  => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/wpglobus-menu-visibility/wpglobus-menu-visibility.php',
					'image_file' => 'wpglobus-menu-visibility-logo.png',
					'order'      => 4,
				),
				'wpglobus-header-images'    => array(
					'dir'        => WP_PLUGIN_DIR .
					                '/wpglobus-header-images/wpglobus-header-images.php',
					'image_file' => 'wpglobus-hi-logo-400x400.png',
					'order'      => 4,
				),
				'woocommerce-nets-netaxept' => array(
					'dir'          => WP_PLUGIN_DIR .
					                  '/woocommerce-nets-netaxept/woocommerce-nets-netaxept.php',
					'product_slug' => 'multilingual-woocommerce-nets-netaxept',
					'image_file'   => 'woocommerce-wpglobus-netaxeptcw-logo-300x300.jpg',

					'order' => 5,
				),
				'wpglobus-revslider' => array(
					'dir'          => WP_PLUGIN_DIR .
					                  '/wpglobus-revslider/wpglobus-revslider.php',
					'product_slug' => 'wpglobus-for-slider-revolution',
					'image_file'   => 'wpglobus-revslider-logo-400x400.png',
					'order' => 4,
				)
			);

			uasort( self::$paid_plugins, array( __CLASS__, 'sort_callback' ) );
		}

		/**
		 * Callback for sorting premium add-ons.
		 *
		 * @param array $a First.
		 * @param array $b Second.
		 *
		 * @return int
		 */
		public static function sort_callback( $a, $b ) {
			return $a['order'] > $b['order'];
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
			// @formatter:off
			/* @noinspection PhpUnusedParameterInspection */ $action,
			/* @noinspection PhpUnusedParameterInspection */ $args
			// @formatter:on
		) {

			if ( is_wp_error( $res ) ) {
				return $res;
			}

			if ( empty( $res->plugins ) ) {
				return $res;
			}

			foreach ( (array) $res->plugins as $key => $plugin ) {
				if ( false === strpos( $plugin->slug, 'wpglobus' ) ) {
					unset( $res->plugins[ $key ] );
				} else {

					if ( 'wpglobus-for-black-studio-tinymce-widget' === $plugin->slug ) {

						/**
						 * Set correct slug for the
						 * `WPGlobus for Black Studio TinyMCE Widget` plugin.
						 *
						 * @since 1.6.3
						 */
						$plugin->slug = 'wpglobus-for-black-studio-widget';

						self::$plugin_card['free'][] = $plugin->slug;

						self::$free_plugins[ $plugin->slug ]['extra_data']['correctLink'] = 'wpglobus-for-black-studio-tinymce-widget';

					} else {
						self::$plugin_card['free'][] = $plugin->slug;
					}
				}
			}

			$url_wpglobus_site = WPGlobus_Utils::url_wpglobus_site();

			$all_products = self::_get_all_product_info_from_server();

			foreach ( self::$paid_plugins as $plugin => $plugin_data ) {

				if ( file_exists( $plugin_data['dir'] ) ) {
					self::$paid_plugins[ $plugin ]['plugin_data'] = get_plugin_data( $plugin_data['dir'], false );
				} else {
					self::$paid_plugins[ $plugin ]['plugin_data'] = null;

					$product_slug = ( isset( $plugin_data['product_slug'] ) ? $plugin_data['product_slug'] : $plugin );

					if ( isset( $all_products[ $product_slug ] ) ) {
						$plugin_info = $all_products[ $product_slug ];

						// Titles come as multilingual strings.
						$_plugin_title = WPGlobus_Filters::filter__text( $plugin_info['title'] );

						self::$paid_plugins[ $plugin ]['plugin_data'] = array(
							'Description' => '', // TODO.
							'Name'        => $_plugin_title,
							'Title'       => $_plugin_title,
							'Version'     => $plugin_info['_api_new_version'],
							'PluginURI'   => $url_wpglobus_site . 'product/' .
							                 $product_slug . '/',
						);
					}
				}
			}

			/**
			 * Prepend the premium add-ons to the list of plugins.
			 */
			foreach ( self::$paid_plugins as $slug => $paid_plugin ) :

				$_info = self::_plugin_info_template();

				$_info->slug = $slug;

				$_info->icons['default'] =
				$_info->icons['1x'] =
				$_info->icons['2x'] = WPGlobus::internal_images_url() . '/' .
				                      $paid_plugin['image_file'];

				if ( ! empty( $paid_plugin['plugin_data'] ) ) {
					$_info->name              = $paid_plugin['plugin_data']['Name'];
					$_info->short_description = $paid_plugin['plugin_data']['Description'];
					$_info->homepage          = $paid_plugin['plugin_data']['PluginURI'];
				} else {
					$_info->name = $slug;
				}

				self::$plugin_card['paid'][] = $slug;

				self::$paid_plugins[ $slug ]['card'] = $_info;

				self::$paid_plugins[ $slug ]['extra_data']['product_url'] =
				self::$paid_plugins[ $slug ]['extra_data']['details_url'] =
					$_info->homepage;

				array_unshift( $res->plugins, $_info );

			endforeach;

			$res->info['results'] = count( $res->plugins );

			return $res;
		}

		/**
		 * Call the WPGlobus server to get information about all premium plugins.
		 *
		 * @return array[]
		 */
		protected static function _get_all_product_info_from_server() {
			$all_product_info = get_transient( 'wpglobus_all_product_info' );
			if ( false === $all_product_info ) {

				$all_product_info = array();

				$http_response = wp_safe_remote_get(
					WPGlobus_Utils::url_wpglobus_site() . 'wc-api/wpglobus-product-info'
				);
				if ( ! empty( $http_response['body'] ) ) {
					$_decoded_json = json_decode( $http_response['body'], true );
					if ( is_array( $_decoded_json ) ) {
						$all_product_info = $_decoded_json;
						set_transient( 'wpglobus_all_product_info',
							$all_product_info, DAY_IN_SECONDS
						);
					}
				}
			}

			return $all_product_info;
		}

		/**
		 * Template for plugin info.
		 *
		 * @return stdClass
		 */
		protected static function _plugin_info_template() {
			$url_wpglobus_site = WPGlobus_Utils::url_wpglobus_site();

			$template                    = new stdClass();
			$template->name              = '';
			$template->short_description = '';
			$template->author            = '<a href="' . $url_wpglobus_site . '">WPGlobus</a>';
			$template->author_profile    = $url_wpglobus_site;
			$template->homepage          = $url_wpglobus_site;
			$template->slug              = '';
			$template->rating            = 100;
			$template->num_ratings       = 0;
			$template->active_installs   = self::FAKE_ACTIVE_INSTALLS;
			$template->version           = self::FAKE_VERSION;
			$template->tested            = self::$fake_compatible_with;
			$template->icons['default']  = '';
			$template->icons['2x']       = '';
			$template->icons['1x']       = '';
			$template->last_updated      = date( 'c' );

			return $template;
		}

		/**
		 * Enqueue admin JS scripts.
		 *
		 * @param string $hook_page The current admin page.
		 */
		static public function enqueue_scripts( $hook_page ) {

			if ( 'plugin-install.php' === $hook_page ) {

				$i18n                    = array();
				$i18n['current_version'] = esc_html__( 'Current Version', 'wpglobus' );
				$i18n['get_it']          = esc_html__( 'Get it now!', 'wpglobus' );
				$i18n['card_header']     = esc_html__( 'Premium add-on', 'wpglobus' );
				$i18n['installed']       = esc_html__( 'Installed', 'wpglobus' );

				wp_register_script(
					'wpglobus-plugin-install',
					dirname( plugin_dir_url( __FILE__ ) ) . '/js/wpglobus-plugin-install' . WPGlobus::SCRIPT_SUFFIX() . '.js',
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
						'pluginData' => array_merge( self::$paid_plugins, self::$free_plugins ),
						'i18n'       => $i18n,
					)
				);
			}
		}
	}

endif;
/*EOF*/
