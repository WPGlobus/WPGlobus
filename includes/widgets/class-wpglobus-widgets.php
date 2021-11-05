<?php
/**
 * File: class-wpglobus-widgets.php
 *
 * Class WPGlobus_Widgets.
 * @since 2.8.6
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPGlobus_Widgets' ) ) :

	if ( ! function_exists( 'wp_get_current_user' ) ) {
		/**
		 * @see `current_user_can` function in WPGlobus_Widgets class.
		 */
		require_once( ABSPATH . WPINC . '/pluggable.php' );
	}

	class WPGlobus_Widgets {

		const SCOPE_ADMIN = 'admin';
		
		const SCOPE_FRONT = 'front';
		
		/**
		 * Current language.
		 */
		protected static $language = false;
		
		/**
		 * All widget options like `widget_*`.
		 * @var array
		 */
		protected static $widget_options = false;
		
		/**
		 * Option key for `sidebars_widgets`.
		 */
		protected static $sidebars_widgets_option_key = 'sidebars_widgets';
		
		/**
		 * Theme switched key.
		 */		
		protected static $theme_switched_key = 'wpglobus_widgets_theme_switched';
		
		/**
		 * Log file.
		 */
		protected static $__log_file = null;

		/**
		 * @var object Instance of this class.
		 */
		protected static $instance;
		
		/**
		 * Constructor.
		 */
		protected function __construct( $path_to_loader ) {
			
			self::$__log_file = plugin_dir_path( __FILE__ ) . 'wpglobus-widgets.log';
			
			self::set_language();
			
			self::set_option_filters();
			
			/**
			 * @see wp-includes\option.php
			 */
			add_action( 'option_widget_pages', array( __CLASS__, 'filter__widget_option' ), 5, 2 );
			
			/**
			 * @see wp-includes\option.php
			 */
			add_filter( 'option_sidebars_widgets', array( __CLASS__, 'filter__sidebars_widgets' ), 1, 2 );
			
			/**
			 * @see wp-includes\option.php
			 */
			add_filter( 'pre_update_option_sidebars_widgets', array( __CLASS__, 'filter__pre_update_option_sidebars_widgets' ), 1, 3 );

			/**
			 * @see wp-includes\option.php
			 */			
			add_filter( 'pre_update_option', array( __CLASS__, 'filter__pre_update_option' ), 1, 3 );
			
			/**
			 * wp-includes\theme.php
			 */
			add_action( 'switch_theme', array( __CLASS__, 'on__switch_theme' ), 1, 3 );

			add_action( 'admin_footer', array( __CLASS__, 'on__admin_footer' ) );
			
			add_action( 'admin_print_scripts', array( __CLASS__, 'on__admin_print_scripts' ), 10 );
		}

		/**
		 * Get instance of this class.
		 *
		 * @return get_instance || void
		 */
		public static function get_instance( $path_to_loader = array() ) {

			if ( WPGlobus_WP::is_doing_ajax() ) {
				return;
			}

			if ( ! ( self::$instance instanceof WPGlobus_Widgets  ) ) {
				self::$instance = new self( $path_to_loader );
			}

			return self::$instance;
		}


		/**
		 * Update `sidebars_widgets` option for extra languages.
		 * We cannot do it in `switch_theme` action, because it is early.
		 */
		public static function on__admin_footer() {
			
			global $pagenow;
			
			if ( 'themes.php' !== $pagenow ) {
				return;
			}
			
			$theme_switched = get_option( self::$theme_switched_key );
		
			if ( ! $theme_switched || empty( $theme_switched ) ) {
				return;
			}
			
			if ( ! function_exists( 'wp_map_sidebars_widgets' ) ) {
				return;	
			}
			
			foreach( WPGlobus::Config()->enabled_languages as $language ) {
				
				if ( $language === WPGlobus::Config()->default_language ) {
					continue;
				}
			
				$_sidebars_widgets = get_option( self::get_sidebars_widgets_option_key($language) );

				if ( ! empty($_sidebars_widgets) ) {
					
					/**
					 * @see wp-includes\widgets.php
					 */
					$new_sidebars_widgets = wp_map_sidebars_widgets( $_sidebars_widgets );

					update_option(
						self::get_sidebars_widgets_option_key($language),
						$new_sidebars_widgets,
						true 
					);
				}
			}
			
			update_option( self::$theme_switched_key, '', false );
		}

		/**
		 * Fires after the theme is switched.
		 *
		 * To get $sidebars_widgets from old theme.
		 * $sidebars_widgets = wp_get_sidebars_widgets();
		 *
		 * To get $sidebars_widgets from new theme.
		 * $theme_mods = get_theme_mods();
		 * $new_sidebars_widgets = $theme_mods['sidebars_widgets']['data'];
		 */
		public static function on__switch_theme( $new_name, $new_theme, $old_theme ) {
			$theme_switched = get_option( 'theme_switched' );
			update_option( self::$theme_switched_key, $theme_switched, false );
		}

		/**
		 * Check if is request to REST API.
		 *
		 * @return bool
		 */	
		public static function is_rest_request() {

			if ( empty( $_SERVER['REQUEST_URI'] ) ) {
				return false;
			}
			
			$rest_prefix = trailingslashit( rest_get_url_prefix() );
			$request_uri = esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) );

			$is_rest_request = true;
			if ( false === strpos( $request_uri, $rest_prefix ) ) {
				$is_rest_request = false;
			} 
			
			return $is_rest_request;		
		}
		
		/**
		 * Check if is request to REST API from admin.
		 *
		 * @return bool
		 */	
		public static function is_admin_rest_request() {
			
			if ( ! self::is_rest_request() ) {
				return false;
			}

			/**
			 * For example URL
			 * http://site/ru/wp-json/wp/v2/sidebars/primary-sidebar
			 * @see `do_permissions_check` function in rest-api\endpoints\class-wp-rest-sidebars-controller.php
			 */ 
			if ( ! current_user_can( 'edit_theme_options' ) ) {
				return false;
			}
	
			$language = WPGlobus::Config()->default_language;
			
			if ( preg_match( '/^\/[a-z]{2}\//', $_SERVER['REQUEST_URI'], $match ) ) {
				$language = str_replace( '/', '', $match[0] );
			}
			
			if ( ! in_array( $language, WPGlobus::Config()->enabled_languages ) ) {
				return false;
			}
			
			$extra_languages = WPGlobus::Config()->enabled_languages;
			unset($extra_languages[0]);
			
			if ( in_array( $language, $extra_languages ) ) {
				/**
				 * Any request with a language code from an external source.
				 */
				return false;
			}

			$is_admin_request = false;
			
			if ( ! empty($_COOKIE) ) {

				foreach( $_COOKIE as $cookie=>$value ) {
					if ( 0 === strpos( $cookie, 'wordpress_logged_in' ) ) {
						$is_admin_request = true;
						break;
					}
				}
			}

			return $is_admin_request;
		}

		/**
		 * Set option filters.
		 */
		public static function set_option_filters() {
			
			$widget_options = array();
			
			$alloptions = wp_cache_get( 'alloptions', 'options' );
			
			foreach( $alloptions as $option=>$value ) {
				if ( 0 === strpos( $option, 'widget_' ) ) {
					
					// Don't filter option with language mark in name.
					$_subs = substr($option, -3, 3);
					if ( $_subs[0] == '_' && array_key_exists( $_subs[1].$_subs[2], WPGlobus::Config()->language_name ) ) {
						continue;
					}

					$widget_options[$option] = $option;
				}
			}

			if ( ! empty($widget_options) ) {
				foreach( $widget_options as $option ) {
					add_filter( 'option_'.$option, array( __CLASS__, 'filter__widget_option' ), 5, 2 );
				}
			}
		}
		
		/**
		 * Filter `sidebars_widgets` option according current language.
		 *
		 * @see wp-includes\option.php
		 */
		public static function filter__sidebars_widgets( $value, $option ) {
			
			$language = false;
			
			$scope = false;

			if ( self::is_admin_widgets() || self::is_admin_rest_request() ) {

				$language = self::get_language();
				
				if ( self::is_default_language($language) ) {
					return $value;
				}	
				
				$scope = self::SCOPE_ADMIN;
				
			} else {
				
				/** @scope front */
				
				if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
					return $value;
				}
				
				$language = WPGlobus::Config()->language;
		
				$scope = self::SCOPE_FRONT;
			}


			return self::_get_sidebars_widgets($value, $language, $scope);
		}

		/**
		 * Filter any option likes `widget_*`.
		 */
		public static function filter__widget_option( $value, $option ) {

			$language = false;
			
			$scope = false;

			if ( self::is_admin_widgets() || self::is_admin_rest_request() ) {

				$language = self::get_language();

				if ( self::is_default_language($language) ) {
					return $value;
				}			
				
				$scope = self::SCOPE_ADMIN;	
	
			} else {
				
				if ( WPGlobus::Config()->language === WPGlobus::Config()->default_language ) {
					return $value;
				}
				
				$language = WPGlobus::Config()->language;
		
				$scope = self::SCOPE_FRONT;				
			}
	
			return self::get_widget_option( $option, $value, $language, $scope );
		}
		
		/**
		 * Filter `pre_update_option`.
		 * @see wp-includes\option.php
		 */
		public static function filter__pre_update_option( $value, $option, $old_value ) {

			if ( self::is_default_language() ) {
				return $value;
			}
			
			$enabled_mask = 'widget_';
			
			if ( 0 !== strpos( $option, $enabled_mask ) ) {
				return $value;
			}
	
			if ( $value === $old_value || maybe_serialize($value) === maybe_serialize($old_value) ) {
				return $value;
			}			

			self::__log('before update option::  '.self::get_widget_option_key( $option, self::get_language() ));
			self::__log($value);			
			self::update_option( 
				self::get_widget_option_key( $option, self::get_language() ), 
				$value 
			);
			
			/**
			 * Return old value to prevent saving value for default language.
			 */
			return $old_value;
		}

		/**
		 * Filter pre update `sidebars_widgets` option.
		 */
		public static function filter__pre_update_option_sidebars_widgets( $value, $old_value, $option ) {
			
			if ( self::is_default_language() ) {
				return $value;
			}
			
			if ( $value === $old_value || maybe_serialize($value) === maybe_serialize($old_value) ) {
				return $value;
			}	
			
			$option_key = self::get_sidebars_widgets_option_key( self::get_language() );

			/**
			 * Update option for extra language.
			 * Debug point.
			 * self::__log('before update option::  '.$option_key);
			 * self::__log($value);			 
			 * self::__log($old_value);			 
			 */
			self::update_option( $option_key, $value );

			/**
			 * Return old value to prevent saving value for default language.
			 */
			return $old_value;
		}
	
		/**
		 * Update option.
		 *
		 * @see `update_option` function in wp-includes\option.php
		 */
		protected static function update_option( $option, $value, $language = false ) {
			
			global $wpdb;
			
			if ( ! $language ) {
				$language = self::get_language();
			}
			
			if ( self::is_default_language( $language ) ) {
				return false;
			}

			$serialized_value = maybe_serialize( $value );

			$update_args = array(
				'option_value' => $serialized_value,
			);

			$update_args['autoload'] = 'yes';
			
			$result = $wpdb->update( $wpdb->options, $update_args, array( 'option_name' => $option ) );
			if ( ! $result ) {
				return false;
			}
	
			$notoptions = wp_cache_get( 'notoptions', 'options' );

			if ( is_array( $notoptions ) && isset( $notoptions[ $option ] ) ) {
				unset( $notoptions[ $option ] );
				wp_cache_set( 'notoptions', $notoptions, 'options' );
			}

			if ( ! wp_installing() ) {
				$alloptions = wp_load_alloptions( true );
				if ( isset( $alloptions[ $option ] ) ) {
					$alloptions[ $option ] = $serialized_value;
					wp_cache_set( 'alloptions', $alloptions, 'options' );
				} else {
					wp_cache_set( $option, $serialized_value, 'options' );
				}
			}
	
			return true;
		}
		
		/**
		 * Return true if current language is default language.
		 */
		protected static function is_default_language( $language = false ) {
			
			if ( ! $language ) {
				$language = self::get_language();
			}
			
			if ( $language === WPGlobus::Config()->default_language ) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * Set current language.
		 */
		protected static function set_language() {
	
			$language = false;
				
			if ( self::is_admin_widgets() ) {
				
				if ( ! empty( $_GET['language'] ) ) {
					$language = WPGlobus_Utils::safe_get('language');
					if ( ! in_array( $language, WPGlobus::Config()->enabled_languages ) ) {
						$language = WPGlobus::Config()->default_language;
					}
					self::update_language_option( $language );
				}
				
				if ( ! $language ) {
					$language = (string) self::get_language_option();
				}
				
				if ( empty($language) ) {
					$language = WPGlobus::Config()->default_language;
					self::update_language_option( $language );
				}
				
			} else if ( self::is_admin_rest_request() ) {
				
				$language = (string) self::get_language_option();
				if ( ! $language || empty( $language ) ) {
					$language = WPGlobus::Config()->default_language;
				}
			
			} else {

				$language = WPGlobus::Config()->language;

			}
			
			self::$language = $language;

			return self::$language;
		}

		/**
		 * Check out that we are on widgets.php page.
		 */		
		protected static function is_admin_widgets() {
			
			global $pagenow;
			
			if ( is_admin() && 'widgets.php' === $pagenow ) {
				return true;
			}
			
			return false;			
		}

		/**
		 * Get language option.
		 */	
		protected static function get_language_option() {
			return get_option( self::get_language_option_key() );
		}

		/**
		 * Update language option.
		 */		
		protected static function update_language_option($language) {
			update_option( self::get_language_option_key(), $language, false );
		}
		
		/** 
		 * Get language option key.
		 */
		protected static function get_language_option_key() {
			return 'wpglobus_widgets_page_language';
		}		

		/**
		 * Get any widget option, like `widget_block`, `widget_search` and so on.
		 */
		protected static function get_widget_option( $option, $value, $language, $scope ) {
			
			$alloptions = wp_cache_get( 'alloptions', 'options' );
			
			$option_key = self::get_widget_option_key($option, $language);

			if ( isset( $alloptions[$option_key] ) ) {
				
				$_value = maybe_unserialize( $alloptions[$option_key] );
				if ( is_array( $_value ) ) {
					$value = $_value;
				}

			} else {
				
				/**
				 * Option doesn't exist.
				 */
				if ( $scope == self::SCOPE_FRONT ) {
					
					// Do nothing.
				
				} else {
					
					/**
					 * @scope 'admin'
					 */
					if ( is_array($value) ) {
						
						if ( ! empty($value) ) {
							foreach( $value as $_key=>$_value ) {
							
								/**
								 * Reset all array values except `_multiwidget`.
								 */
								if ( in_array( $_key, array( '_multiwidget' ) ) ) {
									continue;
								}
						
								unset( $value[ $_key ] );
							}
						}
						
						self::add_option( $option_key, $value );
					}
				}
			}
			
			return $value;			
		}
		
		/** 
		 * Get `sidebars_widgets` option for extra languages.
		 *
		 * @param $value 	Value for default lanaguage.
		 * @param $language Extra language.
		 * @param $scope 	Scope.
		 */
		protected static function _get_sidebars_widgets($value, $language, $scope) {
			
			$alloptions = wp_cache_get( 'alloptions', 'options' );
			
			$option_key = self::get_sidebars_widgets_option_key($language);
			
			if ( isset( $alloptions[$option_key] ) ) {
				
				$_value = maybe_unserialize( $alloptions[$option_key] );

				if ( is_array( $_value ) ) {
					$value = $_value;
				}

			} else {

				if ( $scope == self::SCOPE_FRONT ) {
					
					// Do nothing.
				
				} else {
					
					/**
					 * @scope 'admin'.
					 */
					 
					if ( is_array( $value ) ) {
						
						foreach( $value as $_key=>$_value ) {
						
							/**
							 * Reset all array values except `array_version`.
							 */
							if ( in_array( $_key, array( 'array_version' ) ) ) {
								continue;
							}
					
							$value[ $_key ] = array();
						}
						
						/**
						 * Add option `sidebars_widgets` for current language.
						 */
						self::__log('before add option  ::  '.$option_key);
						self::__log( maybe_serialize($value) );						
						self::__log('==================');								 
						self::add_option( $option_key, $value );
						
						/**
						 * New `sidebars_widgets` option has been added, let's redirect.
						 */
						$redirect_to = admin_url( 
							add_query_arg( 
								array( 
									'language' => self::get_language() 
								),
								'widgets.php'
							)	
						);
						
						wp_redirect( $redirect_to );
						exit;
					}
				}
			}
			
			return $value;
		}

		/**
		 * Add option.			
		 *
		 * @see `add_option` function in wp-includes\option.php
		 */
		protected static function add_option( $option, $value = '', $autoload = 'yes' ) {
	
			if ( add_option( $option, $value, '', $autoload ) ) {
				self::__log('New option was added to DB:: ' . $option);	
				return true;
			}
			self::__log('New option was NOT added to DB:: ' . $option);	
			
			return false;
		}
		
		/** 
		 * Get sidebars widgets option key for language.
		 */		
		protected static function get_sidebars_widgets_option_key( $language = false ) {
			if ( ! $language || empty($language) ) {
				return self::$sidebars_widgets_option_key;
			}
			return self::$sidebars_widgets_option_key . '_' . $language;
		}

		/** 
		 * Get widget option key for language.
		 */		
		protected static function get_widget_option_key( $option, $language = false ) {
			if ( ! $language ) {
				return $option;
			}
			return $option . '_' . $language;
		}		
		
		/**
		 * Get current language.
		 */
		protected static function get_language() {
			return self::$language;	
		}
	
		/** 
		 * Get `sidebars_widgets` option.
		 *
		 * @param $language 				  Language code.
		 * @param $return_in_default_language If true, then return `sidebars_widgets` for default language.
		 */	
		public static function get_sidebars_widgets( $language = '', $return_in_default_language = false ) {
			$sidebars_widgets = get_option( self::get_sidebars_widgets_option_key($language) );
			if ( false === $sidebars_widgets && $return_in_default_language ) {
				$sidebars_widgets = get_option( self::get_sidebars_widgets_option_key() );
			}
			return $sidebars_widgets;
		}
		
		/**
		 * Logger.
		 *
		 * Use `WPGLOBUS_WIDGETS_PHP_DEBUG` constant to activate logging.
		 */
		protected static function __log( $string = '' ) {
			
			if ( ! defined('WPGLOBUS_WIDGETS_PHP_DEBUG') || ! WPGLOBUS_WIDGETS_PHP_DEBUG ) {
				return;
			}
			
			if ( is_null( self::$__log_file ) || empty( $string ) ) {
				return;
			}
			
			if ( is_array($string) ) {
				$string = maybe_serialize($string);
			}
			
			$pre = '';
			
			$timestamp = $pre . '['.date( 'Y-m-d H:i:s' ).'] ';
			
			file_put_contents( 
				self::$__log_file,
				$timestamp.$string . "\n",
				FILE_APPEND
			);
		}
		
		/**
		 * Enqueue scripts.
		 */
		public static function on__admin_print_scripts() {
			
			global $pagenow;
			
			if ( 'widgets.php' !== $pagenow ) {
				return;
			}
			
			$alloptions = wp_cache_get( 'alloptions', 'options' );
			$option_icon = array();
			$_image_url = plugin_dir_url( __FILE__) . 'assets/css/images/';
			
			foreach( WPGlobus::Config()->enabled_languages as $_language ) {
				
				if ( WPGlobus::Config()->default_language === $_language ) {
					
					$option_icon[$_language] = $_image_url . 'icon-ok.png';
				
				} else {
				
					$_option_key = self::get_sidebars_widgets_option_key($_language);
					$option_icon[$_language] = '';
					if ( isset( $alloptions[$_option_key] ) ) {
						$option_icon[$_language] = $_image_url . 'icon-ok.png';
					}
				}					
			}
			unset( $alloptions );
			
			$extra_languages = WPGlobus::Config()->enabled_languages;
			foreach( $extra_languages as $_key=>$_language ) {
				if ( self::get_language() == $_language ) {
					unset( $extra_languages[$_key] );
					break;
				}
			}

			/**
			 * Build the language flag URLs.
			 */
			$language_flag_url = array();
			foreach ( WPGlobus::Config()->enabled_languages as $language ) {
				if ( file_exists( WPGlobus::Config()->flag_path['big'] . WPGlobus::Config()->flag[ $language ] ) ) {
					$language_flag_url[ $language ] = WPGlobus::Config()->flag_urls['big'] . WPGlobus::Config()->flag[ $language ];
				} else {
					$language_flag_url[ $language ] = WPGlobus::Config()->flags_url . WPGlobus::Config()->flag[ $language ];
				}
			}

			$data = array(
				'wpVersion'   => $GLOBALS['wp_version'],
				'version'     => WPGLOBUS_VERSION,
				'phase'	  	  => 'production',
				'homeUrl'	  => home_url('/'),
				'currentUrl'  => add_query_arg(
					array(),
					admin_url( 'widgets.php' )
				),
				'href'  => add_query_arg(
					array( $_GET ),
					admin_url( 'widgets.php' )
				),				
				'pagenow'  		   => $pagenow,
				'flag'  	   	   => WPGlobus::Config()->flag,
				'flagUrls'  	   => WPGlobus::Config()->flag_urls,
				'defaultLanguage'  => WPGlobus::Config()->default_language,
				'enabledLanguages' => WPGlobus::Config()->enabled_languages,
				'enLanguageName'   => WPGlobus::Config()->en_language_name,
				'extraLanguages'   => $extra_languages,
				'currentLanguage'  => (array) self::get_language(),
				'languageFlagUrl'  => $language_flag_url,
				'wpgPremiumURL'    => 'https://wpglobus.com/shop/',
				'wpgOptionsURL'    => add_query_arg(
					array('page'=>'wpglobus_options', 'tab'=>'block-editor'),
					admin_url( 'admin.php' )
				),
				'optionIcon'       => $option_icon,
				'singleTabLayout'  => true,
			);
			
			if ( defined('WPGLOBUS_WIDGETS_PHP_DEBUG') && defined('WPGLOBUS_WIDGETS_PHP_ASSETS_URL') ) {
				$app_js = WPGLOBUS_WIDGETS_PHP_ASSETS_URL . 'js/wpglobus-widgets-block-editor.min.js';
				$app_css = WPGLOBUS_WIDGETS_PHP_ASSETS_URL . 'css/wpglobus-widgets-block-editor.min.css';
			} else {
				$app_js = plugin_dir_url( __FILE__ ) . 'assets/js/wpglobus-widgets-block-editor.min.js';
				$app_css = plugin_dir_url( __FILE__ ) . 'assets/css/wpglobus-widgets-block-editor.min.css';
			}
			
			wp_enqueue_style( 'wpglobus-widgets-block-editor', $app_css, array(), WPGLOBUS_VERSION );
			
			wp_register_script( 'wpglobus-widgets-block-editor', $app_js, array(), WPGLOBUS_VERSION, true );   
			wp_enqueue_script( 'wpglobus-widgets-block-editor' ); 
			wp_localize_script(
				'wpglobus-widgets-block-editor',
				'WPGlobusWidgetsBlockEditor',
				$data
			);		
		}
		
	} // class WPGlobus_Widgets.

endif;

# --- EOF