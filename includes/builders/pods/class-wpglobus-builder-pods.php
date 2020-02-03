<?php
/**
 * File: class-wpglobus-builder-pods.php
 *
 * @since 2.3.0
 *
 * @package WPGlobus\Builders\Pods.
 * @author  Alex Gor(alexgff)
 */
 
if ( ! class_exists( 'WPGlobus_Builder_Pods' ) ) :

	/**
	 * Class WPGlobus_Builder_Pods.
	 */
	class WPGlobus_Builder_Pods {
		
		/**
		 * Default Pods post type.
		 */
		protected static $post_type = '_pods_pod';
		
		/**
		 * Type of Pods: `post_type` or `settings`.
		 */		
		protected static $pods_type = '';

		/**
		 * Get attributes.
		 */
		public static function get_attrs($attrs) {	
			
			if ( ! function_exists('pods_api') ) {
				return false;
			}
		
			/** @global string $pagenow */
			/** @global string $wpdb */
			global $pagenow, $wpdb;

			$result = true;

			if ( 'post.php' === $pagenow ) {

				/**
				 * @see pods\includes\classes.php
				 */
				$pods = pods_api()->load_pods( array( 'type' => 'post_type', 'fields' => true ) );
				
				self::$pods_type = 'post_type';
				
				$post_id = $attrs['post_id'];
				
				if ( $post_id > 0 ) {
				
					$pods_pod = $wpdb->get_results( $wpdb->prepare(
						"SELECT p.post_name AS name, p.ID, p.post_title AS title, p.post_parent AS parent, pm.meta_value AS type FROM $wpdb->posts as p 
							LEFT JOIN $wpdb->postmeta AS pm ON pm.post_id = p.ID WHERE p.post_type = %s AND p.post_status = 'publish' 
								AND pm.meta_key = 'type' AND pm.meta_value = %s",
						self::$post_type,
						self::$pods_type
					), OBJECT_K );
	
					if ( ! empty($pods_pod) ) {
						
						foreach ( $pods_pod as $_pod_key => $_pod ) :
						
							if ( $attrs['post_type'] != $_pod_key ) {
								continue;
							}
							
							if ( empty($pods[ $_pod->ID ]['fields']) ) {
								/**
								 * Pods_pod is attached to post type (e.g. for post) but has no fields.
								 */
								$result = false;
								break;
							}
							
						endforeach;
					}
				}

				if ( ! $result ) {
					return false;
				}
				
				$attrs['builder_page'] = true;
				return $attrs;
				
			} else {
				/**
				 * @todo W.I.P @since 2.3.0
				 */
				// 
				//if ( ! empty($_GET['page']) && false !== strpos( $_GET['page'], 'pods-settings' ) ) {
				//	$attrs['builder_page'] = true;	
				//	return $attrs;					
				//}
			}


			
			return false;
		}	
	}
	
endif;

# --- EOF