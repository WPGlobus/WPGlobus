<?php
/**
 * File: class-tivwp-updater
 *
 * @package TIVWP_Updater
 */

// This is to avoid the PHPStorm warning about multiple Updater classes in the project.
// Had to place it file-wide because otherwise PHPCS complains about improper class comment.
/* @noinspection PhpUndefinedClassInspection */

/**
 * Class TIVWP_Updater
 */
class TIVWP_Updater {

	/**
	 * Array key where response errors are stored.
	 *
	 * @var string
	 */
	const KEY_ERROR = 'error';

	/**
	 * Array key where additional response info is stored.
	 *
	 * @var string
	 */
	const KEY_ADDITIONAL_INFO = 'additional info';

	/**
	 * Active status.
	 *
	 * @var string
	 */
	const STATUS_ACTIVE = 'active';

	/**
	 * Inactive status.
	 *
	 * @var string
	 */
	const STATUS_INACTIVE = 'inactive';

	/**
	 * Variables that can be passed to the Constructor via `$args`.
	 *
	 * @var string[]
	 */
	const CONSTRUCTOR_VARS = 'plugin_file,product_id,url_product';

	/**
	 * Variables that need to be saved and restored from Options.
	 *
	 * @var string[]
	 */
	const PERSISTENT_VARS = 'status,notifications,instance,licence_key,email';

	/**
	 * Current status.
	 *
	 * @var string
	 */
	protected $status = self::STATUS_INACTIVE;

	/**
	 * Array of notification messages.
	 *
	 * @var string[]
	 */
	protected $notifications = array();

	/**
	 * The loader's __FILE__ must be passed to Constructor.
	 *
	 * @var string
	 */
	protected $plugin_file = '';

	/**
	 * The Product ID - must be passed to Constructor.
	 *
	 * @var string
	 */
	protected $product_id = '';

	/**
	 * The URL of the Product - must be passed to Constructor.
	 *
	 * @var string
	 */
	protected $url_product = '';

	/**
	 * The License Key - filled in the form.
	 *
	 * @var string
	 */
	protected $licence_key = '';

	/**
	 * The Email used to purchase the License - filled in the form.
	 *
	 * @var string
	 */
	protected $email = '';

	/**
	 * The Instance - generated on activation.
	 *
	 * @var string
	 */
	protected $instance = '';

	/**
	 * Plugin name - generated from `$plugin_file`.
	 *
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * Plugin slug - generated from `$plugin_file`.
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Getter for `slug`.
	 *
	 * @return string
	 */
	public function getSlug() {
		return $this->slug;
	}

	/**
	 * The current domain name.
	 *
	 * @var string
	 */
	protected $platform = '';

	/**
	 * TIVWP_Updater constructor.
	 *
	 * @param array $args To initialize class variables.
	 */
	public function __construct( array $args = array() ) {

		$this->_initialize_class_variables( $args );

		// Note: these settings are for a specific plugin, so cannot do it at the loader.
		$ok_to_run = true;

		// Continue only if there is no version control folder.
		if ( is_dir( dirname( $this->plugin_file ) . '/.git' ) ) {
			$ok_to_run = false;
		}

		/**
		 * Override the $ok_to_run value.
		 *
		 * @example
		 * <code>
		 *    add_filter( 'tivwp-updater-ok-to-run', '__return_true' );
		 * </code>
		 *
		 * @param bool          $ok_to_run The value.
		 * @param TIVWP_Updater $this      The Updater class instance.
		 */
		$ok_to_run = apply_filters( 'tivwp-updater-ok-to-run', $ok_to_run, $this );

		if ( $ok_to_run ) {
			$this->_set_hooks();
		}

	}

	/**
	 * Set the class variables with the values passed to the Constructor.
	 *
	 * @param array $args To initialize class variables.
	 */
	protected function _initialize_class_variables( array $args ) {
		foreach ( explode( ',', self::CONSTRUCTOR_VARS ) as $var_name ) {
			if ( isset( $args[ $var_name ] ) ) {
				/* @noinspection PhpVariableVariableInspection */
				$this->$var_name = $args[ $var_name ];
			}
		}

		// Slug and Name are generated from the Loader file.
		// Assuming that the loader file ends with `.php`.
		if ( $this->plugin_file ) {
			$this->slug        = str_replace( '.php', '', basename( $this->plugin_file ) );
			$this->plugin_name = basename( dirname( $this->plugin_file ) ) . '/' . $this->slug . '.php';
		}

		// Domain name where the plugin instance is installed. No scheme.
		$this->platform = str_ireplace( array( 'http://', 'https://' ), '', home_url() );

	}

	/**
	 * Setup filters and actions.
	 */
	protected function _set_hooks() {

		// Tell WP where to check for plugin updates.
		add_filter( 'pre_set_site_transient_update_plugins',
			array(
				$this,
				'filter__pre_set_site_transient_update_plugins',
			)
		);

		// Tell WP where to get the plugin information for the update details popup.
		add_filter( 'plugins_api', array( $this, 'filter__plugins_api' ), 10, 3 );

		add_action( 'init', array( $this, 'action__init' ) );

		add_action( 'shutdown', array( $this, 'action__shutdown' ) );

	}

	/**
	 * Hooked constructor.
	 */
	public function action__init() {
		foreach ( explode( ',', self::PERSISTENT_VARS ) as $key ) {
			$this->_var_load( $key );
		}

		/**
		 * These two methods will work only if there is no instance yet.
		 * Migration should be tried first, and then - generate a new instance.
		 */
		$this->_migration();
		$this->_maybe_generate_instance();

		$this->_process_admin_requests();

		/**
		 * This action will display the License Management Form after the plugin row.
		 * We need to show it only when there is no information about the upgrade
		 * already in the transient.
		 * If we show the form, and WP shows it's "Update available" row, then
		 * the AJAX and the "updating" spinning icon won't work, and the upgrade will
		 * process in the background not informing the user about the results.
		 */
		$transient = get_site_transient( 'update_plugins' );
		if ( ! isset( $transient->response[ $this->plugin_name ] ) ) {
			add_action( 'after_plugin_row', array( $this, 'action__after_plugin_row' ) );
		}

	}

	/**
	 * Hooked destructor.
	 */
	public function action__shutdown() {
		foreach ( explode( ',', self::PERSISTENT_VARS ) as $key ) {
			$this->_var_save( $key );
		}
	}

	/**
	 * Save a class variable to the Options.
	 *
	 * @param string $key The variable name.
	 */
	protected function _var_save( $key ) {
		/* @noinspection PhpVariableVariableInspection */
		if ( ! isset( $this->$key ) ) {
			return;
		}
		/* @noinspection PhpVariableVariableInspection */
		if ( $this->$key ) {
			/* @noinspection PhpVariableVariableInspection */
			update_option( $this->slug . '_' . $key, $this->$key, false );
		}

		/** DO NOT DO IT. This may delete options on "fatal" errors' shutdown.
		 * <code>
		 * else {
		 *  delete_option( $this->slug . '_' . $key );
		 *  }
		 * </code>
		 */
	}

	/**
	 * Load a class variable from the Options.
	 *
	 * @param string $key The variable name.
	 */
	protected function _var_load( $key ) {
		/* @noinspection PhpVariableVariableInspection */
		if ( ! isset( $this->$key ) ) {
			return;
		}
		$stored_value = get_option( $this->slug . '_' . $key, null );
		if ( null !== $stored_value ) {
			/* @noinspection PhpVariableVariableInspection */
			$this->$key = $stored_value;
		}
	}

	/**
	 * Show the input for the licence key - only if the update is not available.
	 *
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 */
	public function action__after_plugin_row( $plugin_file ) {
		if ( strtolower( basename( dirname( $plugin_file ) ) ) === strtolower( $this->slug ) ) {
			include dirname( __FILE__ ) . '/includes/license-management-form.php';
		}
	}

	/**
	 * Get status from the Licensing server.
	 *
	 * @return array The response array.
	 */
	public function get_status() {
		return $this->_get_server_response( $this->_url_status() );
	}

	/**
	 * Send the "activate" request to the Licensing server.
	 *
	 * @return array The response array.
	 */
	public function activate() {
		return $this->_get_server_response( $this->_url_activation() );
	}

	/**
	 * Send the "deactivate" request to the Licensing server.
	 *
	 * @return array The response array.
	 */
	public function deactivate() {
		return $this->_get_server_response( $this->_url_deactivation() );
	}

	/**
	 * Check for updates against the Licensing server.
	 * The server will respond with a data structure matching WP's transient
	 * and tell whether an upgrade is available and how to get it.
	 *
	 * @see set_site_transient
	 *
	 * @param  mixed $transient The value of site transient.
	 *
	 * @return mixed $transient Updated value of site transient.
	 */
	public function filter__pre_set_site_transient_update_plugins( $transient ) {

		if (
			// Not our business?
			empty( $transient->checked[ $this->plugin_name ] )
			// Do we have data?
			|| ! $this->_is_license_pair_filled_in()
		) {
			return $transient;
		}

		$current_version = (string) $transient->checked[ $this->plugin_name ];

		$request_parameters = array(
			'request' => 'pluginupdatecheck',
			'slug'    => $this->slug,
			'version' => $current_version,
		);

		$response = $this->_get_upgrade_api_response( $request_parameters );

		if ( isset( $response->new_version )
			 && version_compare( (string) $response->new_version, $current_version, '>' )
		) {
			$transient->response[ $this->plugin_name ] = $response;
		}

		return $transient;

	}

	/**
	 * Call the Licensing server to get the information about plugin.
	 * The response will have all the info expected by WP to show in the "details" popup.
	 *
	 * @param bool|stdClass|array $result The result object or array. Default false.
	 * @param string              $action The type of information being requested from the Plugin Install API.
	 * @param stdClass            $args   Plugin API arguments.
	 *
	 * @return stdClass|bool $response or boolean false
	 */
	public function filter__plugins_api( $result, $action, $args ) {

		if ( empty( $action ) || 'plugin_information' !== $action ) {
			return $result;
		}

		if ( empty( $args->slug ) || $args->slug !== $this->slug ) {
			// Not our business.
			return $result;
		}

		$transient = get_site_transient( 'update_plugins' );

		if ( empty( $transient->checked[ $this->plugin_name ] ) ) {
			return $result;
		}

		$current_version = (string) $transient->checked[ $this->plugin_name ];

		$request_parameters = array(
			'request'          => 'plugininformation',
			'version'          => $current_version,
			'software_version' => $current_version,
		);

		$response = $this->_get_upgrade_api_response( $request_parameters );

		// If everything is okay return the $response.
		if ( isset( $response->sections ) ) {

			foreach ( (array) $response->sections as $section_name => $section_content ) {
				if ( ! $response->sections[ $section_name ] ) {
					// Remove empty sections.
					unset( $response->sections[ $section_name ] );
				} else {
					// Filter each section. Each section is a WP page, so their content should
					// go through `the_content` filter.
					// Use case: multilingual pages made with WPGlobus.
					$response->sections[ $section_name ] =
						apply_filters( 'the_content', $section_content );
				}
			}
			/**
			 * Example of how to set banners.
			 *
			 * <code>
			 * if ( ! isset( $response->banners ) ) {
			 *  $response->banners['low'] =
			 *   $response->banners['high'] = '//woothemess3.s3.amazonaws.com/wp-updater-api/official-wc-extension-1544.png';
			 *   }
			 * </code>
			 */

			// If after the cleanup, there are no sections left, show at least something.
			if ( ! count( $response->sections ) ) {
				$response->sections['other_notes'] =
					'<p>' .
					esc_html__(
						'Please visit the plugin page for more information',
						'tivwp-updater'
					) . ':</p><p><a href="' . $this->url_product . '">' .
					$this->url_product . '</a></p>';
			}

			add_action( 'admin_footer', array( $this, 'fix_info_sections_style' ) );

			$result = $response;
		}

		return $result;

	}

	/**
	 * Fix CSS for the plugin information popup.
	 *
	 * It's displayed in IFRAME, so we cannot add the rules to our general CSS and
	 * have to hook to the `admin_footer` instead.
	 *
	 * Fix 1.
	 * In `list-tables.css`:
	 * <code>
	 * .plugin-install-php h2 {
	 *  clear: both;
	 * }
	 * </code>
	 * forces the H2 line jumping down, after the `.fyi` section at the right.
	 *
	 * Fix 2.
	 * Images must not go wider than their container.
	 */
	public function fix_info_sections_style() {
		?>
		<style id="tivwp-updater-fix-info-css">
			#section-holder .section h2 {
				clear: none;
			}

			#section-holder .section img {
				max-width: 100%
			}
		</style>
		<?php
	}

	/**
	 * Send the API Upgrade request to the Licensing server.
	 *
	 * @param array $request_parameters The request parameters.
	 *
	 * @return stdClass The response.
	 */
	protected function _get_upgrade_api_response( array $request_parameters ) {
		$request_parameters = array_merge( array(
			'activation_email' => $this->email,
			'api_key'          => $this->licence_key,
			'domain'           => $this->platform,
			'instance'         => $this->instance,
			'plugin_name'      => $this->plugin_name,
			'product_id'       => $this->product_id,
		), $request_parameters );

		$url = add_query_arg( 'wc-api', 'upgrade-api', $this->url_product ) . '&' .
			   http_build_query( $request_parameters, '', '&' );

		$result = wp_safe_remote_get( esc_url_raw( $url ) );

		/**
		 * If error, emulate response and show error message.
		 */
		if (
			is_wp_error( $result )
			|| ( 200 !== (int) wp_remote_retrieve_response_code( $result ) )
		) {

			$error_message = '<h3>' .
							 esc_html__( 'Licensing server connection error.', 'tivwp-updater' ) .
							 '</h3>';

			/* @noinspection NotOptimalIfConditionsInspection */
			if ( is_wp_error( $result ) ) {
				/**
				 * We are here if there was a "hard" error
				 * (eq no connection to the server).
				 */
				$error_messages = $result->get_error_messages();
				if ( count( $error_messages ) ) {
					$error_message .= '<p>' . implode( '</p><p>', $error_messages ) . '</p>';
				}
			} elseif ( isset(
				$result['response']['code'],
				$result['response']['message']
			)
			) {
				/**
				 * This is a case of 404 or other "soft" errors.
				 */
				$error_message .= $result['response']['code'] . ' - ' . $result['response']['message'];
			}

			$response_object           = new stdClass();
			$response_object->name     = $this->product_id;
			$response_object->slug     = $this->slug;
			$response_object->homepage = $this->url_product;
			$response_object->sections = array(
				'other_notes' => $error_message,
			);

			return $response_object;
		}

		/**
		 * This is the "OK" place. Still need to make sure we've got a serialized object.
		 */

		$response_body = wp_remote_retrieve_body( $result );
		if ( is_serialized( $response_body ) ) :

			$response_object = unserialize( $response_body );

			if ( is_object( $response_object ) ) {
				/**
				 * Patches for the WC API Manager's response.
				 * ------------------------------------------
				 */

				/**
				 * WC API Manager returns empty string.
				 */
				$response_object->homepage = $this->url_product;

				/**
				 * WC API Manager returns the {folder}/{loader}.
				 * WP wants the folder only to show the `update_available` status.
				 *
				 * @see install_plugin_install_status for the algorithm.
				 */
				$response_object->slug = $this->slug;

				return $response_object;
			}
		endif;

		/**
		 * And this is an "impossible" case. WP will show a default error message.
		 */
		return new stdClass();
	}

	/**
	 * Helper method to build the URL for the `am-software-api` Licensing server requests.
	 *
	 * @param array $args Request parameters.
	 *
	 * @return string The response.
	 */
	protected function _build_url( array $args ) {
		$args = array_merge( array(
			'product_id'  => $this->product_id,
			'instance'    => $this->instance,
			'email'       => $this->email,
			'licence_key' => $this->licence_key,
			'platform'    => $this->platform,
		), $args );


		$url = add_query_arg( 'wc-api', 'am-software-api', $this->url_product ) . '&' .
			   http_build_query( $args, '', '&' );

		if ( class_exists( 'TIVWP_Debug_Bar' ) ) {
			TIVWP_Debug_Bar::print_link( $url );
		}

		return esc_url_raw( $url );
	}

	/**
	 * Helper method to build the "status" request URL.
	 *
	 * @return string The URL.
	 */
	protected function _url_status() {
		return $this->_build_url( array(
			'request' => 'status',
		) );
	}

	/**
	 * Helper method to build the "activation" request URL.
	 *
	 * @return string The URL.
	 */
	protected function _url_activation() {
		return $this->_build_url( array(
			'request' => 'activation',
		) );
	}

	/**
	 * Helper method to build the "deactivation" request URL.
	 *
	 * @return string The URL.
	 */
	protected function _url_deactivation() {
		return $this->_build_url( array(
			'request' => 'deactivation',
		) );
	}

	/**
	 * Send the `am-software-api` Licensing server requests.
	 *
	 * @param string $url Remote URL to access.
	 *
	 * @return array Response from the server.
	 */
	protected function _get_server_response( $url ) {

		if ( ! $this->_is_license_pair_filled_in() ) {
			$response_body = wp_json_encode( array(
				self::KEY_ERROR =>
					__( 'License / email is empty or invalid.', 'tivwp-updater' ),
			) );

		} else {
			$result = wp_safe_remote_get( $url );
			if ( is_wp_error( $result ) ) {

				$error_message = '';

				$error_messages = $result->get_error_messages();
				if ( count( $error_messages ) ) {
					$error_message = implode( '; ', $error_messages );
				}

				$response_body = wp_json_encode( array(
					self::KEY_ERROR => implode( ' ', array(
						__( 'Licensing server connection error.', 'tivwp-updater' ),
						$error_message,
					) ),
				) );

			} elseif ( 200 !== (int) wp_remote_retrieve_response_code( $result ) ) {

				$response_body = wp_json_encode( array(
					self::KEY_ERROR => implode( ' ', array(
						__( 'Licensing server connection error.', 'tivwp-updater' ),
						$result['response']['code'] . ' - '
						. $result['response']['message'],
					) ),
				) );
			} else {
				$response_body = wp_remote_retrieve_body( $result );
			}
		}

		// The JSON_OBJECT_AS_ARRAY constant exists since PHP 5.4
		return json_decode( $response_body, true );
	}

	/**
	 * Generate a new instance if not set yet.
	 */
	protected function _maybe_generate_instance() {
		if ( ! $this->instance ) {
			$this->instance = substr( sha1( site_url() . (string) mt_rand( 100, 999 ) ), 0, 12 );

			/**
			 * If a new instance has been generated, we must update the state.
			 * Updating must be hooked, and not run here, when we are called from
			 * the Constructor.
			 */
			$this->_reset_state();
			add_action( 'admin_init', array( $this, 'update_state' ) );
		}
	}

	/**
	 * Process the "License Form" submission.
	 *
	 * @todo nonce.
	 */
	protected function _process_admin_requests() {
		// @codingStandardsIgnoreStart
		if ( empty( $_POST ) ) {
			return;
		}
		$form_data = $_POST;
		// @codingStandardsIgnoreEnd

		/**
		 * 1. Get the form input values and update the class variables.
		 */

		$key = 'licence_key';
		$_sk = $this->slug . '_' . $key;
		if ( isset( $form_data[ $_sk ] ) && is_string( $form_data[ $_sk ] ) ) {
			/* @noinspection PhpVariableVariableInspection */
			$this->$key = $form_data[ $_sk ];
		}

		$key = 'email';
		$_sk = $this->slug . '_' . $key;
		if ( isset( $form_data[ $_sk ] ) && is_email( $form_data[ $_sk ] ) ) {
			/* @noinspection PhpVariableVariableInspection */
			$this->$key = $form_data[ $_sk ];
		}

		/**
		 * 2. Do some action, depending on which button was pressed.
		 */

		$key = 'action';
		$_sk = $this->slug . '_' . $key;
		if ( 1
			 && $this->licence_key
			 && $this->email
			 && isset( $form_data[ $_sk ] )

		) {
			if ( 'activate' === $form_data[ $_sk ] ) {
				$this->_try_to_activate();
			} elseif ( 'deactivate' === $form_data[ $_sk ] ) {
				$this->_try_to_deactivate();
			} elseif ( 'status' === $form_data[ $_sk ] ) {
				$this->_try_to_get_status();
			}
		}

	}

	/**
	 * Reset the state: clear notification and the transient.
	 */
	protected function _reset_state() {
		$this->_notification_clear_all();
		delete_site_transient( 'update_plugins' );
	}

	/**
	 * Public wrapper to use in callbacks.
	 */
	public function update_state() {
		$this->_try_to_get_status();
	}

	/**
	 * Set notifications to a new value(s), cleaning all existing.
	 *
	 * @param string|string[] $message Message or array of messages.
	 */
	protected function _notifications_set( $message = '' ) {
		$this->notifications = (array) $message;
	}

	/**
	 * Add a message to the array of notifications.
	 *
	 * @param string $message The message.
	 */
	protected function _notification_add( $message ) {
		$this->notifications[] = $message;
	}

	/**
	 * Clear all notification messages.
	 */
	protected function _notification_clear_all() {
		$this->notifications = array();
	}

	/**
	 * Migrate options from the old WPGlobus Updater.
	 * Works only if there is no instance yet.
	 */
	protected function _migration() {

		if ( $this->instance ) {
			return;
		}

		/**
		 * Example of the old options. (slug is `wpglobus_plus`)
		 *
		 * <code>
		 * wpgupd_wpglobus_plus_act
		 * Activated
		 *
		 * wpgupd_wpglobus_plus_data
		 * a:2:{s:24:"wpgupd_wpglobus_plus_api";s:38:"wc_order_****";
		 * s:37:"wpgupd_wpglobus_plus_activation_email";s:20:"email@example.com";}
		 *
		 * wpgupd_wpglobus_plus_dea_cb_key
		 * on
		 *
		 * wpgupd_wpglobus_plus_inst
		 * d91ae12*****
		 *
		 * wpgupd_wpglobus_plus_pid
		 * WPGlobus Plus
		 * </code>
		 */

		// Options prefix - code snipped copied from the WPGlobus Updater.
		$prefix = $this->product_id;
		$prefix = strtolower( $prefix );
		$prefix = preg_replace( '/[^%a-z0-9 _-]/', '', $prefix );
		$prefix = preg_replace( '/[\s-_]+/', '_', $prefix );
		$prefix = trim( $prefix, '_' );
		$prefix = 'wpgupd_' . $prefix;

		// Migrate instance.
		$_old_instance = get_option( $prefix . '_inst' );
		if ( $_old_instance ) {
			$this->instance = $_old_instance;
		}

		// Migrate license and email (serialized in `_data`).
		$_old_data = get_option( $prefix . '_data' );
		if ( ! $this->licence_key && ! empty( $_old_data[ $prefix . '_api' ] ) ) {
			$this->licence_key = $_old_data[ $prefix . '_api' ];
		}
		if ( ! $this->email && ! empty( $_old_data[ $prefix . '_activation_email' ] ) ) {
			$this->email = $_old_data[ $prefix . '_activation_email' ];
		}

		// Update status after migration.
		$this->_try_to_get_status();

	}

	/**
	 * Try to activate and update status.
	 */
	protected function _try_to_activate() {
		$this->_reset_state();
		$result = $this->activate();
		if ( ! empty( $result[ self::KEY_ERROR ] ) ) {
			$this->_notification_add( $result[ self::KEY_ERROR ] );
		} elseif ( isset( $result['activated'] )
				   && $result['activated']
		) {
			$this->status = self::STATUS_ACTIVE;
			$this->_notification_add( $result['message'] );
		}
	}

	/**
	 * Try to deactivate and update status.
	 */
	protected function _try_to_deactivate() {
		$this->_reset_state();
		$result = $this->deactivate();
		if ( ! empty( $result[ self::KEY_ERROR ] ) ) {
			$this->_notification_add( $result[ self::KEY_ERROR ] );
			if ( ! empty( $result[ self::KEY_ADDITIONAL_INFO ] ) ) {
				$this->_notification_add( $result[ self::KEY_ADDITIONAL_INFO ] );
			}
			/**
			 * If the server returns status "inactive" then we assume that the
			 * combination "instance-key-email" is not in the database.
			 * In that case, let's set the local status as inactive.
			 * Otherwise, all fields are disabled and the user is "stuck".
			 *
			 * @since 1.0.7
			 */
			if ( isset( $result['activated'] ) && self::STATUS_INACTIVE === $result['activated'] ) {
				$this->status = self::STATUS_INACTIVE;
			}
		} elseif ( isset( $result['deactivated'] )
				   && $result['deactivated']
		) {
			$this->status = self::STATUS_INACTIVE;
			if ( ! empty( $result['activations_remaining'] ) ) {
				$this->_notification_add( $result['activations_remaining'] );
			}
		}
	}

	/**
	 * Get status from the Licensing server and update the class variables.
	 */
	protected function _try_to_get_status() {

		$result = $this->get_status();
		if ( ! empty( $result[ self::KEY_ERROR ] ) ) {
			$this->status = self::STATUS_INACTIVE;
			$this->_notifications_set( $result[ self::KEY_ERROR ] );
		} elseif ( ! empty( $result['status_check'] ) ) {
			$this->status = $result['status_check'];
			if ( ! empty( $result['activations_remaining'] ) ) {
				$this->_notifications_set( $result['activations_remaining'] );
			}
		}
	}

	/**
	 * Check if Licence Key and Email are filled in.
	 *
	 * @return bool True if both are not empty.
	 */
	protected function _is_license_pair_filled_in() {
		return ( $this->licence_key && $this->email );
	}
}
