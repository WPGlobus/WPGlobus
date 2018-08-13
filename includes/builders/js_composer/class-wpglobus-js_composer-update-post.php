<?php
/**
 * File: class-wpglobus-js_composer-update-post.php
 *
 * @package WPGlobus\Builders\JS_Composer
 * @author Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Update_Post.
 */
 
if ( ! class_exists( 'WPGlobus_Update_Post' ) ) : 
	
	class WPGlobus_Update_Post {
		
		/**
		 * Constructor.
		 */
		public function __construct() {
			
			/**
			 * @todo incorrect the saving post in extra languages with priority = 10
			 */
			add_filter( 'wp_insert_post_data', array( $this, 'filter__wp_insert_post_data' ), 100, 2 );

		}
		
		/**
		 * Callback for 'wp_insert_post_data'.
		 */
		public function filter__wp_insert_post_data( $data, $postarr ) {
		
			global $wpdb;
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d AND post_type = %s LIMIT 1", $postarr['ID'], $postarr['post_type'] ) );
			
			$fields = array();
			if ( isset($postarr['post_title']) ) {
				$fields['post_title'] = $postarr['post_title'];
			}
			
			if ( isset($postarr['post_content']) ) {
				$fields['post_content'] = $postarr['post_content'];
			}
			
			if ( isset($postarr['post_excerpt']) ) {
				$fields['post_excerpt'] = $postarr['post_excerpt'];
			}	
			
			$builder_language = WPGlobus::Config()->builder->get_language();
			
			if ( empty($builder_language) ) {
				$builder_language = $postarr[ WPGlobus::get_language_meta_key() ];
				/**
				 * @todo Add handling for incorrect value. Save to log?
				 */
			}
			
			foreach( $fields as $field=>$value ) { 

				$tr = array();
				
				foreach ( WPGlobus::Config()->enabled_languages as $lang ) :

					if ( $lang == $builder_language ) {
					
						$text = $value;
						if ( WPGlobus_Core::has_translations($value) ) {
							$text = WPGlobus_Core::text_filter($value, $lang , WPGlobus::RETURN_EMPTY);
						}
						if ( ! empty($text) ) {
							$tr[$lang] = $text;
						}
					} else {
						$text = WPGlobus_Core::text_filter($_post->$field, $lang , WPGlobus::RETURN_EMPTY);
						if ( ! empty($text) ) {
							$tr[$lang] = $text;
						}
					}

				endforeach;

				$data[$field] = WPGlobus_Utils::build_multilingual_string($tr);
				
			}
		
			return $data;

		}
		
	}
	
endif;

# --- EOF