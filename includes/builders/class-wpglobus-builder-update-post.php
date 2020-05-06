<?php
/**
 * File: class-wpglobus-builder-update-post.php
 *
 * @since 2.2.35
 * @since 2.4.3   Added `wp_insert_post_data` filter.
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

			/**
			 * @since 2.4.3
			 * @todo incorrect the saving post in extra languages with priority = 10
			 */			
			add_filter( 'wp_insert_post_data', array( $this, 'filter__wp_insert_post_data' ), 100, 2 );
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

		/**
		 * Callback for 'wp_insert_post_data'.
		 *
		 * @since 2.4.3
		 * @param array $data
		 * @param array $postarr
		 *
		 * @return mixed
		 */
		public function filter__wp_insert_post_data( $data, $postarr ) {
			
			/**
			 * Prevent to filter disabled post type.
			 */
			if ( in_array( $data['post_type'], WPGlobus::Config()->disabled_entities, true ) ) {
				return $data;
			}

			global $wpdb;
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d AND post_type = %s LIMIT 1", $postarr['ID'], $postarr['post_type'] ) );

			$fields = array();
			if ( isset( $postarr['post_title'] ) ) {
				$fields['post_title'] = $postarr['post_title'];
			}

			if ( isset( $postarr['post_content'] ) ) {
				$fields['post_content'] = $postarr['post_content'];
			}

			if ( isset( $postarr['post_excerpt'] ) ) {
				$fields['post_excerpt'] = $postarr['post_excerpt'];
			}

			$builder_language = WPGlobus::Config()->builder->get_language();

			if ( empty( $builder_language ) ) {

				if ( empty( $postarr[ WPGlobus::get_language_meta_key() ] ) ) {
					$builder_language = WPGlobus::Config()->default_language;
				} else {
					$builder_language = $postarr[ WPGlobus::get_language_meta_key() ];
				}
			}

			foreach ( $fields as $field => $value ) {

				$tr = array();

				foreach ( WPGlobus::Config()->enabled_languages as $lang ) :

					if ( $lang === $builder_language ) {

						$text = $value;
						if ( WPGlobus_Core::has_translations( $value ) ) {
							$text = WPGlobus_Core::text_filter( $value, $lang, WPGlobus::RETURN_EMPTY );
						}
						if ( ! empty( $text ) ) {
							$tr[ $lang ] = $text;
						}
					} else {
						if ( isset( $_post->$field ) ) {
							$text = WPGlobus_Core::text_filter( $_post->$field, $lang, WPGlobus::RETURN_EMPTY );
							if ( ! empty( $text ) ) {
								$tr[ $lang ] = $text;
							}
						}
					}

				endforeach;

				$data[ $field ] = WPGlobus_Utils::build_multilingual_string( $tr );

			}

			return $data;
		}
	} // class WPGlobus_Builder_Update_Post.

endif;

# --- EOF