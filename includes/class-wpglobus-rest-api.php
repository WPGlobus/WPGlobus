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
		 * Constructor.
		 */
		public static function construct() {
			
			/**
			 * @see wp-includes\rest-api.php
			 */
			register_rest_field( 
				'post', 
				'translation', 
				array(
					'get_callback' => array( __CLASS__, 'on__register_rest_field' ),
				)
			);			
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
		public static function on__register_rest_field( $object_type, $attribute, $args ) {
			
			$response = array(
				'provider' 			=> 'WPGlobus',
				'version' 			=> WPGLOBUS_VERSION,
				'language' 			=> WPGlobus::Config()->language,
				'enabled_languages' => WPGlobus::Config()->enabled_languages,
				'languages' 		=> null
			);
			
			$_fields = array( 'title', 'content', 'excerpt' );
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
	} // class
	
endif;

# --- EOF