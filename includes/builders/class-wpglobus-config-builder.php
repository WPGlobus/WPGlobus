<?php
/**
 * File: class-wpglobus-config-builder.php
 *
 * @package WPGlobus\Builders
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Config_Builder' ) ) :

	class WPGlobus_Config_Builder {

		/**
		 * @var string|false
		 */
		protected $id = false;

		protected $is_run = false;

		/**
		 * May be to use "wpglobus_language" meta (reserved in WPGlobus).
		 *
		 * @todo remove after test
		 */
		//protected $language_meta_key = '_wpglobus_builder_language';

		protected $language_cookie = 'wpglobus-builder-language';

		protected $attrs = array();

		protected $__class = null;

		protected $__builder_page = false;

		protected $__is_admin = false;

		protected $language = false;

		protected $default_language;

		/**
		 * Constructor.
		 *
		 * @param bool  $init
		 * @param array $init_attrs
		 */
		public function __construct( $init = true, $init_attrs = array() ) {

			if ( isset( $init_attrs['default_language'] ) ) {
				$this->default_language = $init_attrs['default_language'];
			}

			if ( $init ) {

				require_once dirname( __FILE__ ) . '/class-wpglobus-builders.php';
				$builder = WPGlobus_Builders::get();

				$this->id = $builder['id'];
				unset( $builder['id'] );

				if ( $this->id ) {

					$this->attrs['version'] = null;

					foreach ( $builder as $key => $value ) {
						if ( 'class' === $key ) {
							$this->__class = $value;
						} elseif ( 'builder_page' === $key ) {
							$this->__builder_page = $value;
						} elseif ( 'is_admin' === $key ) {
							$this->__is_admin = $value;
						}
						$this->attrs[ $key ] = $value;
					}

					$this->language          = $this->get_language();
					$this->attrs['language'] = $this->language;

				} else {
					unset( $this->attrs );
				}
			} else {

				require_once dirname( __FILE__ ) . '/class-wpglobus-builders.php';
				/** @noinspection PhpUnusedLocalVariableInspection */
				$builder = WPGlobus_Builders::get( false );

			}

		}

		/**
		 * Try to run builder.
		 *
		 * @param string $builder
		 * @param bool   $set_run_flag
		 *
		 * @return bool
		 */
		public function maybe_run( $builder = '', $set_run_flag = false ) {

			//if ( defined('DOING_AJAX') && DOING_AJAX ) {
			//return false;
			//}

			if ( ! $this->id ) {
				return false;
			}

			$check_run_flag = true;

			if ( is_bool( $builder ) ) {
				// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIF
				if ( $builder ) {
					// @todo
				} else {
					$check_run_flag = false;
					$set_run_flag   = false;
				}
			}

			if ( $check_run_flag && $this->is_run ) {
				/**
				 * Don't run again.
				 */
				return false;
			}

			if ( '' === $builder ) {
				$builder = $this->id;
			}

			if ( ! $builder ) {
				return false;
			}

			if ( $builder !== $this->id ) {
				return false;
			}

			if ( $this->is_front() ) {
				if ( $set_run_flag ) {
					$this->is_run = true;
				}

				return true;
			}

			if ( $this->is_builder_page() ) {
				if ( $set_run_flag ) {
					$this->is_run = true;
				}

				return true;
			}

			return false;

		}

		/**
		 * Get attribute.
		 *
		 * @param string $attr
		 *
		 * @return bool|mixed
		 */
		public function get( $attr = 'id' ) {
			if ( ! $this->id ) {
				return false;
			}
			if ( 'id' === $attr ) {
				return $this->get_id();
			}
			if ( ! empty( $this->attrs[ $attr ] ) ) {
				return $this->attrs[ $attr ];
			}

			return false;
		}

		/**
		 * Set builder language.
		 *
		 * @param string $language
		 */
		public function set_language( $language = '' ) {
			if ( ! empty( $language ) ) {
				$this->language          = $language;
				$this->attrs['language'] = $this->language;
			}
		}

		/**
		 * Get builder language.
		 *
		 * @param int|string $post_id
		 *
		 * @return array|bool|mixed|string
		 */
		public function get_language( $post_id = '' ) {

			if ( ! $this->id ) {
				return false;
			}

			if ( ! $this->is_builder_page() ) {
				/**
				 * @todo maybe need to check the matching of $this->language and WPGlobus::Config()->language.
				 * @see  Set language for builder in wpglobus\includes\class-wpglobus-config.php
				 */
				return $this->language;
			}

			if ( $this->language ) {
				return $this->language;
			}

			/** @global string $pagenow */
			global $pagenow;

			if ( 'post-new.php' === $pagenow ) {
				/**
				 * Correctly define language for the 'post-new.php' page.
				 *
				 * @since 2.1.1
				 */
				$this->language = $this->default_language;

				return $this->language;
			}

			$post_id = (int) $post_id;
			if ( ! $post_id ) {
				// Post ID not passed..getting from global Post.
				$global_post = get_post();
				if ( $global_post instanceof WP_Post ) {
					$post_id = $global_post->ID;
				}
			}

			$language = false;
			if ( $post_id ) {
				$language = get_post_meta( $post_id, $this->get_language_meta_key(), true );
			}

			if ( ! $language ) {

				if ( empty( $_REQUEST ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification

					if ( empty( $_SERVER['HTTP_REFERER'] ) ) {
						/**
						 * @todo front-end? check it.
						 */
						return false;

					} elseif ( false !== strpos( $_SERVER['HTTP_REFERER'], 'language=' ) ) {
						$language = explode( 'language=', $_SERVER['HTTP_REFERER'] );
						$language = $language[1];
					}
				} else {

					if ( ! empty( $_REQUEST['language'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$language = sanitize_text_field( $_REQUEST['language'] );
					}

					if ( isset( $_REQUEST[ WPGlobus::get_language_meta_key() ] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$language = sanitize_text_field( $_REQUEST[ WPGlobus::get_language_meta_key() ] );
					}
				}
			}

			if ( ! $language ) {

				if ( isset( $_REQUEST['post'] ) && 0 !== (int) $_REQUEST['post'] ) { // phpcs:ignore WordPress.CSRF.NonceVerification

					$language = get_post_meta( $_REQUEST['post'], $this->get_language_meta_key(), true ); // phpcs:ignore WordPress.CSRF.NonceVerification

				} elseif ( isset( $_REQUEST['id'] ) && 0 !== (int) $_REQUEST['id'] ) { // phpcs:ignore WordPress.CSRF.NonceVerification

					/**
					 * Case when post in draft status is autosaved.
					 */
					$language = get_post_meta( $_REQUEST['id'], $this->get_language_meta_key(), true ); // phpcs:ignore WordPress.CSRF.NonceVerification

				} elseif ( isset( $_SERVER['REQUEST_URI'] ) ) {

					/**
					 * See also the Update action in @see \WPGlobus_Builders
					 */
					$_continue = false;
					if ( isset( $GLOBALS['WPGlobus'] ) && ! empty( $GLOBALS['WPGlobus']['post_type'] ) ) {
						$_continue = true;
					}
					if ( false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/wp/v2/posts/' )
						 || false !== strpos( $_SERVER['REQUEST_URI'], '/wp-json/wp/v2/pages/' )
						 || $_continue ) {
						/**
						 * Case when post status was changed ( draft->publish or publish->draft ) in Gutenberg.
						 *
						 * @see \WPGlobus_Builders::is_gutenberg()
						 */
						if ( isset( $GLOBALS['WPGlobus'] ) && ! empty( $GLOBALS['WPGlobus']['post_id'] ) ) {
							$post_id = $GLOBALS['WPGlobus']['post_id'];
						} else {
							$_request_uri = explode( '/', $_SERVER['REQUEST_URI'] );

							$post_id = end( $_request_uri );
							$post_id = preg_replace( '/\?.*/', '', $post_id );
						}

						if ( 0 !== (int) $post_id ) {
							$language = get_post_meta( $post_id, $this->get_language_meta_key(), true ); // phpcs:ignore WordPress.CSRF.NonceVerification
						}
					}
				}
			}

			if ( ! $language ) {
				if ( $this->get_post_id() ) {
					$language = get_post_meta( $this->get_post_id(), $this->get_language_meta_key(), true );
				}
			}

			if ( ! $language && ! empty( $this->default_language ) ) {
				/**
				 * Possible options when the language is not defined:
				 * - new post, post-new.php page;
				 */
				$language = $this->default_language;
				/**
				 * @todo test point if was incorrect setting of $language.
				 */

			}

			$this->language = $language;

			return $language;

		}

		/**
		 * Check if builder is run.
		 */
		public function is_run() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->is_run;
		}

		/**
		 * Check if builder is run.
		 */
		public function is_running() {
			return $this->is_run();
		}

		/**
		 * Check if builder is in admin.
		 */
		public function is_admin() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->__is_admin;
		}

		/**
		 * Check if builder is in front.
		 */
		public function is_front() {
			if ( ! $this->id ) {
				return false;
			}

			return ! $this->__is_admin;
		}

		/**
		 * Get builder ID.
		 */
		public function get_id() {
			return $this->id;
		}

		/**
		 * Get post ID.
		 */
		public function get_post_id() {
			if ( isset( $this->attrs['post_id'] ) && (int) $this->attrs['post_id'] > 0 ) {
				return $this->attrs['post_id'];
			}

			return false;
		}

		/**
		 * Get builder class.
		 */
		public function get_class() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->__class;
		}

		/**
		 *
		 */
		public function get_language_meta_key() {
			if ( ! $this->id ) {
				return false;
			}

			return WPGlobus::get_language_meta_key();
		}

		/**
		 *
		 */
		public function get_cookie_name() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->language_cookie;
		}

		/**
		 * @param string $cookie_name
		 *
		 * @return bool|null
		 */
		public function get_cookie( $cookie_name = '' ) {

			if ( ! $this->id ) {
				return false;
			}

			static $_cookie_value = null;

			if ( is_null( $_cookie_value ) ) {
				if ( empty( $cookie_name ) ) {
					$cookie_name = $this->get_cookie_name();
				}
				if ( empty( $_COOKIE[ $cookie_name ] ) ) {
					$_cookie_value = false;
				} else {
					$_cookie_value = $_COOKIE[ $cookie_name ];
				}
			}

			return $_cookie_value;
		}

		/**
		 * Check if current page is builder's page.
		 */
		public function is_builder_page() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->__builder_page;
		}

		/**
		 * Get all builder data.
		 */
		public function get_data() {

			if ( ! $this->id ) {
				return false;
			}

			$data       = array();
			$data['id'] = $this->get_id();

			if ( empty( $data['id'] ) ) {
				return false;
			}

			if ( ! empty( $this->attrs ) ) {
				foreach ( $this->attrs as $key => $value ) {
					$data[ $key ] = $value;
				}
			}

			$data['language'] = $this->get_language();

			return $data;

		}

		/**
		 * Set multilingual fields.
		 *
		 * @param array $multilingual_fields
		 */
		public function set_multilingual_fields( $multilingual_fields ) {

			if ( ! isset( $this->attrs ) ) {
				return;
			}

			if ( is_array( $multilingual_fields ) && ! empty( $multilingual_fields ) ) {
				$this->attrs['multilingualFields'] = array_merge( $this->attrs['multilingualFields'], $multilingual_fields );
			}
		}

	}

endif;
