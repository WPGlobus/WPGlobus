<?php
/**
 * File: class-wpglobus-vendor-pods-front.php
 *
 * @since 2.3.0
 *
 * @package WPGlobus\Vendor\Pods.
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Vendor_Pods_Front.
 */

if ( ! class_exists( 'WPGlobus_Vendor_Pods_Front' ) ) :

	class WPGlobus_Vendor_Pods_Front {

		public static function controller() {

			/**
			 * @scope front.
			 */
			add_filter( 'wpglobus_multilingual_meta_keys', array( __CLASS__, 'filter__multilingual_meta_keys' ), 5 );
		}
		
		/**
		 * Specify meta keys where the meta data can be multilingual.
		 */
		public static function filter__multilingual_meta_keys( $multilingual_meta_keys ) {
			
			$pods = pods_api()->load_pods( array( 'type' => 'post_type', 'fields' => true ) );
			
			if ( empty( $pods ) ) {
				return $multilingual_meta_keys;
			}
			
			foreach( $pods as $key=>$pod ) {
				
				if ( ! empty( $pod ) ) {
					$_fields = $pod['fields'];
					if ( ! empty( $_fields ) ) {
						foreach( $_fields as $_key=>$_attrs ) {
							$multilingual_meta_keys[$_key] = true;
						}
					}
				}
			}

			return $multilingual_meta_keys;
		}	
		
	}
	
endif;

# --- EOF