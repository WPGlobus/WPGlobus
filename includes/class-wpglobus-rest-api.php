<?php
/**
 * File: class-wpglobus-rest-api.php
 *
 * @package WPGlobus
 * @since 2.5.7
 */

/**
 * Class WPGlobus_Rest_API
 */
if ( ! class_exists( 'WPGlobus_Rest_API' ) ) :
	
	class WPGlobus_Rest_API {
		
		/**
		 * The name of the WPGlobus field.
		 * 
		 * @since 2.5.10
		 * @var string
		 */
		const REST_FIELD_NAME = 'translation';
		
		/**
		 * Constructor.
		 */
		public static function construct() {
			
			/**
			 * @see wp-includes\rest-api.php
			 */			
			add_action( 'rest_api_init', array( __CLASS__, 'on__rest_api_init' ), 5 );
		}

		/**
		 * Preparing to serve an API request.
		 *
		 * @since 2.5.10
		 *
		 * @param WP_REST_Server $wp_rest_server Server object.
		 */
		public static function on__rest_api_init( WP_REST_Server $wp_rest_server ) {

			$public_post_types = self::get_public_post_types();
			
			foreach ( $public_post_types as $post_type ) {
				/**
				 * @see wp-includes\rest-api.php
				 */
				register_rest_field( 
					$post_type, 
					self::REST_FIELD_NAME, 
					array(
						'get_callback' => array( __CLASS__, 'rest_field__for_post' ),
					)
				);
			}			
			
		}
		
		/**
		 * Registers a new field.
		 *
		 *
		 * @param string|array $object_type Object(s) the field is being registered
		 *                                  to, "post"|"term"|"comment" etc.
		 * @param string       $attribute   The attribute name.
		 * @param array        $args {
		 *     Optional. An array of arguments used to handle the registered field.
		 *
		 *     @type callable|null $get_callback    Optional. The callback function used to retrieve the field value. Default is
		 *                                          'null', the field will not be returned in the response. The function will
		 *                                          be passed the prepared object data.
		 *     @type callable|null $update_callback Optional. The callback function used to set and update the field value. Default
		 *                                          is 'null', the value cannot be set or updated. The function will be passed
		 *                                          the model object, like WP_Post.
		 *     @type array|null $schema             Optional. The callback function used to create the schema for this field.
		 *                                          Default is 'null', no schema entry will be returned.
		 * }
		 */
		public static function rest_field__for_post( $object_type, $attribute, $args ) {

			if ( 'attachment' == $object_type['type'] ) {
				$_fields = array( 'title' );
				/**
				 * @W.I.P @since 2.5.10 Add description, caption, alternative text fields for attachment.
				 */
			} else {
				$_fields = array( 'title', 'content', 'excerpt' );
			}

			$response = array(
				'provider' 			=> 'WPGlobus',
				'version' 			=> WPGLOBUS_VERSION,
				'language' 			=> WPGlobus::Config()->language,
				'enabled_languages' => WPGlobus::Config()->enabled_languages,
				'languages' 		=> null
			);
			
			foreach( WPGlobus::Config()->enabled_languages as $_language ) {
				foreach( $_fields as $_field ) {
					if ( empty( $object_type[$_field]['raw'] ) ) {
						$response['languages'][$_language][$_field] = false;
					} else {
						$response['languages'][$_language][$_field] = WPGlobus_Core::has_translation( $object_type[$_field]['raw'], $_language );
					}
				}
			}
		
			return $response;
		}
		
		/**
		 * Returns an array with the public post types.
		 *
		 * @since 2.5.10
		 *
		 * @param string $output The output type to use.
		 *
		 * @return array Array with all the public post_types.
		 */
		public static function get_public_post_types( $output = 'names' ) {
			return get_post_types( array( 'public' => true ), $output );
		}
		
	} // class
	
endif;

# --- EOF