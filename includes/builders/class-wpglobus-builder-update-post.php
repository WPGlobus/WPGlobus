<?php
/**
 * File: class-wpglobus-builder-update-post.php
 *
 * @since 2.2.35
 *
 * @package WPGlobus\Builders
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Builder_Update_Post.
 */
if ( ! class_exists( 'WPGlobus_Builder_Update_Post' ) ) :

	class WPGlobus_Builder_Update_Post {

		/**
		 * Builder ID.
		 */
		protected $id = null;
		
		/**
		 * Constructor.
		 */
		public function __construct( $id ) {
			
			$this->id = $id;
			
			/**
			 * Do not start for `gutenberg`.
			 * The block editor prohibits saving post with an empty post title from post edit page.
			 */
			if ( 'gutenberg' == $this->id ) {
				return;
			}
			
			add_filter( 'wp_insert_post_empty_content', array( $this, 'filter__post_empty_content' ), 10, 2);
		}

		/**
		 * Filters whether the post should be considered "empty".
		 * 
		 * @see wp-includes\post.php
		 *
		 * @param bool  $maybe_empty Whether the post should be considered "empty".
		 * @param array $postarr     Array of post data.
		 */		
		public function filter__post_empty_content( $maybe_empty, $postarr ) {

			if ( WPGlobus::Config()->builder->is_default_language() ) {
				return $maybe_empty;
			}
			
			/**
			 * Don't return a truthy value for extra language.
			 */
			return false;
		}
	}

endif;