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

			// phpcs:ignore WordPress.CSRF.NonceVerification.NoNonceVerification
			if ( empty( $_GET['s'] ) || 'wpglobus' !== strtolower( $_GET['s'] ) ) {
				return;
			}

			self::$fake_compatible_with = $GLOBALS['wp_version'];

			self::$plugin_card['free'] = array();
			self::$plugin_card['paid'] = array();

			self::setup_paid_plugins();

			// Enqueue the CSS & JS scripts.
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

			add_filter( 'plugins_api_result', array( __CLASS__, 'filter__plugins_api_result' ), 10, 3 );
		}

		/**
		 * List of the premium WPGlobus extensions.
		 * This file is created manually.
		 */
		protected static function setup_paid_plugins() {

			self::$paid_plugins = array();

			$data_file = WPGlobus::data_path() . '/paid_plugins.json';

			if ( is_readable( $data_file ) ) {
				$_json              = file_get_contents( $data_file );
				self::$paid_plugins = json_decode( $_json, true );
				uasort( self::$paid_plugins, array( __CLASS__, 'sort_paid_plugins' ) );
			}
		}

		/**
		 * Callback for sorting the paid_plugins array.
		 *
		 * @param array $a First.
		 * @param array $b Second.
		 *
		 * @return int
		 */
		public static function sort_paid_plugins( $a, $b ) {
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
				if ( is_array( $plugin ) ) {
					/** @since  2.1.10 */
					$plugin = (object) $plugin;
				}
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

			$all_products = self::get_all_product_info();

			foreach ( self::$paid_plugins as $plugin => $plugin_data ) {

				$plugin_file = implode( '/', array( WP_PLUGIN_DIR, $plugin_data['slug'], $plugin_data['loader'] ) );

				if ( is_readable( $plugin_file ) ) {
					// Plugin is installed.
					self::$paid_plugins[ $plugin ]['plugin_data'] = get_plugin_data( $plugin_file, false );
				} else {
					self::$paid_plugins[ $plugin ]['plugin_data'] = null;

					$product_slug = ( isset( $plugin_data['product_slug'] ) ? $plugin_data['product_slug'] : $plugin );

					if ( isset( $all_products[ $product_slug ] ) ) {
						$plugin_info = $all_products[ $product_slug ];

						/**
						 * Titles come as multilingual strings but only in 2 languages ['en','ru']
						 * because the WPGlobus website has only those.
						 * So we need to force `en` language code if the admin language
						 * is out of the list.
						 */
						$language = WPGlobus::Config()->language;
						if ( ! in_array( $language, array( 'en', 'ru' ), true ) ) {
							$language = 'en';
						}
						$_plugin_title = WPGlobus_Core::text_filter( $plugin_info['title'], $language );

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
			foreach ( self::$paid_plugins as $slug => $paid_plugin ) {

				$info = self::plugin_info_template();

				$info->slug = $slug;

				$info->icons['default'] = WPGlobus::internal_images_url() . '/' . $paid_plugin['image_file'];
				$info->icons['1x']      = $info->icons['default'];
				$info->icons['2x']      = $info->icons['default'];

				if ( ! empty( $paid_plugin['plugin_data'] ) ) {
					$info->name              = $paid_plugin['plugin_data']['Name'];
					$info->short_description = $paid_plugin['plugin_data']['Description'];
					$info->homepage          = $paid_plugin['plugin_data']['PluginURI'];
				} else {
					$info->name = $slug;
				}

				self::$plugin_card['paid'][] = $slug;

				self::$paid_plugins[ $slug ]['card'] = $info;

				self::$paid_plugins[ $slug ]['extra_data']['product_url'] =
				self::$paid_plugins[ $slug ]['extra_data']['details_url'] =
					$info->homepage;

				array_unshift( $res->plugins, $info );

			}

			$res->info['results'] = count( $res->plugins );

			return $res;
		}

		/**
		 * Get information about all premium plugins.
		 * This file is created automatically at build. Do not edit!
		 *
		 * @return array[]
		 */
		protected static function get_all_product_info() {
			$all_product_info = array();

			$data_file = WPGlobus::data_path() . '/wpglobus-product-info.json';

			if ( is_readable( $data_file ) ) {
				$all_product_info_json = file_get_contents( $data_file );
				$all_product_info      = json_decode( $all_product_info_json, true );
			}

			return $all_product_info;
		}

		/**
		 * Template for plugin info.
		 *
		 * @return stdClass
		 */
		protected static function plugin_info_template() {
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
		public static function enqueue_scripts( $hook_page ) {

			if ( 'plugin-install.php' === $hook_page ) {

				$i18n                    = array();
				$i18n['current_version'] = esc_html__( 'Current Version', 'wpglobus' );
				$i18n['get_it']          = esc_html__( 'Get it now!', 'wpglobus' );
				$i18n['card_header']     = esc_html__( 'Premium add-on', 'wpglobus' );
				$i18n['installed']       = esc_html__( 'Installed', 'wpglobus' );

				wp_register_script(
					'wpglobus-plugin-install',
					WPGlobus::$PLUGIN_DIR_URL . '/includes/js/wpglobus-plugin-install' . WPGlobus::SCRIPT_SUFFIX() . '.js',
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
