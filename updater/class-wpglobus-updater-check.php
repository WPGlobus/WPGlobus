<?php
/**
 * Check Update Status
 *
 * @package WPGlobus/Updater
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Updater_API_Check' ) ) :

	/**
	 * Class WPGlobus_Updater_API_Check
	 */
	class WPGlobus_Updater_API_Check {

		protected $_messages;

		private $upgrade_url; // URL to access the Update API Manager.
		private $plugin_name; // same as plugin slug. if a theme use a theme name like 'twentyeleven'
		private $product_id; // Software Title
		private $api_key; // API License Key
		private $activation_email; // License Email
		private $renew_license_url; // URL to renew a license
		private $instance; // Instance ID (unique to each blog activation)
		private $domain; // blog domain name
		private $plugin_or_theme; // 'theme' or 'plugin'
		private $extra; // Used to send any extra information.

		/** @var string $slug */
		private $slug = '';

		/**
		 * @param string $upgrade_url
		 * @param string $plugin_name
		 * @param string $product_id
		 * @param string $api_key
		 * @param string $activation_email
		 * @param string $renew_license_url
		 * @param string $instance
		 * @param string $domain
		 * @param string $software_version Unused
		 * @param string $plugin_or_theme
		 * @param string $text_domain      Unused
		 * @param string $extra
		 */
		public function init(
			$upgrade_url, $plugin_name, $product_id, $api_key, $activation_email, $renew_license_url, $instance, $domain, /** @noinspection PhpUnusedParameterInspection */
			$software_version, $plugin_or_theme, /** @noinspection PhpUnusedParameterInspection */
			$text_domain, $extra = ''
		) {

			// API data
			$this->upgrade_url       = $upgrade_url;
			$this->plugin_name       =
				$plugin_name; // same as plugin slug. if a theme use a theme name like 'twentyeleven'
			$this->product_id        = $product_id;
			$this->api_key           = $api_key;
			$this->activation_email  = $activation_email;
			$this->renew_license_url = $renew_license_url;
			$this->instance          = $instance;
			$this->domain            = $domain;
			$this->extra             = $extra;

			// Slug should be the same as the plugin/theme directory name
			if ( strpos( $this->plugin_name, '.php' ) !== 0 ) {
				$this->slug = dirname( $this->plugin_name );
			} else {
				$this->slug = $this->plugin_name;
			}
			/**
			 * Flag for plugin or theme updates
			 *
			 * @param string , plugin or theme
			 */
			$this->plugin_or_theme = $plugin_or_theme; // 'theme' or 'plugin'

			/*********************************************************************
			 * The plugin and theme filters should not be active at the same time
			 *********************************************************************/

			/**
			 * More info:
			 * function set_site_transient moved from wp-includes/functions.php
			 * to wp-includes/option.php in WordPress 3.4
			 * set_site_transient() contains the pre_set_site_transient_{$transient} filter
			 * {$transient} is either update_plugins or update_themes
			 * Transient data for plugins and themes exist in the Options table:
			 * _site_transient_update_themes
			 * _site_transient_update_plugins
			 */

			// uses the flag above to determine if this is a plugin or a theme update request
			if ( $this->plugin_or_theme === 'plugin' ) {
				/**
				 * Plugin Updates
				 */
				// Check For Plugin Updates
				add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );

				// Check For Plugin Information to display on the update details page
				add_filter( 'plugins_api', array( $this, 'request' ), 10, 3 );

			} else if ( $this->plugin_or_theme === 'theme' ) {
				/**
				 * Theme Updates
				 */
				// Check For Theme Updates
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_check' ) );

				// Check For Theme Information to display on the update details page
				//add_filter( 'themes_api', array( $this, 'request' ), 10, 3 );

			}

			$this->_init_messages();

		}

		/**
		 * Upgrade API URL
		 *
		 * @param array $args
		 * @return string
		 */
		private function create_upgrade_api_url( $args ) {
			$upgrade_url = add_query_arg( 'wc-api', 'upgrade-api', $this->upgrade_url );

			return $upgrade_url . '&' . http_build_query( $args );
		}

		/**
		 * Check for updates against the remote server.
		 *
		 * @see set_site_transient
		 * @param  mixed $transient
		 * @return mixed $transient
		 */
		public function update_check( $transient ) {


			if ( empty( $transient->checked[ $this->plugin_name ] ) ) {
				return $transient;
			}

			$curr_ver = (string) $transient->checked[ $this->plugin_name ];

			$args = array(
				'request'          => 'pluginupdatecheck',
				'slug'             => $this->slug,
				'plugin_name'      => $this->plugin_name,
				'version'          => $curr_ver,
				'product_id'       => $this->product_id,
				'api_key'          => $this->api_key,
				'activation_email' => $this->activation_email,
				'instance'         => $this->instance,
				'domain'           => $this->domain,
			);

			// Check for a plugin update
			$response = $this->plugin_information( $args );

			// Displays an admin error message in the WordPress dashboard
			$this->check_response_for_errors( $response );

			// Set version variables
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				// New plugin version from the API
				$new_ver = (string) $response->new_version;
			}

			// If there is a new version, modify the transient to reflect an update is available
			if ( isset( $new_ver ) ) {

				/** @noinspection NestedPositiveIfStatementsInspection */
				if ( $response !== false && version_compare( $new_ver, $curr_ver, '>' ) ) {

					if ( $this->plugin_or_theme === 'plugin' ) {

						$transient->response[ $this->plugin_name ] = $response;

					} elseif ( $this->plugin_or_theme === 'theme' ) {

						$transient->response[ $this->plugin_name ]['new_version'] = $response->new_version;
						$transient->response[ $this->plugin_name ]['url']         = $response->url;
						$transient->response[ $this->plugin_name ]['package']     = $response->package;

					}

				}

			}

			return $transient;
		}

		/**
		 * Sends and receives data to and from the server API
		 *
		 * @param array $args
		 * @return stdClass|bool $response
		 */
		public function plugin_information( $args ) {

			$target_url = esc_url_raw( $this->create_upgrade_api_url( $args ) );

			$request = wp_remote_get( $target_url );

			if ( is_wp_error( $request ) || (int) wp_remote_retrieve_response_code( $request ) !== 200 ) {
				return false;
			}

			$response = wp_remote_retrieve_body( $request );
			if ( ! is_serialized( $response ) ) {
				error_log( 'WPGlobus_Updater Error: ' . serialize( $response ) );

				return false;
			}

			$response = unserialize( $response );

			if ( is_object( $response ) ) {
				if ( ! empty( $response->sections ) ) {
					foreach ( $response->sections as $section_name => $section_content ) {
						/** @noinspection AlterInForeachInspection */
						$response->sections[ $section_name ] = apply_filters( 'the_content', $section_content );
					}
				}

				return $response;
			} else {
				return false;
			}
		}

		/**
		 * Generic request helper.
		 *
		 * @param  bool           $result
		 * @param  string         $action Unused
		 * @param  stdClass|array $args
		 * @return stdClass|bool $response or boolean false
		 */
		public function request( $result, $action, $args ) {

			if ( empty( $action ) or $action !== 'plugin_information' ) {
				return $result;
			}

			if ( empty( $args->slug ) or $args->slug !== $this->slug ) {
				// Not our business
				return $result;
			}


			$transient = get_site_transient(
				$this->plugin_or_theme === 'plugin' ? 'update_plugins' : 'update_themes'
			);


			if ( empty( $transient->checked[ $this->plugin_name ] ) ) {
				return $result;
			}

			$curr_ver = (string) $transient->checked[ $this->plugin_name ];

			$args = array(
				'request'          => 'plugininformation',
				'plugin_name'      => $this->plugin_name,
				'version'          => $curr_ver,
				'product_id'       => $this->product_id,
				'api_key'          => $this->api_key,
				'activation_email' => $this->activation_email,
				'instance'         => $this->instance,
				'domain'           => $this->domain,
				'software_version' => $curr_ver,
				//				'extra'            => $this->extra,
			);

			$response = $this->plugin_information( $args );

			// If everything is okay return the $response
			if ( isset( $response ) && is_object( $response ) && $response !== false ) {
				$result = $response;
			}

			return $result;

		}

		/**
		 * Displays admin error messages if response has errors.
		 *
		 * @param  stdClass $response
		 */
		public function check_response_for_errors( $response ) {

			if ( ! empty( $response ) ) {

				foreach (
					array(
						'exp_license',
						'hold_subscription',
						'cancelled_subscription',
						'exp_subscription',
						'suspended_subscription',
						'pending_subscription',
						'trash_subscription',
						'no_subscription',
						'no_activation',
						'no_key',
						'download_revoked',
						'switched_subscription',
					) as $error_code
				) {
					if ( isset( $response->errors[ $error_code ] ) &&
					     $response->errors[ $error_code ] === $error_code
					) {
						add_action( 'admin_notices', array( $this, $error_code . '_error_notice' ) );
					}
				}

			}

		}

		/**
		 * Initialize error message texts.
		 *
		 * @since 1.2.6
		 */
		protected function _init_messages() {
			$this->_messages = array(
				'go_to_account'          => '<a href="' . $this->renew_license_url . '" target="_blank">' .
				                            esc_html__( 'To check status, purchase or reactivate license, please go to your account.', 'wpglobus' ) .
				                            '</a>',
				'exp_license'            => esc_html__( 'License expired.', 'wpglobus' ),
				'hold_subscription'      => esc_html__( 'Subscription on-hold.', 'wpglobus' ),
				'cancelled_subscription' => esc_html__( 'Subscription cancelled.', 'wpglobus' ),
				'suspended_subscription' => esc_html__( 'Subscription suspended.', 'wpglobus' ),
				'expired_subscription'   => esc_html__( 'Subscription expired.', 'wpglobus' ),
				'pending_subscription'   => esc_html__( 'Subscription pending.', 'wpglobus' ),
				'trash_subscription'     => esc_html__( 'Subscription pending deletion.', 'wpglobus' ),
				'no_subscription'        => esc_html__( 'Subscription not found.', 'wpglobus' ),
				'no_key'                 => esc_html__( 'License key not found or deactivated.', 'wpglobus' ),
				'download_revoked'       => esc_html__( 'No download permission. License / subscription probably expiring.', 'wpglobus' ),
				'no_activation'          => esc_html__( 'License not activated. Go to the settings page and enter the license key and email to activate.', 'wpglobus' ),
				'switched_subscription'  => esc_html__( 'Subscription changed. Please enter new license key in the settings page.', 'wpglobus' ),

			);

			// To debug:
			//			foreach ( $this->_messages as $_ => $__ ) {
			//				if ( method_exists( $this, $_ . '_error_notice' ) ) {
			//					add_action( 'admin_notices', array( $this, $_ . '_error_notice' ) );
			//				}
			//			}

		}

		/**
		 * Print error notice
		 *
		 * @param string $status_msg_id
		 * @param string $info_msg_id
		 * @since 1.2.6
		 */
		protected function _print_error_notice( $status_msg_id, $info_msg_id = '' ) {
			echo '<div class="error"><p>';
			echo '<strong>' . $this->product_id . ':</strong> ';
			echo $this->_messages[ $status_msg_id ];

			if ( $info_msg_id ) {
				echo ' ' . $this->_messages[ $info_msg_id ];
			}

			echo '</p></div>';
		}

		/**
		 * Display license expired error notice
		 */
		public function exp_license_error_notice() {
			$this->_print_error_notice( 'exp_license', 'go_to_account' );
		}

		/**
		 * Display subscription on-hold error notice
		 */
		public function hold_subscription_error_notice() {
			$this->_print_error_notice( 'hold_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription cancelled error notice
		 */
		public function cancelled_subscription_error_notice() {
			$this->_print_error_notice( 'cancelled_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription expired error notice
		 */
		public function expired_subscription_error_notice() {
			$this->_print_error_notice( 'expired_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription suspended error notice
		 */
		public function suspended_subscription_error_notice() {
			$this->_print_error_notice( 'suspended_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription pending error notice
		 */
		public function pending_subscription_error_notice() {
			$this->_print_error_notice( 'pending_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription expired error notice
		 */
		public function trash_subscription_error_notice() {
			$this->_print_error_notice( 'trash_subscription', 'go_to_account' );
		}

		/**
		 * Display subscription expired error notice
		 */
		public function no_subscription_error_notice() {
			$this->_print_error_notice( 'no_subscription', 'go_to_account' );
		}

		/**
		 * Display missing key error notice
		 */
		public function no_key_error_notice() {
			$this->_print_error_notice( 'no_key', 'go_to_account' );
		}

		/**
		 * Display missing download permission revoked error notice
		 */
		public function download_revoked_error_notice() {
			$this->_print_error_notice( 'download_revoked', 'go_to_account' );
		}

		/**
		 * Display no activation error notice
		 */
		public function no_activation_error_notice() {
			$this->_print_error_notice( 'no_activation' );
		}

		/**
		 * Display switched activation error notice
		 */
		public function switched_subscription_error_notice() {
			$this->_print_error_notice( 'switched_subscription' );
		}

	} // class

endif;

# --- EOF
