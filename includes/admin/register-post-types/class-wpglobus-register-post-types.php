<?php
/**
 * File: class-wpglobus-register-post-types.php
 *
 * @since 2.2.24
 * @package WPGlobus\Admin
 */

/**
 * Class WPGlobus_Register_Post_Types.
 */
if ( ! class_exists( 'WPGlobus_Register_Post_Types' ) ) :

	class WPGlobus_Register_Post_Types {

		/**
		 * @var array
		 */
		protected static $pages = array( 'edit.php', 'post.php' );

		/**
		 * Don't handling with these post types.
		 * @var array
		 */		
		protected static $excluded_post_types = array( 
			'attachment', 
			'attachment:audio',
			'attachment:video', 
			'revision', 
			'nav_menu_item', 
			'custom_css', 
			'customize_changeset', 
			'oembed_cache', 
			'user_request', 
			'wp_block', 
		);
		
		/**
		 * @var array
		 */
		protected static $post_types = array();

		/**
		 * Constructor.
		 */
		public static function construct() {

			if ( ! WPGlobus_WP::is_pagenow(self::$pages) ) {
				return;
			}

			/**
			 * @see wp-includes\post.php
			 */
			add_action( 'registered_post_type', array( __CLASS__, 'on__registered' ), 10, 2 );
			
			add_action( 'wp_loaded', array( __CLASS__, 'on__wp_loaded' ) );

		}

		/**
		 * Fires after a post type is registered.
		 *
		 * @param string       $post_type        Post type.
		 * @param WP_Post_Type $post_type_object Arguments used to register the post type.
		 */
		public static function on__registered( $post_type, $post_type_object ) {

			global $_wp_post_type_features;
			
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				return;
			}
			
			static $_init = null;
			
			if ( is_null($_init) ) {
				
				self::$excluded_post_types = array_merge(self::$excluded_post_types, WPGlobus::Config()->disabled_entities);
				
				self::$excluded_post_types = array_unique( self::$excluded_post_types );
				
				$_init = true;

			}
			
			if ( self::is_excluded_post_type($post_type) ) {
				return;
			}
			
			if ( empty($_wp_post_type_features[$post_type]) ) {
				return;
			}
			
			/**
			 * @see `use_block_editor_for_post_type()` in wp-admin\includes\post.php
			 */
			self::$post_types[$post_type] = array(
				'show_in_rest'  => $post_type_object->show_in_rest,
				'features' 		=> $_wp_post_type_features[$post_type]
			);

		}
		
		/**
		 * Fired to save option.
		 */
		public static function on__wp_loaded() {
			
			if ( defined('DOING_AJAX') && DOING_AJAX ) {
				return;
			}
			if ( ! empty(self::$post_types) ) {
				update_option( WPGlobus::Config()->option_register_post_types, self::$post_types, false );
			}
		}

		/**
		 * Check for excluded post type.
		 *
		 * @return bool
		 */
		public static function is_excluded_post_type( $post_type = '') {
			
			if ( empty($post_type) ) {
				return true;
			}
			
			if ( in_array( $post_type, self::get_excluded_post_types() ) ) {
				return true;
			}
			
			return false;
		}
		
		/**
		 * Get excluded post types.
		 *
		 * @return array
		 */
		public static function get_excluded_post_types() {
			return self::$excluded_post_types;
		}
	}

endif;
/* EOF */