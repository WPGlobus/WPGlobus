<?php
/**
 * File: class-wpglobus-builders.php
 *
 * @package WPGlobus\Builders
 * @author Alex Gor(alexgff)
 */
 
/**
 * Class WPGlobus_Builders.
 */
if ( ! class_exists('WPGlobus_Builders') ) :

	class WPGlobus_Builders {
		
		protected static $attrs = array();
		
		public static function get($init = true) {
			
			/*
			$meta_type = 'post';
			add_filter( "update_{$meta_type}_metadata", array( __CLASS__, 'on__update_metadata' ), 5, 5 );
			// */
			
			self::$attrs = array(
				'id'			=> false,
				'version' 		=> '',
				'class'   		=> '',
				'post_type'		=> '',
				'is_admin' 		=> true,
				'builder_page' 	=> false,
				'language'		=> '',
				'message'		=> ''
			);
			
			if ( $init ) {
			
				$builder = false;
				
				$builder = self::is_gutenberg();
				if ( $builder ) {
					return $builder;
				}
				
				//*
				$builder = self::is_js_composer();
				if ( $builder ) {
					return $builder;
				}
				// */

				/*
				$builder = self::is_elementor();
				if ( $builder ) {
					return $builder;
				}
				// */
				
				/*
				$builder = self::is_siteorigin_panels();
				if ( $builder ) {
					return $builder;
				}
				// */
				
			}
			
			return self::$attrs;

		}
	
		/**
		 *
		 */
		public static function on__update_metadata( $check, $object_id, $meta_key, $meta_value, $prev_value ) {
			
			//error_log(print_r('HERE on__update_metadata : '.$meta_key, true));

			
			if ( 'panels_data' == $meta_key ) {
				
				//error_log(print_r($meta_key, true));
				//error_log(print_r($this->language, true));
			}
			
			return $check;
		}
		
		/**
		 * @see https://wordpress.org/plugins/siteorigin-panels/
		 */
		protected static function is_siteorigin_panels() {
	
			if ( ! defined('SITEORIGIN_PANELS_VERSION') ) {
				return false;
			}
			
			if ( ! is_admin() ) {
				$_attrs = array(
					'version' 		=> SITEORIGIN_PANELS_VERSION,
					'class'   		=> 'WPGlobus_Siteorigin_Panels',
					'is_admin' 		=> false
				);
				$attrs = self::get_attrs($_attrs);
				return new WPGlobus_Config_Builder('siteorigin_panels', $attrs);				
			}
			
			global $pagenow;
			
			if ( 'post.php' == $pagenow ) {
				
				$_attrs = array(
					'version' 		=> SITEORIGIN_PANELS_VERSION,
					'class'   		=> 'WPGlobus_Siteorigin_Panels',
					'builder_page' 	=> true
				);
				
				//if ( in_array( $post_type, $cpt_support ) ) {
					//$attrs['builder_page'] = true;
				//}
			
				$attrs = self::get_attrs($_attrs);
			
				return new WPGlobus_Config_Builder('siteorigin_panels', $attrs);
			
			}
			
			return false;
			
		}
		
		/**
		 * @see https://wordpress.org/plugins/elementor/
		 */
		protected static function is_elementor() {
			
			if ( ! defined('ELEMENTOR_VERSION') ) {
				return false;
			}
			
			global $pagenow;
			
			if ( 'post.php' == $pagenow ) {
				
				/**
				 * $cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );
				 * @see elementor\includes\plugin.php
				 */
				$cpt_support = get_option( 'elementor_cpt_support', [ 'page', 'post' ] );			
				
				$post_type = self::get_post_type($_GET['post']);
				
				$attrs = array(
					'version' 		=> ELEMENTOR_VERSION,
					'class'   		=> 'WPGlobus_Elementor',
					'builder_page' 	=> false
				);
				
				if ( in_array( $post_type, $cpt_support ) ) {
					$attrs['builder_page'] = true;
				}
				
				//return new WPGlobus_Config_Builder('elementor', $attrs);
				return false;
			}
			
			return false;
			
		}
		
		/**
		 * @see WPBakery Page Builder.
		 */
		protected static function is_js_composer() {
			
			if ( ! defined('WPB_VC_VERSION') ) {
				return false;
			}

			global $pagenow;
			
			/** @global wpdb $wpdb */
			global $wpdb;
			
			if ( 'post.php' == $pagenow ) {
				
				$_builder_page = true;
				
				/**
				 * @see vc_editor_post_types() (js_composer\include\helpers\helpers_api.php) doesn't work here.
				 * so let's get 'wp_user_roles' option.
				 */
				$_opts = get_option( 'wp_user_roles' );

				if ( ! function_exists('wp_get_current_user') ) {
					require_once( ABSPATH . WPINC . '/pluggable.php' );
				}
				
				$_user = wp_get_current_user();
				
				$post_id = WPGlobus_Utils::safe_get('post');
				
				$_post_type = $wpdb->get_col( $wpdb->prepare("SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id) );
				
				$post_type = '';
				if ( ! empty($_post_type[0]) ) {
					$post_type = $_post_type[0];
				}

				if ( '' == $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * All post types are disabled in WPBakery Page Builder.
					 */
					$_builder_page = false; 

				} else if ( '1' == $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * WPBakery Page Builder is available for pages only.
					 */
					if ( $post_type != 'page' ) {
						$_builder_page = false;
					}

				} else if ( 'custom' == $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * Custom settings for post types in WPBakery Page Builder.
					 */					
					if ( ! empty( $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types/'.$post_type] ) 
							&& '1' == $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types/'.$post_type] ) {
						
						$_builder_page = true;

					} else {
						$_builder_page = false;
					}
					
				} else {
					$_builder_page = false;
				}
				
				$_attrs = array(
					'id' 			=> 'js_composer',
					'version' 		=> WPB_VC_VERSION,
					'class'   		=> 'WPGlobus_JS_Composer',
					'post_type' 	=> $post_type,
					'builder_page' 	=> $_builder_page
				);
				
				$attrs = self::get_attrs($_attrs);
			
				return $attrs;
				
			}
			
			return false;
		}
		
		/**
		 * Check for gutenberg.
		 */		
		protected static function is_gutenberg() {
		
			$load_gutenberg = false;
			$message = '';
			
			global $pagenow;

			if ( defined('GUTENBERG_VERSION') ) {
				
				if ( version_compare( GUTENBERG_VERSION, '3.1.999', '<=' ) ) {
					
					$message = 'Unsupported Gutenberg version.';
				
				} else {
			
					if ( self::is_gutenberg_ajax() ) {

						$load_gutenberg = true;

					} else {
						
						if ( 'post-new.php' == $pagenow ) {
							
							/**
							 * Don't load WPGlobus_Gutenberg for new post.
							 */
							$load_gutenberg = false; 
						
						} elseif ( 'index.php' == $pagenow ) {

							/**
							 * When Update button was clicked.
							 */
							if ( ! is_admin() ) {
								/**
								 * Gutenberg updates post as from front.
								 */
								$actions = array('edit');
								if ( false !== strpos($_SERVER['REQUEST_URI'], 'wp/v2/posts') ) {
									$load_gutenberg = true;
								}
							}
							
						} elseif( 'post.php' == $pagenow ) {
							
							$load_gutenberg = true;
							
							$actions = array('edit', 'editpost');
							if ( ! empty($_GET['action']) ) {
								if ( in_array($_GET['action'], $actions ) ) {
									if ( array_key_exists('classic-editor', $_GET) ) {
										$load_gutenberg = false;
									}
									if ( isset($_GET['meta_box']) && (int) $_GET['meta_box'] == 1 ) {
										$load_gutenberg = true;
									}
								}
							} elseif ( ! empty($_POST['action']) ) {
								if ( in_array($_POST['action'], $actions ) ) {
									if ( array_key_exists('classic-editor', $_POST) ) {
										$load_gutenberg = false;
									}
									if ( isset($_POST['meta_box']) && (int) $_POST['meta_box'] == 1 ) {
										$load_gutenberg = true;
									}
								}
							}
						
						}

					}
				
				}
				
				$_attrs = array(
					'id' 			=> 'gutenberg',
					'version' 		=> GUTENBERG_VERSION,
					'class'			=> 'WPGlobus_Gutenberg',
					'builder_page' 	=> false,
					'message'		=> $message
				);
				
				if ( $load_gutenberg ) {
					$_attrs['builder_page'] = true;
				}
				
				$attrs = self::get_attrs($_attrs);

				return $attrs;
			
			}

			return $load_gutenberg;		
		}
		
		/**
		 * Check for gutenberg ajax.
		 */				
		protected static function is_gutenberg_ajax() {
			$result = false;

			if ( empty( $_POST ) || empty($_POST['action']) ) {
				return $result;
			}
			
			$actions = array('edit', 'editpost');	
			if ( in_array($_POST['action'], $actions ) ) {
				if ( array_key_exists( 'gutenberg_meta_boxes', $_POST ) ) {
					$result = true;
				}
			}
			return $result;
		}
		
		/**
		 * Get attributes.
		 */
		protected static function get_attrs($_attrs) {
			return array_merge( self::$attrs, $_attrs );
		}

		/**
		 * Get post type.
		 */		
		protected static function get_post_type( $id = '' ) {
			if ( 0 == (int) $id ) {
				return null;
			}
			
			global $wpdb;

			$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $id ) );
			
			return $post_type;
		}
		
	}

endif;

# --- EOF