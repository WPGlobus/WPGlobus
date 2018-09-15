<?php
/**
 * File: class-wpglobus-config-builder.php
 *
 * @package WPGlobus\Builders
 * @author Alex Gor(alexgff)
 */

if ( ! class_exists('WPGlobus_Config_Builder') ) :

	class WPGlobus_Config_Builder {
		
		protected $id = false;
		
		protected $is_run = false;

		/**
		 * May be to use "wpglobus_language" meta (reserved in WPGlobus).
		 * @todo remove after test
		 */
		//protected $language_meta_key = '_wpglobus_builder_language';
		
		protected $language_cookie = 'wpglobus-builder-language';
		
		protected $attrs = array();
		
		protected $__class = null;
		
		protected $__builder_page = false;
		
		protected $__is_admin = false;
		
		protected $language = false;
		
		/**
		 * Constructor.
		 */
		public function __construct($init = true, $init_attrs = array()) {
			
			if ( isset( $init_attrs['default_language'] ) ) {
				$this->default_language = $init_attrs['default_language'];
			}
			
			if ( $init ) {

				require_once dirname( __FILE__ ).'/class-wpglobus-builders.php' ; 
				$builder = WPGlobus_Builders::get();
				
				$this->id = $builder['id'];
				unset( $builder['id'] );
				
				if ( $this->id ) {
				
					$this->attrs['version'] = null;
					
					foreach ( $builder as $key => $value ) {
						if ( 'class' == $key ) {
							$this->__class = $value;
						} elseif( 'builder_page' == $key ) {
							$this->__builder_page = $value;
						} elseif( 'is_admin' == $key ) {
							$this->__is_admin = $value;
						}
						$this->attrs[$key] = $value;
					}
					
					$this->attrs['language'] = $this->language = $this->get_language();
				
				} else {
					unset($this->attrs);
				}
				
			} else {

				require_once dirname( __FILE__ ).'/class-wpglobus-builders.php' ;
				$builder = WPGlobus_Builders::get(false);
				
			}
			
		}
		
		/**
		 * Try to run builder.
		 */
		public function maybe_run($builder = '', $set_run_flag = false) {
			
			//if ( defined('DOING_AJAX') && DOING_AJAX ) {
				//return false;	
			//}
			
			if ( ! $this->id ) {
				return false;
			}
			
			$check_run_flag = true;
			
			if ( is_bool($builder) ) {
				if ( $builder ) {
					// @todo 
				} else {
					$check_run_flag  = false;
					$set_run_flag = false;
				}
			}
			
			if ( $check_run_flag && $this->is_run ) {
				/**
				 * Don't run again.
				 */
				return false;
			}
			
			if ( '' == $builder ) {
				$builder = $this->id;
			}
			
			if ( ! $builder ) {
				return false;
			}
			
			if ( $builder !== $this->id ) {
				return false;
			}
			
			if ( $this->is_front() ) {
				if ( $set_run_flag ) {
					$this->is_run = true;
				}
				return true;
			}
	
			if ( $this->is_builder_page() ) {
				if ( $set_run_flag ) {
					$this->is_run = true;
				}
				return true;
			}

			return false;
			
		}
		
		/**
		 * Get attribute.
		 */
		public function get($attr = 'id') {
			if ( ! $this->id ) {
				return false;
			}
			if ( 'id' == $attr) {
				return $this->get_id();
			}
			if ( ! empty($this->attrs[$attr]) ) {
				return $this->attrs[$attr];
			}
			return false;
		}
		
		/**
		 * Set builder language.
		 */
		public function set_language($language = '') {
			if ( ! empty( $language ) ) {
				$this->attrs['language'] = $this->language = $language;
			}
		}
		
		/**
		 * Get builder language.
		 */
		public function get_language($post = '') {
			
			if ( ! $this->id ) {
				return false;
			}
		
			if ( ! $this->is_builder_page() ) {
				/**
				 * @todo may be need to check the coincidence value of $this->language with value of WPGlobus::Config()->language.
				 * @see Set language for builder in wpglobus\includes\class-wpglobus-config.php 
				 */
				return $this->language;
			}
			
			if ( $this->language ) {
				return $this->language;
			}
			
			$_id = false;
			
			if( '' == $post ) {
				global $post;
			}
		
			/**
			 * Get post as global object.
			 */
			if ( $post instanceof WP_Post ) {
				$_id = $post->ID;
			} else {
				$_id = (int) $post;
				if ( 0 == $_id ) {
					$_id = false;
				}
			}
			
			$language = false;
			if ( $_id ) {
				$language = get_post_meta( $_id, $this->get_language_meta_key(), true );
			}
			
			if ( ! $language ) {
				
				if ( empty($_REQUEST) ) {
				
					if ( empty($_SERVER['HTTP_REFERER']) )  {
						/**
						 * @todo front-end? check it.
						 */
						return;
						
					} elseif ( false !== strpos( $_SERVER['HTTP_REFERER'], 'language=' ) ) {
						$language = explode('language=', $_SERVER['HTTP_REFERER']);
						$language = $language[1];
					} 			
				
				} else {

					if ( ! empty( $_REQUEST['language'] ) ) { // WPCS: input var ok, sanitization ok.
						$language = sanitize_text_field($_REQUEST['language']);
					}
					
					if ( isset( $_REQUEST['wpglobus-language'] ) ) { // WPCS: input var ok, sanitization ok.
						$language = sanitize_text_field($_REQUEST['wpglobus-language']);
					}
					
				}
			}
			
			if ( ! $language ) {

				if ( isset( $_REQUEST['post'] ) && (int) $_REQUEST['post'] != 0 ) { // WPCS: input var ok, sanitization ok.

					$language = get_post_meta( $_REQUEST['post'], $this->get_language_meta_key(), true);
				
				} else if ( isset( $_REQUEST['id'] ) && (int) $_REQUEST['id'] != 0 ) { // WPCS: input var ok, sanitization ok.
				
					/**
					 * Case when post in draft status are autosaved.
					 */
					$language = get_post_meta( $_REQUEST['id'], $this->get_language_meta_key(), true);
					
				}
				
			}

			if ( ! $language && ! empty($this->default_language) ) {
				/**
				 * Possible options when the language is not defined:
				 * - new post, post-new.php page;
				 */
				$language = $this->default_language;
			}
			
			$this->language = $language;
			
			return $language;
			
		}
		
		/**
		 * Check if builder is run.
		 */		
		public function is_run() {
			if ( ! $this->id ) {
				return false;
			}
			return $this->is_run;
		}
		
		/**
		 * Check if builder is run.
		 */			
		public function is_running() {
			return $this->is_run();
		}

		/**
		 * Check if builder is in admin.
		 */
		public function is_admin() {
			if ( ! $this->id ) {
				return false;
			}
			return $this->__is_admin;
		}

		/**
		 * Check if builder is in front.
		 */		
		public function is_front() {
			if ( ! $this->id ) {
				return false;
			}
			return ! $this->__is_admin;
		}

		/**
		 * Get builder ID. 
		 */		
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get post ID. 
		 */		
		public function get_post_id() {
			if ( isset($this->attrs['post_id']) && (int) $this->attrs['post_id'] > 0 ) {
				return $this->attrs['post_id'];
			}
			return false;
		}		
		
		/**
		 * Get builder class. 
		 */		
		public function get_class() {
			if ( ! $this->id ) {
				return false;
			}
			return $this->__class;
		}

		/**
		 *
		 */		
		public function get_language_meta_key() {
			if ( ! $this->id ) {
				return false;
			}
			return WPGlobus::get_language_meta_key();
		}

		/**
		 *
		 */		
		public function get_cookie_name() {
			if ( ! $this->id ) {
				return false;
			}
			return $this->language_cookie;
		}
		
		/**
		 *
		 */		
		public function get_cookie($cookie_name = '') {
			
			if ( ! $this->id ) {
				return false;
			}

			static $_cookie_value = null;
			
			if ( is_null( $_cookie_value ) ) {
				if ( empty($cookie_name) ) {
					$cookie_name = $this->get_cookie_name();
				}
				if ( empty($_COOKIE[$cookie_name]) ) {
					$_cookie_value = false;
				} else {
					$_cookie_value = $_COOKIE[$cookie_name];
				}
			}

			return $_cookie_value;
		}
		
		/**
		 * Check if current page is bulder's page.
		 */		
		public function is_builder_page() {
			if ( ! $this->id ) {
				return false;
			}
			return $this->__builder_page;
		}
		
		/**
		 * Get all builder data.
		 */
		public function get_data() {
			
			if ( ! $this->id ) {
				return false;
			}

			$data = array();
			$data['id'] = $this->get_id();

			if ( empty( $data['id'] ) ) {
				return false;
			}			
			
			if ( ! empty($this->attrs) ) {
				foreach( $this->attrs as $key=>$value ) {
					$data[$key] = $value;
				}
			}
		
			$data['language'] = $this->get_language();
			
			return $data;
			
		}		
		
	}
	
endif;

# --- EOF