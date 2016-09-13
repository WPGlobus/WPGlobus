<?php
/**
 * Requests to remote server
 * @package   WPGlobus\Updater
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Updater_Key' ) ) :

	/**
	 * Class WPGlobus_Updater_Key
	 */
	class WPGlobus_Updater_Key {

		/** @var  WPGlobus_Updater $WPGlobus_Updater */
		protected $WPGlobus_Updater;

		/**
		 * Load admin menu
		 *
		 * @param WPGlobus_Updater $WPGlobus_Updater
		 */
		public function __construct( WPGlobus_Updater $WPGlobus_Updater ) {

			$this->WPGlobus_Updater = $WPGlobus_Updater;

		}

		/**
		 * API Key URL
		 *
		 * @param array $args
		 *
		 * @return string
		 */
		public function create_software_api_url( $args ) {

			$api_url = add_query_arg( 'wc-api', 'am-software-api', $this->WPGlobus_Updater->upgrade_url );

			return $api_url . '&' . http_build_query( $args );
		}

		/**
		 * Send activation request to the API server.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function activate( $args ) {

			$defaults = array(
				'request'    => 'activation',
				'product_id' => $this->WPGlobus_Updater->ame_product_id,
				'instance'   => $this->WPGlobus_Updater->ame_instance_id,
				'platform'   => $this->WPGlobus_Updater->ame_domain,
			);

			$args = wp_parse_args( $defaults, $args );

			// Short-circuit on empty license.
			// Server response example:
			// {"error":"Invalid API License Key","code":"105","additional info":"No debug information available","activated":"inactive","timestamp":1465319450}
			if ( ! ( $args['email'] && $args['licence_key'] ) ) {
				$response = json_encode( array(
					'error'           => 'Invalid API License Key',
					'code'            => '105',
					'additional info' => esc_html__( 'The License Key / Email pair is empty or invalid.', 'wpglobus' ),
					'activated'       => 'inactive',
					'timestamp'       => time(),
				) );
			} else {

				$target_url = esc_url_raw( self::create_software_api_url( $args ) );

				$request = wp_safe_remote_get( $target_url );

				if ( is_wp_error( $request ) || (int) wp_remote_retrieve_response_code( $request ) !== 200 ) {
					// Request failed
					return false;
				}

				$response = wp_remote_retrieve_body( $request );
			}

			return $response;
		}

		/**
		 * Send deactivation request to the API server.
		 *
		 * @param array $args
		 *
		 * @return bool|string
		 */
		public function deactivate( $args ) {

			$defaults = array(
				'request'    => 'deactivation',
				'product_id' => $this->WPGlobus_Updater->ame_product_id,
				'instance'   => $this->WPGlobus_Updater->ame_instance_id,
				'platform'   => $this->WPGlobus_Updater->ame_domain
			);

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( self::create_software_api_url( $args ) );

			$request = wp_safe_remote_get( $target_url );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

		/**
		 * Checks if the software is activated or deactivated
		 *
		 * @param  array $args
		 *
		 * @return array
		 */
		public function status( $args ) {

			$defaults = array(
				'request'    => 'status',
				'product_id' => $this->WPGlobus_Updater->ame_product_id,
				'instance'   => $this->WPGlobus_Updater->ame_instance_id,
				'platform'   => $this->WPGlobus_Updater->ame_domain
			);

			$args = wp_parse_args( $defaults, $args );

			$target_url = esc_url_raw( self::create_software_api_url( $args ) );

			$request = wp_safe_remote_get( $target_url );

			if ( is_wp_error( $request ) ) {
				// Request failed

				$error_message = '';

				$error_messages = $request->get_error_messages();
				if ( count( $error_messages ) ) {
					$error_message = implode( '; ', $error_messages );
				}

				return json_encode( array(
					WPGlobus_Updater::KEY_INTERNAL_ERROR => implode( ' ', array(
						__( 'Licensing server connection error.', 'wpglobus' ),
						$error_message
					) ),
				) );

			}

			$response_code = (int) wp_remote_retrieve_response_code( $request );

			if ( 200 !== $response_code ) {
				return json_encode( array(
					WPGlobus_Updater::KEY_INTERNAL_ERROR => sprintf( __( 'Licensing server connection error (%d).', 'wpglobus' ), $response_code )
				) );
			}

			return wp_remote_retrieve_body( $request );
		}

	} //class

endif;

# --- EOF
