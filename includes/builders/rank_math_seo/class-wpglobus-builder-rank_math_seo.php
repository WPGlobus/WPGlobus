<?php
/**
 * File: class-wpglobus-builder-rank_math_seo.php
 *
 * @since 2.4.3
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * @author  Alex Gor(alexgff)
 */
 
if ( ! class_exists( 'WPGlobus_Builder_RankMathSEO' ) ) :

	/**
	 * Class WPGlobus_Builder_RankMathSEO.
	 */
	class WPGlobus_Builder_RankMathSEO {
		
		/**
		 * Options titles. 
		 * @see section Titles&Meta.
		 */
		protected static $options_titles = 'rank-math-options-titles';

		/**
		 * Get attributes.
		 */
		public static function get_attrs($attrs) {	
		
			/** @global string $pagenow */
			global $pagenow;

			if ( 'post.php' === $pagenow ) {
				
				$post_type = 'post';
				if ( ! empty( $attrs['post_type'] ) ) {
					$post_type = $attrs['post_type'];
				}
				
				$opts = get_option( self::$options_titles );
				
				if ( ! empty( $opts[ "pt_{$post_type}_add_meta_box" ] ) && 'off' == $opts[ "pt_{$post_type}_add_meta_box" ] ) {
					$attrs = false;
				} else {
					$attrs['builder_page'] = true;
				}
				
				return $attrs;
				
			} elseif ( 'term.php' === $pagenow ) {
				
				$tax = empty( $_GET['taxonomy'] ) ? false : sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ); // phpcs:ignore WordPress.CSRF.NonceVerification
				
				if ( $tax ) {
					
					$opts = get_option( self::$options_titles );

					if ( ! empty( $opts[ "tax_{$tax}_add_meta_box" ] ) && 'off' == $opts[ "tax_{$tax}_add_meta_box" ] ) {
						$attrs = false;
					} else {
						$attrs['post_type'] 	= ''; // reset post type.
						$attrs['taxonomy']  	= $tax;
						$attrs['builder_page']  = true;
					}
					
					return $attrs;
				}			
			}
			
			return false;
		}	
	}
	
endif;

# --- EOF