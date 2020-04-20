<?php
/**
 * File: class-wpglobus-admin-post.php
 * Class for post.php page.
 *
 * @package WPGlobus\Admin
 * @author  Alex Gor(alexgff)
 * @since 2.4
 */
if ( ! class_exists( 'WPGlobus_Admin_Post' ) ) :

	/**
	 * Class WPGlobus_Admin_Menu.
	 */
	class WPGlobus_Admin_Post {

		/**
		 * Static constructor.
		 */
		public static function construct() {
			
			if ( ! WPGlobus_WP::is_pagenow('post.php') ) {
				return;
			}

			if ( 'off' == WPGlobus::Config()->toggle ) {
				return;
			}
			
			add_filter( 'preview_post_link', array( __CLASS__, 'filter__preview_post_link' ), 5, 2 );			
		}

		/**
		 * Filters the URL used for a post preview in builder mode.
		 * @see wp-includes/link-template.php
		 *
		 * @param string  $preview_link URL used for the post preview.
		 * @param WP_Post $post         Post object.
		 */
		public static function filter__preview_post_link( $preview_link, $post ) {
	
			if ( ! WPGlobus::Config()->builder->is_builder_page() ) {
				return $preview_link;
			}
			
			if ( WPGlobus::Config()->builder->is_default_language() ) {
				return $preview_link;

			}

			return WPGlobus_Utils::localize_url( $preview_link,  WPGlobus::Config()->builder->get_language() );
		}
	}
	
endif;	

# --- EOF