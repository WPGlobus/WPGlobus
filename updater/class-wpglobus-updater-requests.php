<?php
/**
 * Requests to remote server
 * @package WPGlobus/Updater
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

			$target_url = esc_url_raw( self::create_software_api_url( $args ) );

			$request = wp_remote_get( $target_url );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

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

			$request = wp_remote_get( $target_url );

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

			$request = wp_remote_get( $target_url );

			if ( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				return false;
			}

			$response = wp_remote_retrieve_body( $request );

			return $response;
		}

	} //class

endif;

# --- EOF