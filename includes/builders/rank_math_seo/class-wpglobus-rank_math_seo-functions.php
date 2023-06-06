<?php
/**
 * File: class-wpglobus-rank_math_seo-functions.php
 *
 * @since   2.4.3
 * @since   2.8.9 Added support multilingual columns on `edit.php` page.
 * @since   2.12.0 Filter multilingual strings for the options page.
 *
 * @package WPGlobus\Builders\RankMathSEO.
 * Author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_RankMathSEO_Functions' ) ) :

	/**
	 * Class WPGlobus_RankMathSEO_Functions.
	 */
	class WPGlobus_RankMathSEO_Functions {

		/**
		 * RankMath options page prefix.
		 *
		 * @since 2.12.0
		 */
		protected static $options_page_prefix = 'rank-math';

		/**
		 * Current taxonomy.
		 */
		protected static $taxonomy = false;

		/**
		 * WP_Term object.
		 *
		 * @var WP_Term
		 */
		protected static $tag = false;

		/**
		 * Current language.
		 */
		protected static $current_language = false;

		/**
		 * Constructor.
		 */
		public static function controller() {

			if ( WPGlobus_WP::is_pagenow( 'edit.php' ) ) {

				/**
				 * To translate SEO columns on `edit.php` page.
				 *
				 * @since 2.8.9
				 */
				add_filter( 'get_post_metadata', array( __CLASS__, 'filter__post_metadata' ), 2, 4 );
			}

			/**
			 * Handle options page
			 *
			 * @since 2.12.0
			 */
			self::handle_options_page();

			if ( is_admin() ) {

				$_POST_action = WPGlobus_WP::get_http_post_parameter( 'action' );

				if ( ! WPGlobus_WP::get_http_post_parameter( 'nonce_CMB2phprank_math_metabox' ) || ! $_POST_action ) {
					/**
					 * Not `Rank Math SEO`.
					 */
					return;
				}

				global $pagenow;

				$_POST_get_language_meta_key = WPGlobus_WP::get_http_post_parameter( WPGlobus::get_language_meta_key() );

				if ( $_POST_get_language_meta_key ) {
					self::$current_language = $_POST_get_language_meta_key;
				}

				if ( 'edit-tags.php' === $pagenow && 'editedtag' === $_POST_action ) {
					/**
					 * Update button was clicked.
					 */
					self::build_ml_description();
				}

				add_filter( 'wp_update_term_data', array( __CLASS__, 'filter__update_term_data' ), 5, 4 );
			}
		}

		/**
		 * Method handle_options_page.
		 *
		 * @since 2.12.0
		 * @return void
		 */
		protected static function handle_options_page() {

			if ( ! is_admin() ) {
				return;
			}

			global $pagenow;

			if ( 'admin.php' !== $pagenow ) {
				return;
			}

			$page = WPGlobus_WP::get_http_get_parameter( 'page' );

			/**
			 * Make sure we are on the options page.
			 */
			if ( false === strpos( $page, self::$options_page_prefix ) ) {
				return;
			}

			/**
			 * See `setup_json` function in seo-by-rank-math\includes\replace-variables\class-manager.php
			 * See `do_filter` function in seo-by-rank-math\includes\traits\class-hooker.php
			 */
			add_filter( 'rank_math/vars/replacements', array( __CLASS__, 'filter__vars_replacements' ), 10 );
		}

		/**
		 * Method filter__vars_replacements.
		 *
		 * @since 2.12.0
		 *
		 * @param array $args
		 *
		 * @return array
		 */
		public static function filter__vars_replacements( $args ) {

			$enabled_keys = array( 'example' );

			foreach ( $args as $key => $data ) {
				foreach ( $enabled_keys as $enabled_key ) {
					if ( ! empty( $data[ $enabled_key ] ) && WPGlobus_Core::has_translations( $data[ $enabled_key ] ) ) {
						$args[ $key ][ $enabled_key ] = WPGlobus_Core::extract_text( $data[ $enabled_key ], WPGlobus::Config()->default_language );
					}
				}
			}

			return $args;
		}

		/**
		 * Method filter__post_metadata.
		 *
		 * @since        2.8.9
		 *
		 * @param $check
		 * @param $object_id
		 * @param $meta_key
		 * @param $single
		 *
		 * @return mixed
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function filter__post_metadata( $check, $object_id, $meta_key, $single ) {

			/**
			 * Todo  Get keys from builder.
			 *
			 * @since 2.8.9
			 */
			$enabled_keys = array(
				'rank_math_title',
				'rank_math_description',
				'rank_math_focus_keyword',
				'rank_math_seo_score',
			);

			if ( ! in_array( $meta_key, $enabled_keys, true ) ) {
				return $check;
			}

			$meta_type = 'post';

			$meta_cache = wp_cache_get( $object_id, $meta_type . '_meta' );

			if ( ! $meta_cache ) {
				return $check;
			}

			if ( ! empty( $meta_cache[ $meta_key ][0] ) && WPGlobus_Core::has_translations( $meta_cache[ $meta_key ][0] ) ) {

				$meta_cache[ $meta_key ][0] = WPGlobus_Core::text_filter( $meta_cache[ $meta_key ][0], WPGlobus::Config()->language );

				wp_cache_replace(
					$object_id,
					$meta_cache,
					$meta_type . '_meta'
				);
			}

			return $check;
		}

		/**
		 * Build multilingual description.
		 * We don't have filter for description like filter for name @see 'wp_update_term_data' in wp-includes\taxonomy.php
		 */
		protected static function build_ml_description() {

			if ( ! self::$current_language ) {
				return;
			}

			global $wpdb;

			$tag_ID   = (int) WPGlobus_WP::get_http_post_parameter( 'tag_ID' );
			$taxonomy = WPGlobus_WP::get_http_post_parameter( 'taxonomy' );

			self::$tag = get_term( $tag_ID, $taxonomy );

			if ( is_wp_error( self::$tag ) ) {

				$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE t.term_id = %d", $tag_ID ) );
				if ( ! empty( $terms[0] ) && is_object( $terms[0] ) ) {
					self::$tag = $terms[0];
				}
			}

			if ( is_wp_error( self::$tag ) ) {
				/**
				 * Todo Investigate.
				 */
				return;
			}

			$new_desc = array();

			foreach ( WPGlobus::Config()->enabled_languages as $lang ) :

				if ( $lang === self::$current_language ) {

					$text = trim( WPGlobus_WP::get_http_post_parameter( 'description' ) );
					if ( ! empty( $text ) ) {
						$new_desc[ $lang ] = $text;
					}
				} else {

					$text = WPGlobus_Core::text_filter( self::$tag->description, $lang, WPGlobus::RETURN_EMPTY );
					if ( ! empty( $text ) ) {
						$new_desc[ $lang ] = $text;
					}
				}

			endforeach;

			$_POST['description'] = WPGlobus_Utils::build_multilingual_string( $new_desc );
		}

		/**
		 * Filters term data before it is updated in the database.
		 *
		 * @param array  $data     Term data to be updated.
		 * @param int    $term_id  Term ID.
		 * @param string $taxonomy Taxonomy slug.
		 * @param array  $args     Arguments passed to wp_update_term().
		 *
		 * @return array
		 * @noinspection PhpUnusedParameterInspection
		 */
		public static function filter__update_term_data( $data, $term_id, $taxonomy, $args ) {

			if ( is_wp_error( self::$tag ) ) {
				/**
				 * Todo Investigate.
				 * maybe to use $args.
				 */
				return $data;
			}

			if ( ! self::$current_language ) {
				return $data;
			}

			$new_term_name = array();
			foreach ( WPGlobus::Config()->enabled_languages as $language ) :

				if ( $language === self::$current_language ) {

					$text = trim( $data['name'] );
					if ( ! empty( $text ) ) {
						$new_term_name[ $language ] = $text;
					}
				} else {

					$text = WPGlobus_Core::text_filter( self::$tag->name, $language, WPGlobus::RETURN_EMPTY );
					if ( ! empty( $text ) ) {
						$new_term_name[ $language ] = $text;
					}
				}

			endforeach;

			$data['name'] = WPGlobus_Utils::build_multilingual_string( $new_term_name );

			return $data;
		}

	} // class WPGlobus_RankMathSEO_Functions.

endif;
