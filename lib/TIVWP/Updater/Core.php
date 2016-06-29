<?php
/**
 * File: Core.php
 *
 * @package TIVWP\Updater
 */

/**
 * Class TIVWP_Updater_Core
 */
class TIVWP_Updater_Core {

	/**
	 * @var string
	 */
	const KEY_INTERNAL_ERROR = 'internal_error';

	/**
	 * @var string[]
	 */
	protected static $PERSISTENT_VARS = array( 'status', 'notifications', 'instance', 'licence_key', 'email' );

	/**
	 * @var string
	 */
	protected $status = 'Inactive';

	/**
	 * @var string[]
	 */
	protected $notifications = array();

	/**
	 * The loader's __FILE__ must be passed.
	 *
	 * @var string
	 */
	protected $plugin_file = '';

	/**
	 * @var string
	 */
	protected $product_id = '';

	/**
	 * @var string
	 */
	protected $url_product = '';

	/**
	 * @var string
	 */
	protected $licence_key = '';

	/**
	 * @var string
	 */
	protected $email = '';

	/**
	 * @var string
	 */
	protected $instance = '';

	/**
	 * @var string
	 */
	protected $plugin_name = '';

	/**
	 * @var string
	 */
	protected $slug = '';

	/**
	 * This is the current domain name. Set in the constructor.
	 *
	 * @var string
	 */
	private $platform = '';

	/**
	 * TIVWP_Updater_Core constructor.
	 */
	public function __construct() {

		// Domain name where the plugin instance is installed. No scheme.
		$this->platform = str_ireplace( array( 'http://', 'https://' ), '', home_url() );

		// Check For Plugin Updates
		$transient = 'update_plugins';
		add_filter( 'pre_set_site_transient_' . $transient, array(
			$this,
			'filter__pre_set_site_transient_update_plugins'
		) );

		// Check For Plugin Information to display on the update details page
		add_filter( 'plugins_api', array( $this, 'filter__plugins_api' ), 10, 3 );

		add_filter( 'upgrader_pre_download', array( $this, 'filter__upgrader_pre_download' ), 10, 3 );

		add_action( 'after_plugin_row', array( $this, 'action__after_plugin_row' ), 10, 3 );

		add_action( 'init', array( $this, 'action__init' ) );

		add_action( 'shutdown', array( $this, 'action__shutdown' ) );

	}

	/**
	 *
	 */
	public function action__init() {
		foreach ( self::$PERSISTENT_VARS as $key ) {
			$this->var_load( $key );
		}

		$this->maybe_generate_instance();

		$this->process_admin_requests();
	}

	/**
	 *
	 */
	public function action__shutdown() {
		foreach ( self::$PERSISTENT_VARS as $key ) {
			$this->var_save( $key );
		}
	}

	/**
	 * @param string $key
	 */
	protected function var_save( $key ) {
		if ( ! isset( $this->$key ) ) {
			return;
		}
		if ( $this->$key ) {
			update_option( $this->slug . '_' . $key, $this->$key, false );
		} else {
			delete_option( $this->slug . '_' . $key );
		}
	}

	/**
	 * @param string $key
	 */
	protected function var_load( $key ) {
		if ( ! isset( $this->$key ) ) {
			return;
		}
		$stored_value = get_option( $this->slug . '_' . $key, null );
		if ( null !== $stored_value ) {
			$this->$key = $stored_value;
		}
	}

	/**
	 * Show the input for the licence key
	 *
	 * @param string $plugin_file Path to the plugin file, relative to the plugins directory.
	 * @param array  $plugin_data An array of plugin data.
	 * @param string $status      Status of the plugin. Defaults are 'All', 'Active',
	 *                            'Inactive', 'Recently Activated', 'Upgrade', 'Must-Use',
	 *                            'Drop-ins', 'Search'.
	 */
	public function action__after_plugin_row( $plugin_file, $plugin_data, $status ) {
		if ( strtolower( basename( dirname( $plugin_file ) ) ) === strtolower( $this->slug ) ) {
			include( dirname( __FILE__ ) . '/View/html-key-input.php' );
		}
	}

	/**
	 * @param string $plugin_file
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setPluginFile( $plugin_file ) {
		$this->plugin_file = $plugin_file;

		$this->setSlug( str_replace( '.php', '', basename( $this->plugin_file ) ) );
		$this->setPluginName( basename( dirname( $this->plugin_file ) ) . '/' . $this->slug . '.php' );

		return $this;
	}

	/**
	 * @param string $plugin_name
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setPluginName( $plugin_name ) {
		$this->plugin_name = $plugin_name;

		return $this;
	}

	/**
	 * @param string $product_id
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setProductId( $product_id ) {
		$this->product_id = $product_id;

		return $this;
	}

	/**
	 * @param string $url_product
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setUrlProduct( $url_product ) {
		$this->url_product = $url_product;

		return $this;
	}

	/**
	 * @param string $licence_key
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setLicenceKey( $licence_key ) {
		$this->licence_key = $licence_key;

		return $this;
	}

	/**
	 * @param string $email
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setEmail( $email ) {
		$this->email = $email;

		return $this;
	}

	/**
	 * @param string $instance
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setInstance( $instance ) {
		$this->instance = $instance;

		return $this;
	}

	/**
	 * @param string $slug
	 *
	 * @return TIVWP_Updater_Core
	 */
	public function setSlug( $slug ) {
		$this->slug = $slug;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_status() {
		return $this->get_server_response( $this->url_status() );
	}

	/**
	 * @return array
	 */
	public function activate() {
		return $this->get_server_response( $this->url_activation() );
	}

	/**
	 * @return array
	 */
	public function deactivate() {
		return $this->get_server_response( $this->url_deactivation() );
	}

	/**
	 * Check for updates against the remote server.
	 *
	 * @see set_site_transient
	 *
	 * @param  mixed $transient
	 *
	 * @return mixed $transient
	 */
	public function filter__pre_set_site_transient_update_plugins( $transient ) {

		if ( empty( $transient->checked[ $this->plugin_name ] ) ) {
			return $transient;
		}

		$current_version = (string) $transient->checked[ $this->plugin_name ];

		$request_parameters = array(
			'request' => 'pluginupdatecheck',
			'slug'    => $this->slug,
			'version' => $current_version,
		);

		$response = $this->get_upgrade_api_response( $request_parameters );

		if ( isset( $response->new_version )
		     && version_compare( (string) $response->new_version, $current_version, '>' )
		) {
			$transient->response[ $this->plugin_name ] = $response;
		}

		return $transient;

	}

	/**
	 * @param bool|stdClass|array $result The result object or array. Default false.
	 * @param string              $action The type of information being requested from the Plugin Install API.
	 * @param stdClass            $args   Plugin API arguments.
	 *
	 * @return stdClass|bool $response or boolean false
	 */
	public function filter__plugins_api( $result, $action, $args ) {

		if ( empty( $action ) or $action !== 'plugin_information' ) {
			return $result;
		}

		if ( empty( $args->slug ) or $args->slug !== $this->slug ) {
			// Not our business
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

		$response = $this->get_upgrade_api_response( $request_parameters );


		// If everything is okay return the $response
		if ( isset( $response->sections ) ) {

			// Filter each section. Each section is a WP page, so their content should
			// go through `the_content` filter.
			// Use case: multilingual pages made with WPGlobus.
			foreach ( $response->sections as $section_name => $section_content ) {
				$response->sections[ $section_name ] =
					apply_filters( 'the_content', $section_content );
			}
//			if ( ! isset( $response->banners ) ) {
//				$response->banners['low'] =
//				$response->banners['high'] = '//woothemess3.s3.amazonaws.com/wp-updater-api/official-wc-extension-1544.png';
//			}

			$result = $response;
		}

		return $result;

	}

	/**
	 * When @see download_url is called, the temporary file is created with a wrong name.
	 * This filter renames it to the valid name, {plugin-slug}.zip
	 *
	 * @param bool|string $reply       Whether to bail without returning the package.
	 *                                 Default false.
	 * @param string      $package     The package file name.
	 * @param WP_Upgrader $wp_upgrader The WP_Upgrader instance.
	 *
	 * @return mixed|WP_Error|bool
	 */
	public function filter__upgrader_pre_download( $reply, $package, $wp_upgrader ) {

		/**
		 * There could be several instances of the Updater, one for each paid extension.
		 * So, we need to check if we are called for the correct extension.
		 */
		/** @noinspection PhpUndefinedFieldInspection */
		if ( isset( $wp_upgrader->skin->plugin_info['Name'] ) &&
		     $wp_upgrader->skin->plugin_info['Name'] === $this->product_id
		) {

			// This is the regular WP download. Creates a file in the temp folder,
			// with an ugly file name, in our case, because of the ugly download URL.
			$path_to_downloaded_plugin_zip = download_url( $package );

			// `is_string` means, no error
			if ( is_string( $path_to_downloaded_plugin_zip ) ) {

				// Rename to {plugin_slug}.zip, still in the temp folder
				$valid_path_to_plugin_zip = get_temp_dir() . $this->slug . '.zip';
				if ( file_exists( $valid_path_to_plugin_zip ) ) {
					unlink( $valid_path_to_plugin_zip );
				}
				if ( rename( $path_to_downloaded_plugin_zip, $valid_path_to_plugin_zip ) ) {
					// If renamed successfully, return the new file path
					$reply = $valid_path_to_plugin_zip;
				}
			}
		}

		return $reply;
	}

	/**
	 * @param array $request_parameters
	 *
	 * @return stdClass
	 */
	protected function get_upgrade_api_response( Array $request_parameters ) {
		$request_parameters = array_merge( array(
			'activation_email' => $this->email,
			'api_key'          => $this->licence_key,
			'domain'           => $this->platform,
			'instance'         => $this->instance,
			'plugin_name'      => $this->plugin_name,
			'product_id'       => $this->product_id,
		), $request_parameters );

		$url = add_query_arg( 'wc-api', 'upgrade-api', $this->url_product )
		       . '&' . http_build_query( $request_parameters );

		$response = wp_safe_remote_get( esc_url_raw( $url ) );

		// TODO check for errors


		$response_body = wp_remote_retrieve_body( $response );
		if ( is_serialized( $response_body ) ):

			$response_object = unserialize( $response_body );

			if ( is_object( $response_object ) ) {
				return $response_object;
			}
		endif;

		return new stdClass();

	}

	/**
	 * @param array $args
	 *
	 * @return string
	 */
	protected function build_url( Array $args ) {
		$args = array_merge( array(
			'product_id'  => $this->product_id,
			'instance'    => $this->instance,
			'email'       => $this->email,
			'licence_key' => $this->licence_key,
			'platform'    => $this->platform,
		), $args );

		return esc_url_raw(
			add_query_arg( 'wc-api', 'am-software-api', $this->url_product ) . '&' .
			http_build_query( $args )
		);
	}

	/**
	 * @return string
	 */
	protected function url_status() {
		return $this->build_url( array(
			'request' => 'status',
		) );
	}

	/**
	 * @return string
	 */
	protected function url_activation() {
		return $this->build_url( array(
			'request' => 'activation',
		) );
	}

	/**
	 * @return string
	 */
	protected function url_deactivation() {
		return $this->build_url( array(
			'request' => 'deactivation',
		) );
	}

	/**
	 * @param string $url Remote URL to access.
	 *
	 * @return array Response from the server.
	 */
	protected function get_server_response( $url ) {

		$result = wp_safe_remote_get( $url );
		if ( is_wp_error( $result ) ) {
			// TODO
			$error_message = '';

			$error_messages = $result->get_error_messages();
			if ( count( $error_messages ) ) {
				$error_message = implode( '; ', $error_messages );
			}

			$response_body = json_encode( array(
				self::KEY_INTERNAL_ERROR => implode( ' ', array(
					// TODO Languages.
					__( 'Licensing server connection error.', 'tivwp-updater' ),
					$error_message
				) ),
			) );

		} elseif ( 200 !== (int) wp_remote_retrieve_response_code( $result ) ) {

			$response_body = json_encode( array(
				self::KEY_INTERNAL_ERROR => implode( ' ', array(
					__( 'Licensing server connection error.', 'tivwp-updater' ),
					$result['response']['code'] . ' - ' . $result['response']['message']
				) ),
			) );
		} else {
			$response_body = wp_remote_retrieve_body( $result );
		}

		return json_decode( $response_body, JSON_OBJECT_AS_ARRAY );
	}

	/**
	 *
	 */
	protected function maybe_generate_instance() {
		if ( ! $this->instance ) {
			$this->instance = substr( sha1( site_url() . $_SERVER['HTTP_HOST'] . (string) mt_rand( 100, 999 ) ), 0, 12 );
		}
	}

	/**
	 *
	 */
	protected function process_admin_requests() {

		$key = 'licence_key';
		$_   = $this->slug . '_' . $key;
		if ( isset( $_POST[ $_ ] ) && is_string( $_POST[ $_ ] ) ) {
			$this->$key = $_POST[ $_ ];
		}

		$key = 'email';
		$_   = $this->slug . '_' . $key;
		if ( isset( $_POST[ $_ ] ) && is_email( $_POST[ $_ ] ) ) {
			$this->$key = $_POST[ $_ ];
		}

		$key = 'action';
		$_   = $this->slug . '_' . $key;
		if ( 1
		     && $this->licence_key
		     && $this->email
		     && isset( $_POST[ $_ ] )
		     && 'activate' === $_POST[ $_ ]
		) {
			$this->notification_clear_all();
			$activation_result = $this->activate();
			if ( ! empty( $activation_result['error'] ) ) {
				$this->notification_add( $activation_result['error'] );
			} elseif ( isset( $activation_result['activated'] )
			           && $activation_result['activated']
			) {
				$this->status = 'Active';
				$this->notification_add( $activation_result['message'] );
			}
		}

	}

	/**
	 * @param string $message
	 */
	protected function notification_add( $message ) {
		$this->notifications[] = $message;
	}

	/**
	 * Clear all notification messages.
	 */
	protected function notification_clear_all() {
		$this->notifications = array();
	}
}

/* EOF */
