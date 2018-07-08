<?php
/**
 * File: class-wpglobus-gutenberg-update-post.php
 *
 * @package WPGlobus\Builders\Gutenberg
 * @author Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Gutenberg_Update_Post.
 */
if ( ! class_exists( 'WPGlobus_Gutenberg_Update_Post' ) ) : 
	
	class WPGlobus_Gutenberg_Update_Post {
		
		protected $_prepared_post = null;
		
		/**
		 * Static "constructor".
		 */
		public function __construct() {
			
			/**
			 * @todo to save meta see request ($_REQUEST)
			 * post.php?post=259&action=edit&classic-editor=1&meta_box=1
			 */
			
			//if ( defined('DOING_AJAX') && DOING_AJAX ) {
			//}
			
			/**
			 * @see wp-includes\rest-api\endpoints\class-wp-rest-posts-controller.php
			 */			
			add_filter( 'rest_pre_insert_post', array( $this, 'filter__pre_insert_post' ), 2, 2);
			
			/**
			 * @todo incorrect the saving post in extra languages with priority = 10
			 */
			add_filter( 'wp_insert_post_data', array( $this, 'filter__wp_insert_post_data' ), 100, 2 );
			
			/**
			 * @see \wp-includes\rest-api\class-wp-rest-server.php
			 */
			add_filter( 'rest_request_after_callbacks', array( $this, 'filter__rest_after_callbacks' ), 10, 3 );
			 
		}
	
		/**
		 * Callback for 'rest_request_after_callbacks'.
		 */
		function filter__rest_after_callbacks($response, $handler, $request) {
			
			if ( ! empty($response->data['id']) ) {
				$post_id = $response->data['id'];
			} else {
				/**
				 * @todo What to do?
				 */
				return $response;		
			}
			
			$builder_language = get_post_meta($post_id, WPGlobus::Config()->builder->get_language_meta_key(), true);
			if ( empty($builder_language) ) {
				$builder_language = WPGlobus::Config()->default_language;
			}	

			$fix_title = true;
			if  ( ! empty($response->data['title']['raw']) && WPGlobus_Core::has_translations($response->data['title']['raw']) ) {
				$response->data['title']['raw'] 	 = WPGlobus_Core::text_filter($response->data['title']['raw'], $builder_language);
				$response->data['title']['rendered'] = $response->data['title']['raw'];
				$fix_title = false;
			}
			
			if  ( ! empty($response->data['excerpt']['raw']) && WPGlobus_Core::has_translations($response->data['excerpt']['raw']) ) {
				$excerpt_in_default = WPGlobus_Core::text_filter($response->data['excerpt']['raw'], WPGlobus::Config()->default_language);
				$excerpt 		    = WPGlobus_Core::text_filter($response->data['excerpt']['raw'], $builder_language);
				$response->data['excerpt']['raw'] 	   = $excerpt;
				$response->data['excerpt']['rendered'] = str_replace($excerpt_in_default, $excerpt, $response->data['excerpt']['rendered']);
			}
			
			if ( $builder_language == WPGlobus::Config()->default_language ) {
				return $response;
			}
			
			if ( $fix_title ) :
				/**
				 * Fix the title.
				 * When we have title with different value:
				 * $response->data[title][raw] => Русский заголовок
				 * $response->data[title][rendered] => English title
				 */
				if ( empty($response->data['title']) ) {
					return $response;
				}
				if ( empty($response->data['title']['rendered']) || empty($response->data['title']['raw']) ) {
					return $response;
				}
				
				if ( $response->data['title']['rendered'] != $response->data['title']['raw'] ) {
					$response->data['title']['rendered'] = $response->data['title']['raw'];
				}

			endif;
			
			return $response;
		}
		
		/**
		 * Callback for 'rest_pre_insert_post'.
		 */
		public function filter__pre_insert_post( $prepared_post, $request ) {
			
			global $wpdb;
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $prepared_post->ID ) );
			
			$fields = array();
			if ( ! empty($prepared_post->post_title) ) {
				$fields['post_title'] = $prepared_post->post_title;
			}
			
			if ( ! empty($prepared_post->post_content) ) {
				$fields['post_content'] = $prepared_post->post_content;
			}
			
			if ( ! empty($prepared_post->post_excerpt) ) {
				$fields['post_excerpt'] = $prepared_post->post_excerpt;
			}	
			
			$builder_language = WPGlobus::Config()->builder->get_language();
			
			if ( empty($builder_language) ) {
				$builder_language = WPGlobus::Config()->default_language;
				/**
				 * @todo Add handling for incorrect value. Save to log.
				 */
				// 
			}
			
			$_fields = array();
			
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

				$prepared_post->$field = WPGlobus_Utils::build_multilingual_string($tr);
				
			}
			
			$this->_prepared_post = clone $prepared_post;
	
			return $prepared_post;					

		}
		
		/**
		 * Callback for 'wp_insert_post_data'.
		 */
		public function filter__wp_insert_post_data( $data, $postarr ) {
		
			/**
			 * Check $this->_prepared_post was loaded with first XMLHttpRequest.
			 * @see Network tab in browser console.
			 */
			if ( ! is_object( $this->_prepared_post ) ) {
				return $data;
			}
			
			$_fields = array( 'post_title', 'post_content', 'post_excerpt' );
			foreach( $_fields as $_field ) {
				if ( ! empty($data[$_field]) && ! empty($this->_prepared_post->$_field) ) {
					$data[$_field] = $this->_prepared_post->$_field;
				}
			}

			return $data;
		}
		
	}
	
endif;

# --- EOF