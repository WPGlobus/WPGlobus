<?php

/**
 * File: Core.php
 *
 * @package TIVWP\Updater
 */
class TIVWP_Updater_Core {

	/**
	 * @var string
	 */
	const KEY_INTERNAL_ERROR = 'internal_error';

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
	private $platform = '';

	/**
	 * TIVWP_Updater_Core constructor.
	 */
	public function __construct() {

		// Domain name where the plugin instance is installed. No scheme.
		$this->platform = str_ireplace( array( 'http://', 'https://' ), '', home_url() );
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
			'request'     => 'status',
		) );
	}

	/**
	 * @return string
	 */
	protected function url_activation() {
		return $this->build_url( array(
			'request'     => 'activation',
		) );
	}

	/**
	 * @return string
	 */
	protected function url_deactivation() {
		return $this->build_url( array(
			'request'     => 'deactivation',
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
}

/* EOF */
