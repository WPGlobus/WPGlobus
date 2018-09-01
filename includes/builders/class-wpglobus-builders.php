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
		
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				//return false;
			}
			
			self::$attrs = array(
				'id'			=> false,
				'version' 		=> '',
				'class'   		=> '',
				'post_type'		=> '',
				'is_admin' 		=> true,
				'pagenow' 		=> '',
				'builder_page' 	=> false,
				'doing_ajax' 	=> WPGlobus_WP::is_doing_ajax(),
				'language'		=> '',
				'message'		=> '',
				'ajax_actions'	=> '',
				'multilingualFields' => array('post_title', 'excerpt'),
				'translatableClass'	 => 'wpglobus-translatable'
			);
			
			if ( $init ) {
			
				$builder = false;
				
				/**
				 * @since 1.9.17
				 */
				$builder = self::is_gutenberg();
				if ( $builder ) {
					if ( $builder['builder_page'] ) {
						return $builder;
					}
				}
				
				/**
				 * @since 1.9.17
				 */
				$builder = self::is_js_composer();
				if ( $builder ) {
					return $builder;
				}

				/**
				 * @since 1.9.17
				 * @todo WIP.
				 */
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
				
				/**
				 * @since 1.9.17
				 */
				$builder = self::is_yoast_seo();
				if ( $builder ) {
					return $builder;
				}				
				
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
			
			$load_elementor = false;
			
			if ( in_array($pagenow, array('admin-ajax.php', 'post.php', 'index.php') ) ) {
			
				$ajax_actions 	= '';
				$is_admin 		= true;
				
				if ( 'admin-ajax.php' == $pagenow ) {
					
					if ( ! isset( $_REQUEST['action'] ) || 'elementor_ajax' != $_REQUEST['action'] ) {
						return false;
					}
					if ( false !== strpos( $_REQUEST['actions'], 'save_builder' ) ) {
						$ajax_actions = 'save_builder';
						$load_elementor = true;
					} else if ( false !== strpos( $_REQUEST['actions'], '"action":"render_widget"' ) ) {
						$ajax_actions = 'render_widget';
						$load_elementor = true;
					} else {
						return false;
						
					}
					
				} else if( 'index.php' == $pagenow ) {

					if ( ! isset( $_GET['elementor-preview'] ) ) {
						return false;
					}
					$load_elementor = true;
					$is_admin = false;
					
				} else {
					
					$load_elementor = true;
					
				}

				/**
				 * $cpt_support = get_option( 'elementor_cpt_support', array('page', 'post') );
				 * @see elementor\includes\plugin.php
				 */
				$cpt_support = get_option( 'elementor_cpt_support', array('page', 'post') );			
				
				$post_type = '';
				if ( isset( $_GET['post'] ) ) {
					$post_type = self::get_post_type($_GET['post']);
				}
				
				$_attrs = array(
					'id' 			=> 'elementor',
					'version' 		=> ELEMENTOR_VERSION,
					'is_admin' 		=> $is_admin,
					'class'   		=> 'WPGlobus_Elementor',
					'post_type' 	=> $post_type,
					'builder_page' 	=> false,
					'ajax_actions'	=> $ajax_actions
				);
				
				if ( in_array( $post_type, $cpt_support ) ) {
					$_attrs['builder_page'] = true;
				}
				
				if ( $load_elementor ) {
					$_attrs['builder_page'] = true;
				}
				
				$attrs = self::get_attrs($_attrs);
			
				return $attrs;
				
			}
			
			return false;
			
		}
		
		/**
		 * @see WPBakery Page Builder.
		 * @since 1.9.17
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
				 * so let's check the roles.
				 */
				$_opts = wp_roles()->roles;

				if ( ! function_exists('wp_get_current_user') ) {
					require_once( ABSPATH . WPINC . '/pluggable.php' );
				}
				
				$_user = wp_get_current_user();
				
				$post_id = WPGlobus_Utils::safe_get('post');
				
				if ( empty( $post_id ) ) {
					/**
					 * Before update post we can get empty $_GET array.
					 * Let's check $_POST.
					 */
					$post_id = isset($_POST['post_ID']) ? sanitize_text_field($_POST['post_ID']) : '';
				}
				
				if ( empty( $post_id ) ) {
					// @todo add handling this case.
				}
				
				$_post_type = $wpdb->get_col( $wpdb->prepare("SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id) );
				
				$post_type = '';
				if ( ! empty($_post_type[0]) ) {
					$post_type = $_post_type[0];
				}
				
				if ( ! isset( $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types'] ) ) {
					/**
					 * WPBakery Page Builder is available for pages only (settings was not saved yet).
					 */
					if ( $post_type != 'page' ) {
						$_builder_page = false;
					}
					
				} else if ( '' == $_opts[$_user->roles[0]]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * All post types are disabled in WPBakery Page Builder.
					 */
					$_builder_page = false; 

				// TODO compare booleans and not '1'==true.
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
		 * @since 1.9.17
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
							 * Load specific language switcher for this page.
							 * @see get_switcher_box() in wpglobus\includes\builders\gutenberg\class-wpglobus-gutenberg.php
							 */
							$load_gutenberg = true; 
						
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

							$post_type = '';
							if ( ! empty( $_GET['post'] ) ) {
								$post_type = self::get_post_type($_GET['post']);
							}
							
							/**
							 * Since 1.9.17 Gutenberg support will be start for posts and pages only.
							 */
							if ( ! in_array( $post_type, array('post', 'page') ) ) {
								$load_gutenberg = false;
							}

						}

					}
				
				}
				
				$_attrs = array(
					'id' 			=> 'gutenberg',
					'version' 		=> GUTENBERG_VERSION,
					'class'			=> 'WPGlobus_Gutenberg',
					'builder_page' 	=> false,
					'pagenow' 		=> $pagenow,
					'post_type'		=> empty($post_type) ? '' : $post_type,
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
		 * Check for Yoast SEO.
		 * @since 1.9.17
		 */		
		protected static function is_yoast_seo() {
			
			if ( defined('WPSEO_VERSION') ) {
				
				global $pagenow;
	
				if ( 'post.php' == $pagenow ) {	
				
					$post_type = '';
					if ( ! empty( $_GET['post'] ) ) {
						$post_type = self::get_post_type($_GET['post']);
					}
					
					if ( empty($post_type) ) {
						/**
						 * Check $_REQUEST when post is updated.
						 */
						if ( ! empty($_REQUEST['post_type']) ) { 
							$post_type = $_REQUEST['post_type'];
						}
					}
					
					$_attrs = array(
						'id' 			=> 'yoast_seo',
						'version' 		=> WPSEO_VERSION,
						'class'			=> 'WPGlobus_Yoast_SEO',
						'builder_page' 	=> false,
						'post_type'		=> empty($post_type) ? '' : $post_type
					);
					
					if ( empty($post_type) ) {
						/**
						 * @since 1.9.17 detect builder page using $pagenow.
						 */
						$_attrs['builder_page'] = true;
					} else if ( in_array( $post_type, array('post', 'page') ) ) {
						$_attrs['builder_page'] = true;
					}
					
					$attrs = self::get_attrs($_attrs);
					
					return $attrs;
						
				}
				
			}
			
			return false;
			
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