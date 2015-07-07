<?php
/**
 * WPGlobus Updater
 * @version   0.1.0
 * @package   WPGlobus-WC
 * @author    WPGlobus http://www.wpglobus.com/
 * @copyright Copyright 2014-2015 The WPGlobus Team: Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 * @license   GNU General Public License v3.0 http://www.gnu.org/licenses/gpl-3.0.html
 * --------------------------
 * @origins   WPGlobus Updater is based on the AME Example Plugin by Todd Lahman
 * ORIGINAL COPYRIGHT NOTICE:
 *    Intellectual Property rights, and copyright, reserved by Todd Lahman, LLC as allowed by law include,
 *    but are not limited to, the working concept, function, and behavior of this plugin,
 *    the logical code structure and expression as written.
 * author      Todd Lahman LLC
 * copyright   Copyright (c) Todd Lahman LLC
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Updater' ) ) :

	/**
	 * Class WPGlobus_Updater
	 */
	class WPGlobus_Updater {

		/**
		 * @var string
		 * Base URL to the remote upgrade API Manager server. If not set then the Author URI is used.
		 */
		public $upgrade_url = '';

		/** @var string */
		public $ame_software_product_id;
		/** @var string */
		public $ame_data_key;
		/** @var string */
		public $ame_api_key;
		/** @var string */
		public $ame_activation_email;
		/** @var string */
		public $ame_product_id_key;
		/** @var string */
		public $ame_instance_key;
		/** @var string */
		public $ame_deactivate_checkbox_key;
		/** @var string */
		public $ame_activated_key;

		/** @var string */
		public $ame_deactivate_checkbox;
		/** @var string */
		public $ame_activation_tab_key;
		/** @var string */
		public $ame_deactivation_tab_key;
		/** @var string */
		public $ame_settings_menu_title;
		/** @var string */
		public $ame_settings_title;
		/** @var string */
		public $ame_menu_tab_activation_title;
		/** @var string */
		public $ame_menu_tab_deactivation_title;

		/** @var string */
		public $ame_options;
		/** @var string */
		public $ame_plugin_name;
		/** @var string */
		public $ame_product_id;
		/** @var string */
		public $ame_renew_license_url;
		/** @var string */
		public $ame_instance_id;
		/** @var string */
		public $ame_domain;

		/**
		 * @var string $ame_plugin_or_theme 'theme' or 'plugin'
		 */
		public $ame_plugin_or_theme = 'plugin';

		/** @var string */
		public $ame_update_version;

		/** @var string */
		public $plugin_slug;

		/** @var string */
		protected $_plugin_file;

		/**
		 * @param array $args
		 *
		 * @example
		 *            new WPGlobus_Updater(
		 *            array(
		 *            'product_id'     => 'My Extension',
		 *            'url_product'    => 'http://www.wpglobus.com/shop/extensions/my-extension/',
		 *            'url_my_account' => 'http://www.wpglobus.com/my-account/',
		 *            'plugin_file'    => __FILE__,
		 *            )
		 *            );
		 */
		function __construct( Array $args = array() ) {

			if ( ! empty( $args['product_id'] ) ) {
				$this->ame_software_product_id = $args['product_id'];
			}
			if ( ! empty( $args['url_product'] ) ) {
				$this->upgrade_url = $args['url_product'];
			}
			if ( ! empty( $args['url_my_account'] ) ) {
				$this->ame_renew_license_url = $args['url_my_account'];
			}
			if ( ! empty( $args['plugin_file'] ) ) {
				$this->_plugin_file = $args['plugin_file'];
				$this->plugin_slug  = plugin_basename( $this->_plugin_file );
			}

			/**
			 * @todo Call it on plugin uninstall or make a special button
			 */
			0 && register_deactivation_hook( $this->_plugin_file, array( $this, 'uninstall' ) );

			$this->_init();
		}

		protected function _init() {

			// Check for external connection blocking
			add_action( 'admin_notices', array( $this, 'check_external_blocking' ) );


			/**
			 * Set all data defaults here
			 */
			$prefix = $this->ame_software_product_id;
			$prefix = mb_strtolower( $prefix );
			$prefix = preg_replace( '/[^%a-z0-9 _-]/', '', $prefix );
			$prefix = preg_replace( '/[\s-_]+/', '_', $prefix );
			$prefix = trim( $prefix, '_' );
			$prefix = 'wpgupd_' . $prefix;

			$this->ame_api_key = $prefix . '_api';

			/**
			 * These are stored in the `options` table.
			 * Watch for 64-characters limit.
			 */
			$this->ame_data_key                = $prefix . '_data';
			$this->ame_product_id_key          = $prefix . '_pid';
			$this->ame_instance_key            = $prefix . '_inst';
			$this->ame_deactivate_checkbox_key = $prefix . '_dea_cb_key';
			$this->ame_activated_key           = $prefix . '_act';
			$this->ame_deactivate_checkbox     = $prefix . '_dea_cb';
			$this->ame_activation_email        = $prefix . '_activation_email';
			$this->store_options();

			/**
			 * Set all admin menu data
			 */
			$this->ame_activation_tab_key          = $prefix . '_ame_activation_tab_key';
			$this->ame_deactivation_tab_key        = $prefix . '_ame_deactivation_tab_key';
			$this->ame_settings_menu_title         = $this->ame_software_product_id .
			                                         ' ' . __( 'License', 'wpglobus' );
			$this->ame_settings_title              = $this->ame_settings_menu_title;
			$this->ame_menu_tab_activation_title   = __( 'License Activation', 'wpglobus' );
			$this->ame_menu_tab_deactivation_title = __( 'License Deactivation', 'wpglobus' );

			/**
			 * Set all software update data here
			 */
			$this->ame_options     = get_option( $this->ame_data_key );
			$this->ame_plugin_name = $this->ame_software_product_id;
			$this->ame_product_id  = get_option( $this->ame_product_id_key ); // Software Title
			$this->ame_instance_id =
				get_option( $this->ame_instance_key ); // Instance ID (unique to each blog activation)
			/**
			 * Some web hosts have security policies that block the : (colon) and // (slashes) in http://,
			 * so only the host portion of the URL can be sent. For example the host portion might be
			 * www.example.com or example.com. http://www.example.com includes the scheme http,
			 * and the host www.example.com.
			 * Sending only the host also eliminates issues when a client site changes from http to https,
			 * but their activation still uses the original scheme.
			 * To send only the host, use a line like the one below:
			 * $this->ame_domain = str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name
			 */
			$this->ame_domain =
				str_ireplace( array( 'http://', 'https://' ), '', home_url() ); // blog domain name

			/**
			 * Displays an inactive message if the API License Key has not yet been activated
			 */
			if ( get_option( $this->ame_activated_key ) !== 'Activated' ) {
				add_action( 'admin_notices', array( $this, 'notice_license_inactive' ) );
			}


			// Admin menu with the license key and license email form
			require_once( 'class-wpglobus-updater-admin.php' );
			new WPGlobus_Updater_Menu( $this );

			$options = get_option( $this->ame_data_key );

			/**
			 * Check for software updates
			 */
			if ( ! empty( $options ) && $options !== false ) {

				// Checks for software updates
				require_once( 'class-wpglobus-updater-check.php' );
				$_updater_check = new WPGlobus_Updater_API_Check();
				$_updater_check->init(
					$this->upgrade_url,
					$this->plugin_slug,
					$this->ame_product_id,
					$this->ame_options[ $this->ame_api_key ],
					$this->ame_options[ $this->ame_activation_email ],
					$this->ame_renew_license_url,
					$this->ame_instance_id,
					$this->ame_domain,
					null,
					$this->ame_plugin_or_theme,
					null
				);

				// To debug messages:
				// add_action( 'admin_notices', array( $_updater_check, 'no_key_error_notice' ) );

			}

		}

		/**
		 * API Key Class.
		 * @return WPGlobus_Updater_Key
		 */
		public function key() {

			/** @var WPGlobus_Updater_Key $WPGlobus_Updater_Key */
			static $WPGlobus_Updater_Key = null;

			if ( is_null( $WPGlobus_Updater_Key ) ) {
				require_once( 'class-wpglobus-updater-requests.php' );
				$WPGlobus_Updater_Key = new WPGlobus_Updater_Key( $this );
			}

			return $WPGlobus_Updater_Key;
		}

		/**
		 * URL pointing to this folder.
		 * @return string
		 */
		public function my_url() {
			return plugins_url( '/', __FILE__ );
		}

		/**
		 * Store configuration in the options table
		 */
		public function store_options() {

			/**
			 * Check if options exist and do nothing
			 */
			if ( ! get_option( $this->ame_data_key ) ) {

				$global_options = array(
					$this->ame_api_key          => '',
					$this->ame_activation_email => '',
				);

				update_option( $this->ame_data_key, $global_options );
			}

			$instance = substr( sha1( rand( 10000, 20000 ) . rand( 20000, 30000 ) . rand( 30000, 40000 ) ), 6, 12 );

			$single_options = array(
				$this->ame_product_id_key          => $this->ame_software_product_id,
				$this->ame_instance_key            => $instance,
				$this->ame_deactivate_checkbox_key => 'on',
				$this->ame_activated_key           => 'Deactivated',
			);

			foreach ( $single_options as $key => $value ) {
				if ( ! get_option( $key ) ) {
					update_option( $key, $value );
				}
			}

		}

		/**
		 * Deletes all data if plugin deactivated
		 * @return void
		 */
		public function uninstall() {
			$this->license_key_deactivation();
			$this->clean_options();
		}

		/**
		 * Remove options
		 */
		public function clean_options() {

			/** @global int $blog_id */
			global $blog_id;


			if ( is_multisite() ) {
				switch_to_blog( $blog_id );
			}

			foreach (
				array(
					$this->ame_data_key,
					$this->ame_product_id_key,
					$this->ame_instance_key,
					$this->ame_deactivate_checkbox_key,
					$this->ame_activated_key,
					$this->ame_deactivate_checkbox,
				) as $option
			) {
				delete_option( $option );
			}

			if ( is_multisite() ) {
				restore_current_blog();
			}

		}

		/**
		 * Deactivates the license on the API server
		 * @return void
		 */
		public function license_key_deactivation() {

			$activation_status = get_option( $this->ame_activated_key );

			$api_email = $this->ame_options[ $this->ame_activation_email ];
			$api_key   = $this->ame_options[ $this->ame_api_key ];

			$args = array(
				'email'       => $api_email,
				'licence_key' => $api_key,
			);

			if ( $activation_status == 'Activated' && $api_key != '' && $api_email != '' ) {
				$this->key()->deactivate( $args ); // reset license key activation
			}
		}

		/**
		 * Displays an inactive notice when the software is inactive.
		 */
		public function notice_license_inactive() { ?>
			<?php if ( ! current_user_can( 'manage_options' ) ) {
				return;
			} ?>
			<?php if ( isset( $_GET['page'] ) && $this->ame_activation_tab_key === $_GET['page'] ) {
				return;
			} ?>
			<div id="message" class="error">
				<p>
					<strong><?php echo esc_html( $this->ame_software_product_id ); ?>: </strong>
					<?php printf( __( 'License has not been activated. %sClick here%s to enter the license key and get the updates.', 'wpglobus' ),
						'<a href="' .
						esc_url( admin_url( 'index.php?page=' . $this->ame_activation_tab_key ) ) . '">',
						'</a>' ); ?>
				</p>
			</div>
		<?php
		}

		/**
		 * Check for external blocking constant
		 * @return string
		 */
		public function check_external_blocking() {
			// show notice if external requests are blocked through the WP_HTTP_BLOCK_EXTERNAL constant
			if ( defined( 'WP_HTTP_BLOCK_EXTERNAL' ) && WP_HTTP_BLOCK_EXTERNAL === true ) {

				// check if our API endpoint is in the allowed hosts
				$host = parse_url( $this->upgrade_url, PHP_URL_HOST );

				if ( ! defined( 'WP_ACCESSIBLE_HOSTS' ) || stristr( WP_ACCESSIBLE_HOSTS, $host ) === false ) {
					?>
					<div class="error">
						<p><?php printf( __( '<b>Warning!</b> You\'re blocking external requests which means you won\'t be able to get %s updates. Please add %s to %s.', 'wpglobus' ), $this->ame_software_product_id, '<strong>' . $host . '</strong>', '<code>WP_ACCESSIBLE_HOSTS</code>' ); ?></p>
					</div>
				<?php
				}

			}
		}

	} // class

endif;

# --- EOF