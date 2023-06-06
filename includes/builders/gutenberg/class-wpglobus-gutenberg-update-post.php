<?php
/**
 * File: class-wpglobus-gutenberg-update-post.php
 *
 * @package WPGlobus\Builders\Gutenberg
 * Author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Gutenberg_Update_Post' ) ) :

	/**
	 * Class WPGlobus_Gutenberg_Update_Post.
	 */
	class WPGlobus_Gutenberg_Update_Post extends WPGlobus_Builder_Update_Post {

		/**
		 * Var language.
		 *
		 * @var string|null
		 */
		protected $language = null;

		/**
		 * Var $prepared_post
		 *
		 * @var WP_Post|null
		 */
		protected $prepared_post = null;

		/**
		 * Constructor.
		 */
		public function __construct() {

			parent::__construct( 'gutenberg' );

			/**
			 * Todo to save meta see request ($_REQUEST)
			 * post.php?post=259&action=edit&classic-editor=1&meta_box=1
			 */

			/**
			 * //if ( defined('DOING_AJAX') && DOING_AJAX ) {
			 * //}
			 */

			/**
			 * Filters order:
			 * 1. rest_pre_insert_post
			 * 2. wp_insert_post_data
			 * 3. rest_request_after_callbacks
			 */

			/**
			 * Check
			 *
			 * @see WP_REST_Posts_Controller::prepare_item_for_database
			 */
			if ( 'core' === WPGlobus::Config()->builder->get( 'context' ) ) {

				$post_type = WPGlobus::Config()->builder->get( 'post_type' );
				if ( ! empty( $post_type ) ) {
					add_filter( "rest_pre_insert_{$post_type}", array( $this, 'filter__pre_insert_post' ), 2, 2 );
				}
			} else {

				add_filter( 'rest_pre_insert_post', array( $this, 'filter__pre_insert_post' ), 2, 2 );
				add_filter( 'rest_pre_insert_page', array( $this, 'filter__pre_insert_post' ), 2, 2 );

			}

			/**
			 * Todo incorrect the saving post in extra languages with priority = 10
			 */
			add_filter( 'wp_insert_post_data', array( $this, 'filter__wp_insert_post_data' ), 100, 2 );

			/**
			 * In \wp-includes\rest-api\class-wp-rest-server.php
			 *
			 * @see WP_REST_Server::dispatch
			 */
			add_filter( 'rest_request_after_callbacks', array( $this, 'filter__rest_after_callbacks' ), 10, 3 );

		}

		/**
		 * Callback for 'rest_request_after_callbacks' will be fired after 'wp_insert_post_data' filter.
		 *
		 * @see          'filter__wp_insert_post_data'
		 *
		 * @param stdClass $response
		 * @param array    $handler
		 * @param mixed    $request Unused.
		 *
		 * @return mixed
		 * @noinspection PhpUnusedParameterInspection
		 */
		public function filter__rest_after_callbacks( $response, $handler, $request ) {

			if ( ! empty( $handler['methods']['POST'] ) && ! empty( $handler['methods']['PUT'] ) && ! empty( $handler['methods']['PATCH'] ) ) {
				/**
				 * Update post.
				 */

				/**
				 * This returns incorrect language
				 * $builder_language = get_post_meta($post_id, WPGlobus::Config()->builder->get_language_meta_key(), true);
				 *
				 * @todo check updating language meta after UPDATE post
				 */
				$builder_language = $this->language;

				/**
				 * // if ( empty( $builder_language ) ) {
				 * // @todo incorrect case
				 * // }
				 */

				$fix_title = true;
				if ( ! empty( $response->data['title']['raw'] ) && WPGlobus_Core::has_translations( $response->data['title']['raw'] ) ) {
					$response->data['title']['raw']      = WPGlobus_Core::text_filter( $response->data['title']['raw'], $builder_language, WPGlobus::RETURN_EMPTY );
					$response->data['title']['rendered'] = $response->data['title']['raw'];
					$fix_title                           = false;
				}

				// $fix_excerpt = true;
				if ( ! empty( $response->data['excerpt']['raw'] ) && WPGlobus_Core::has_translations( $response->data['excerpt']['raw'] ) ) {
					$excerpt_in_default = WPGlobus_Core::text_filter( $response->data['excerpt']['raw'], WPGlobus::Config()->default_language );
					//$excerpt 		    = WPGlobus_Core::text_filter($response->data['excerpt']['raw'], $builder_language);
					$excerpt                               = WPGlobus_Core::text_filter( $response->data['excerpt']['raw'], $builder_language, WPGlobus::RETURN_EMPTY );
					$response->data['excerpt']['raw']      = $excerpt;
					$response->data['excerpt']['rendered'] = str_replace( $excerpt_in_default, $excerpt, $response->data['excerpt']['rendered'] );
					// $fix_excerpt                           = false;
				}

				if ( WPGlobus::Config()->default_language === $builder_language ) {
					return $response;
				}

				if ( $fix_title ) :
					/**
					 * Fix the title.
					 * When we have title with different value:
					 * $response->data[title][raw] => Русский заголовок
					 * $response->data[title][rendered] => English title
					 */
					if ( empty( $response->data['title'] ) ) {
						return $response;
					}
					if ( empty( $response->data['title']['rendered'] ) || empty( $response->data['title']['raw'] ) ) {
						return $response;
					}

					if ( $response->data['title']['rendered'] !== $response->data['title']['raw'] ) {
						$response->data['title']['rendered'] = $response->data['title']['raw'];
					}

				endif;

			}

			return $response;
		}

		/**
		 * Callback for 'rest_pre_insert_post'.
		 *
		 * @param WP_Post $prepared_post
		 * @param mixed   $request Unused.
		 *
		 * @return mixed
		 * @noinspection PhpUnusedParameterInspection
		 */
		public function filter__pre_insert_post( $prepared_post, $request ) {

			global $wpdb;
			$_post = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d LIMIT 1", $prepared_post->ID ) );

			$builder_language = WPGlobus::Config()->builder->get_language();

			if ( empty( $builder_language ) ) {
				/**
				 * Check  superglobal variable $_SERVER or may be something other.
				 *
				 * @todo Probably we are working with WP Rest API here.
				 */
				$builder_language = get_post_meta( $prepared_post->ID, WPGlobus::get_language_meta_key(), true );

				if ( empty( $builder_language ) ) {
					/**
					 * Case when post is draft and has post_status 'auto-draft'.
					 */
					$builder_language = WPGlobus::Config()->default_language;
				}
			}

			$this->language = $builder_language;

			$fields = array();

			/**
			 * Post title.
			 */
			if ( ! empty( $prepared_post->post_title ) ) {
				$fields['post_title'] = $prepared_post->post_title;
				// } else {
				//$fields['post_title'] = '';
			}

			/**
			 * Post content.
			 */
			if ( ! empty( $prepared_post->post_content ) ) {
				$fields['post_content'] = $prepared_post->post_content;
			} else {
				$fields['post_content'] = '';
			}

			/**
			 * Post excerpt.
			 */
			if ( isset( $prepared_post->post_excerpt ) ) {
				if ( empty( $prepared_post->post_excerpt ) ) {
					/**
					 * Post excerpt was removed by user.
					 */
					$fields['post_excerpt'] = '';
				} else {
					$fields['post_excerpt'] = $prepared_post->post_excerpt;
				}
			} else {
				/**
				 * Post excerpt was not modified by user.
				 * so, we have unset '$prepared_post->post_excerpt' field.
				 */
				$fields['post_excerpt'] = WPGlobus_Core::text_filter( $_post->post_excerpt, $builder_language, WPGlobus::RETURN_EMPTY );
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

						$text = WPGlobus_Core::text_filter( $_post->$field, $lang, WPGlobus::RETURN_EMPTY );
						if ( ! empty( $text ) ) {
							$tr[ $lang ] = $text;
						}
					}

				endforeach;

				/**
				 * We are using `wp_slash` function.
				 *
				 * @link   https://github.com/WPGlobus/WPGlobus/pull/83
				 * @since  2.2.29
				 */
				$prepared_post->$field = wp_slash( WPGlobus_Utils::build_multilingual_string( $tr ) );

			}

			/**
			 * $this->_prepared_post contains 'post_title', 'post_content', 'post_excerpt' with language marks and ready to insert in DB.
			 */
			$this->prepared_post = clone $prepared_post;

			return $prepared_post;

		}

		/**
		 * Callback for 'wp_insert_post_data'.
		 *
		 * @param array $data
		 * @param mixed $postarr Unused.
		 *
		 * @return array
		 * @noinspection PhpUnusedParameterInspection
		 */
		public function filter__wp_insert_post_data( $data, $postarr ) {

			/**
			 * Check $this->_prepared_post was loaded with first XMLHttpRequest.
			 *
			 * @see 'filter__pre_insert_post' filter.
			 * @see Network tab in browser console.
			 */
			if ( ! is_object( $this->prepared_post ) ) {
				return $data;
			}

			$_fields = array( 'post_title', 'post_content', 'post_excerpt' );
			foreach ( $_fields as $_field ) {

				if ( ! empty( $data[ $_field ] ) && ! empty( $this->prepared_post->$_field ) ) {
					$data[ $_field ] = $this->prepared_post->$_field;
				}
			}

			return $data;
		}

	}

endif;
