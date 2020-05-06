<?php
/**
 * File: class-wpglobus-rank_math_seo-update-post.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_rank_math_seo_Update_Post' ) ) :

	/**
	 * Class WPGlobus_rank_math_seo_Update_Post.
	 */
	// phpcs:ignore PEAR.NamingConventions
	class WPGlobus_rank_math_seo_Update_Post extends WPGlobus_Builder_Update_Post {
		
		/**
		 * Constructor.
		 */
		public function __construct( $id = null ) {

			if ( is_null($id) ) {
				$id = WPGlobus::Config()->builder->get_id();
			}

			parent::__construct( $id );

			/**
			 * @see_file wpglobus\includes\class-wpglobus.php
			 */
			remove_action( 'wp_insert_post_data', array( 'WPGlobus', 'on_save_post_data' ), 10 );
		}

	} // class WPGlobus_rank_math_seo_Update_Post.

endif;

# --- EOF