<?php
/**
 * File: class-wpglobus-config-builder.php
 *
 * @package WPGlobus\Builders
 * Author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Config_Builder' ) ) :

	class WPGlobus_Config_Builder {

		/**
		 * ID
		 *
		 * @var string|false
		 */
		protected $id = false;

		protected $is_run = false;

		protected $language_cookie = 'wpglobus-builder-language';

		protected $attrs = array();

		protected $the_class = null;

		protected $builder_page = false;

		protected $is_admin = false;

		protected $language = false;

		protected $default_language;

		/**
		 * Var post_types
		 *
		 * @since 2.2.11
		 */
		public $post_types = array();

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

			/**
			 * Check $init_attrs['post_types']
			 *
			 * @since 2.2.11
			 */
			if ( isset( $init_attrs['post_types'] ) ) {
				$this->post_types = $init_attrs['post_types'];
			}

			if ( $init ) {

				require_once dirname( __FILE__ ) . '/class-wpglobus-builders.php';
				/**
				 * Added $init_attrs.
				 *
				 * @since 2.2.24
				 */
				$builder = WPGlobus_Builders::get( true, $init_attrs );

				$this->id = $builder['id'];
				unset( $builder['id'] );

				if ( $this->id ) {

					$this->attrs['version'] = null;

					foreach ( $builder as $key => $value ) {
						if ( 'class' === $key ) {
							$this->the_class = $value;
						} elseif ( 'builder_page' === $key ) {
							$this->builder_page = $value;
						} elseif ( 'is_admin' === $key ) {
							$this->is_admin = $value;
						}
						$this->attrs[ $key ] = $value;
					}

					/**
					 * Get REST API request language
					 * For example for setting language see wpglobus\includes\builders\rank_math_seo\class-wpglobus-builder-rank_math_seo.php
					 *
					 * @since 2.8.9
					 */
					if ( isset( $builder['rest_request'] ) && $builder['rest_request'] && isset( $builder['rest_language'] ) ) {
						$this->set_language( $builder['rest_language'] );
					} else {
						$this->language          = $this->get_language();
						$this->attrs['language'] = $this->language;
					}

				} else {
					unset( $this->attrs );
				}
			} else {

				require_once dirname( __FILE__ ) . '/class-wpglobus-builders.php';
				/**
				 * Need it?
				 *
				 * @noinspection PhpUnusedLocalVariableInspection
				 */
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
			/**
			 * //if ( defined('DOING_AJAX') && DOING_AJAX ) {
			 * //return false;
			 * //}
			 */
			if ( ! $this->id ) {
				return false;
			}

			$check_run_flag = true;

			if ( is_bool( $builder ) ) {
				if ( ! $builder ) {
					$check_run_flag = false;
					$set_run_flag   = false;
				} // Else TODO
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
				 * Todo maybe need to check the matching of $this->language and WPGlobus::Config()->language.
				 * See Set language for builder in wpglobus\includes\class-wpglobus-config.php
				 */
				return $this->language;
			}

			if ( $this->language ) {
				return $this->language;
			}

			if ( WPGlobus_WP::is_pagenow( 'post-new.php' ) ) {
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

				0 && wp_verify_nonce( '' );
				if ( empty( $_REQUEST ) ) {

					$_SERVER_HTTP_REFERER = WPGlobus_WP::http_referer();
					if ( empty( $_SERVER_HTTP_REFERER ) ) {
						/**
						 * Todo front-end? check it.
						 */
						return false;

					} elseif ( false !== strpos( $_SERVER_HTTP_REFERER, 'language=' ) ) {
						$language = explode( 'language=', $_SERVER_HTTP_REFERER );
						$language = $language[1];
					}
				} else {

					if ( ! empty( $_REQUEST['language'] ) ) {
						$language = sanitize_text_field( $_REQUEST['language'] );
					}

					if ( isset( $_REQUEST[ WPGlobus::get_language_meta_key() ] ) ) {
						$language = sanitize_text_field( $_REQUEST[ WPGlobus::get_language_meta_key() ] );
					}
				}
			}

			if ( ! $language ) {

				$_REQUEST_post       = WPGlobus_WP::get_http_request_parameter( 'post' );
				$_REQUEST_id         = WPGlobus_WP::get_http_request_parameter( 'id' );
				$_SERVER_REQUEST_URI = WPGlobus_WP::request_uri();

				if ( 0 !== (int) $_REQUEST_post ) {

					$language = get_post_meta( $_REQUEST_post, $this->get_language_meta_key(), true );

				} elseif ( 0 !== (int) $_REQUEST_id ) {

					/**
					 * Case when post in draft status is autosaved.
					 */
					$language = get_post_meta( $_REQUEST_id, $this->get_language_meta_key(), true );

				} elseif ( $_SERVER_REQUEST_URI ) {

					/**
					 * See also the Update action in @see WPGlobus_Builders
					 */
					$_continue = false;

					/**
					 * Todo  In a rare case (so far only one) $GLOBALS['WPGlobus'] defined as object. Need an investigation.
					 *
					 * @since 2.5.17 Check $GLOBALS['WPGlobus'] for an array to prevent an occurring error `Cannot use object of type WPGlobus as array`.
					 */
					if ( isset( $GLOBALS['WPGlobus'] ) &&
						 is_array( $GLOBALS['WPGlobus'] ) &&
						 ! empty( $GLOBALS['WPGlobus']['post_type'] )
					) {
						$_continue = true;
					}
					if ( false !== strpos( $_SERVER_REQUEST_URI, '/wp-json/wp/v2/posts/' )
						 || false !== strpos( $_SERVER_REQUEST_URI, '/wp-json/wp/v2/pages/' )
						 || $_continue ) {
						/**
						 * Case when post status was changed ( draft->publish or publish->draft ) in Gutenberg.
						 *
						 * @see WPGlobus_Builders::is_gutenberg()
						 */
						if ( isset( $GLOBALS['WPGlobus'] ) && ! empty( $GLOBALS['WPGlobus']['post_id'] ) ) {
							$post_id = $GLOBALS['WPGlobus']['post_id'];
						} else {
							$_request_uri = explode( '/', $_SERVER_REQUEST_URI );

							$post_id = end( $_request_uri );
							$post_id = preg_replace( '/\?.*/', '', $post_id );
						}

						if ( 0 !== (int) $post_id ) {
							$language = get_post_meta( $post_id, $this->get_language_meta_key(), true );
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
				 * Todo test point if was incorrect setting of $language.
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

			return $this->is_admin;
		}

		/**
		 * Check if builder is in front.
		 */
		public function is_front() {
			if ( ! $this->id ) {
				return false;
			}

			return ! $this->is_admin;
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

			return $this->the_class;
		}

		/**
		 * Method get_language_meta_key
		 */
		public function get_language_meta_key() {
			if ( ! $this->id ) {
				return false;
			}

			return WPGlobus::get_language_meta_key();
		}

		/**
		 * Method get_cookie_name
		 */
		public function get_cookie_name() {
			if ( ! $this->id ) {
				return false;
			}

			return $this->language_cookie;
		}

		/**
		 * Method get_cookie
		 *
		 * @param string $cookie_name
		 *
		 * @return bool|null
		 * @noinspection PhpUnused
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
					$_cookie_value = sanitize_text_field( $_COOKIE[ $cookie_name ] );
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

			return $this->builder_page;
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
		 * If $this->default_language was not set, returns `null`.
		 * If the language is equal to the default_language, returns `true`,
		 * otherwise it returns `false`.
		 *
		 * @since 2.2.6
		 *
		 * @return null|boolean
		 */
		public function is_default_language() {

			if ( empty( $this->default_language ) ) {
				return null;
			}
			if ( $this->language === $this->default_language ) {
				return true;
			}

			return false;
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
