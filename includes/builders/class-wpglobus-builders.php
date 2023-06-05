<?php
/**
 * File: class-wpglobus-builders.php
 *
 * @package WPGlobus\Builders
 * Author  Alex Gor(alexgff)
 */

/**
 * Elementor.
 *
 * @since        2.4.11
 * @noinspection PhpUndefinedNamespaceInspection
 * @noinspection PhpUndefinedClassInspection
 */

use Elementor\Modules\Gutenberg;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class WPGlobus_Builders.
 */
if ( ! class_exists( 'WPGlobus_Builders' ) ) :

	class WPGlobus_Builders {

		/**
		 * Attrs
		 *
		 * @var array
		 */
		protected static $attrs = array();

		/**
		 * Var $admin_attrs
		 *
		 * @var array
		 */
		protected static $admin_attrs = array();

		/**
		 * Var $add_on
		 *
		 * @var array
		 */
		protected static $add_on = array();

		/**
		 * Var $post_type
		 *
		 * @since 2.2.11
		 * @var string
		 */
		protected static $post_type = null;

		/**
		 * Var $init_attrs
		 *
		 * @since 2.2.24
		 * @var array
		 */
		protected static $init_attrs = null;

		/**
		 * Method get_addons
		 *
		 * @return array
		 */
		public static function get_addons() {

			if ( ! empty( self::$add_on ) ) {
				return self::$add_on;
			}

			global $wp_version;

			self::$add_on['gutenberg'] = array(
				'id'                      => 'gutenberg',
				'role'                    => 'builder',
				'admin_bar_label'         => version_compare( $wp_version, '4.9.99', '>' ) ? 'Core' : 'Builder',
				'supported_min_version'   => '4.0.0',
				'const'                   => 'GUTENBERG_VERSION',
				'plugin_name'             => 'Gutenberg',
				'admin_bar_builder_label' => '',
				'plugin_uri'              => 'https://github.com/WordPress/gutenberg',
				'path'                    => 'gutenberg/gutenberg.php',
				'stage'                   => 'production',
			);

			self::$add_on['js_composer'] = array(
				'id'                      => 'js_composer',
				'role'                    => 'builder',
				'supported_min_version'   => '5.4.0',
				'const'                   => 'WPB_VC_VERSION',
				'plugin_name'             => 'WPBakery Page Builder',
				'admin_bar_builder_label' => 'WPBakery PB',
				'plugin_uri'              => 'https://wpbakery.com/',
				'path'                    => 'js_composer/js_composer.php',
				'stage'                   => 'production',
			);

			self::$add_on['elementor'] = array(
				'id'                    => 'elementor',
				'role'                  => 'builder',
				'supported_min_version' => '2.5.14',
				'const'                 => 'ELEMENTOR_VERSION',
				'plugin_name'           => 'Elementor',
				'plugin_uri'            => 'https://wordpress.org/plugins/elementor/',
				'path'                  => 'elementor/elementor.php',
				'stage'                 => 'beta',
				'beta_version'          => '3',
			);

			/**
			 * Yoast SEO add-ons.
			 *
			 * @since 2.3.11
			 * @since 2.8.4 Moved to separate file.
			 */
			$_addon_dir = dirname( __FILE__ ) . '/yoast_seo/addon-yoast_seo.php';
			if ( file_exists( $_addon_dir ) ) {
				require_once $_addon_dir;
			}

			/**
			 * WooCommerce.
			 */
			self::$add_on['woocommerce'] = array(
				'id'                    => 'woocommerce',
				'role'                  => 'add-on',
				'config_file'           => 'woocommerce.json',
				'supported_min_version' => '3.5.1',
				'const'                 => 'WC_PLUGIN_FILE',
				'plugin_name'           => 'WooCommerce',
				'plugin_uri'            => 'https://woocommerce.com',
				'path'                  => 'woocommerce/woocommerce.php',
				'stage'                 => 'production',
			);

			/**
			 * Config $add_on['pods']
			 *
			 * @since 2.3.0
			 */
			self::$add_on['pods'] = array(
				'id'                      => 'pods',
				'role'                    => 'builder',
				'admin_bar_label'         => 'Add-on',
				'config_file'             => 'pods.json',
				'supported_min_version'   => '2.7.16',
				'const'                   => 'PODS_VERSION',
				'plugin_name'             => 'Pods-Custom Content Types and Fields',
				'plugin_uri'              => 'https://wordpress.org/plugins/pods/',
				'path'                    => 'pods/init.php',
				'stage'                   => 'production',
				'admin_bar_builder_label' => 'Pods',
			);

			/**
			 * Config $add_on['rank_math_seo']
			 *
			 * @since 2.4.3
			 */
			self::$add_on['rank_math_seo'] = array(
				'id'                      => 'rank_math_seo',
				'role'                    => 'builder',
				'admin_bar_label'         => 'Add-on',
				'config_file'             => 'rank-math-seo.json',
				'supported_min_version'   => '1.0.42',
				'const'                   => 'RANK_MATH_VERSION',
				'plugin_name'             => 'Rank Math SEO',
				'plugin_uri'              => 'https://wordpress.org/plugins/seo-by-rank-math/',
				'path'                    => 'seo-by-rank-math/rank-math.php',
				'stage'                   => 'production',
				'admin_bar_builder_label' => 'Rank Math SEO',
			);

			/**
			 * Unused
			 * self::$add_on['wp-subtitle'] = array(
			 * 'id'                    => 'wp-subtitle',
			 * 'role'                    => 'add-on',
			 * 'config_file'            => 'wp-subtitle.json',
			 * 'supported_min_version' => '3.1',
			 * 'const'                 => 'WPSUBTITLE_DIR',
			 * 'plugin_name'           => 'WP Subtitle',
			 * 'plugin_uri'            => 'http://wordpress.org/plugins/wp-subtitle/',
			 * 'path'                  => 'wp-subtitle/wp-subtitle.php',
			 * 'stage'                 => 'production',
			 * );
			 * // */

			/**
			 * Test
			 * self::$add_on['__test'] = array(
			 * 'id'                    => '__test',
			 * 'supported_min_version' => '1.0',
			 * 'const'                 => '__TEST_VERSION',
			 * 'plugin_name'           => 'Test Add-on',
			 * 'plugin_uri'            => '',
			 * 'path'                    => 'test-add-on/test-add-on.php',
			 * );
			 * // */

			return self::$add_on;
		}

		/**
		 * Method get_addon
		 *
		 * @param bool $builder
		 *
		 * @return false|array
		 */
		public static function get_addon( $builder = false ) {
			if ( ! $builder ) {
				return false;
			}
			if ( isset( self::$add_on[ $builder ] ) ) {
				return self::$add_on[ $builder ];
			}

			return false;
		}

		/**
		 * Method get
		 *
		 * @param bool  $init
		 * @param array $init_attrs added @since 2.2.24
		 *
		 * @return array|bool
		 */
		public static function get( $init = true, $init_attrs = array() ) {

			/**
			 * Unused
			 * // if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			 * //return false;
			 * // }*/

			/**
			 * Bail out on empty
			 *
			 * @since 2.2.26
			 */
			if ( empty( $init_attrs ) ) {
				return false;
			}

			/**
			 * Set self::$init_attrs
			 *
			 * @since 2.2.24
			 */
			self::$init_attrs = $init_attrs;

			/**
			 * Set $post_types
			 *
			 * @since 2.2.24
			 */
			$post_types = $init_attrs['post_types'];

			/**
			 * Global
			 *
			 * @global string $pagenow
			 */
			global $pagenow;

			self::$attrs = array(
				'id'              => false,
				'context'         => 'add-on',
				'version'         => '',
				'class'           => '',
				'post_type'       => '',
				'post_id'         => '',
				'is_admin'        => true,
				'pagenow'         => $pagenow,
				'builder_page'    => false,
				'doing_ajax'      => WPGlobus_WP::is_doing_ajax(),
				'language'        => '',
				'message'         => '',
				'ajax_actions'    => '',
				'builder_support' => true, // @since 2.4.12
				'taxonomy'        => '',   // @since 2.8.9
				'tag_ID'          => '',   // @since 2.8.9
				'rest_request'    => WPGlobus_WP::is_rest_api_request(), // @since 2.8.9
			);

			self::$admin_attrs = array(
				'multilingualFields' => array( 'post_title', 'excerpt' ),
				'translatableClass'  => 'wpglobus-translatable',
			);

			/**
			 * Check $pagenow
			 *
			 * @since 2.2.11
			 */
			if ( WPGlobus_WP::is_pagenow( array( 'post.php', 'post-new.php', 'media-new.php' ) ) ) {

				/**
				 * W.I.P
				 *
				 * @since 2.2.14
				 * if ( in_array( $pagenow, array('post.php', 'post-new.php', 'media-new.php', 'admin-ajax.php') ) ) {
				 */
				$post_type = self::get_post_type_2();

				/**
				 * Filter for post types.
				 *
				 * @since 2.2.11
				 *
				 * @param array  $post_types Array of post types.
				 * @param string $post_type  Current post type.
				 *
				 * @return array
				 */
				$post_types = apply_filters( 'wpglobus_builders_post_types', $post_types, $post_type );

				if ( ! array_key_exists( $post_type, $post_types ) ) {
					return self::$attrs;
				} elseif ( ! $post_types[ $post_type ] ) {
					return self::$attrs;
				}
			}

			if ( $init ) {

				self::get_addons();

				/**
				 * Set $builder
				 *
				 * @since 1.9.17
				 */
				$builder = self::is_gutenberg();
				if ( $builder && $builder['builder_page'] ) {
					return $builder;
				}

				/**
				 * JS Composer.
				 *
				 * @since 1.9.17
				 * @since 2.2.3 Start js_composer as a builder.
				 */
				if ( ! $builder || ! $builder['builder_page'] ) {
					$builder = self::is_js_composer();
					if ( $builder && $builder['builder_page'] ) {
						return $builder;
					}
				}

				/**
				 * Elementor.
				 *
				 * @since 1.9.17
				 */
				if ( ! $builder || ! $builder['builder_page'] ) {
					$builder = self::is_elementor();
					if ( $builder ) {
						if ( $builder['is_admin'] ) {
							if ( $builder['builder_page'] ) {
								return $builder;
							}
						} else {
							include_once WPGlobus::$PLUGIN_DIR_PATH . 'includes/builders/elementor/class-wpglobus-elementor-front.php';
							WPGlobus_Elementor_Front::init( $builder );
						}
					}
				}

				/**
				 * W.I.P
				 *
				 * @since 1.9.17
				 * $builder = self::is_siteorigin_panels();
				 * if ( $builder ) {
				 * return $builder;
				 * }
				 * // */

				/**
				 * Check $builder
				 *
				 * @since 1.9.17
				 */
				if ( ! $builder || ! $builder['builder_page'] ) {
					$builder = self::is_yoast_seo();
					if ( $builder && $builder['builder_page'] ) {
						return $builder;
					}
				}

				/**
				 * Pods – Custom Content Types and Fields.
				 *
				 * @since 2.3.0
				 */
				if ( ! $builder || ! $builder['builder_page'] ) {
					$builder = self::is_pods();
					if ( $builder && $builder['builder_page'] ) {
						return $builder;
					}

				}

				/**
				 * Rank Math SEO.
				 *
				 * @since 2.4.3
				 */
				if ( ! $builder || ! $builder['builder_page'] ) {
					$builder = self::is_rank_math_seo();
					if ( $builder && $builder['builder_page'] ) {
						return $builder;
					}

				}
			}

			return self::$attrs;

		}

		/**
		 * Page Builder by SiteOrigin.
		 *
		 * W.I.P
		 *
		 * @link         https://wordpress.org/plugins/siteorigin-panels/
		 * @noinspection PhpUnused
		 */
		protected static function is_siteorigin_panels() {
		}

		/**
		 * Elementor Page Builder.
		 * https://wordpress.org/plugins/elementor/
		 */
		protected static function is_elementor() {

			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return false;
			}

			$__builder = self::get_addon( 'elementor' );

			if ( ! $__builder ) {
				return false;
			}

			$load_elementor = false;

			if ( version_compare( ELEMENTOR_VERSION, $__builder['supported_min_version'], '<' ) ) {

				$message = 'Unsupported Elementor version.';

				$_attrs = array(
					'id'           => 'elementor',
					'version'      => ELEMENTOR_VERSION,
					'class'        => 'WPGlobus_Elementor',
					'is_admin'     => false,
					'builder_page' => false,
					'message'      => $message,
				);

				return self::get_attrs( $_attrs );

			} else {

				if ( WPGlobus_WP::is_pagenow( array( 'admin-ajax.php', 'post.php', 'index.php', 'post-new.php' ) ) ) {

					/**
					 * Init current post type.
					 */
					$post_type = is_null( self::$post_type ) ? '' : self::$post_type;

					/**
					 * Init post ID.
					 */
					$post_id = '';

					/**
					 * Init `builder_support`.
					 *
					 * @since 2.4.12
					 */
					$builder_support = true;

					$ajax_actions = '';
					$is_admin     = true;

					if ( WPGlobus_WP::is_pagenow( 'admin-ajax.php' ) ) {

						$_REQUEST_action = WPGlobus_WP::get_http_request_parameter( 'action' );
						if ( 'elementor_ajax' !== $_REQUEST_action ) {
							return false;
						}
						if ( WPGlobus_WP::is_strpos_http_request( 'actions', 'save_builder' ) ) {
							$ajax_actions = 'save_builder';
						} elseif ( WPGlobus_WP::is_strpos_http_request( 'actions', '"action":"render_widget"' ) ) {
							$ajax_actions = 'render_widget';
						} else {
							return false;
						}
						$load_elementor = true;

						$post_id = WPGlobus_WP::get_http_request_parameter( 'editor_post_id' );

					} elseif ( WPGlobus_WP::is_pagenow( 'index.php' ) ) {

						/**
						 * Todo remove after testing.
						 * if ( ! isset( $_GET['elementor-preview'] ) ) {
						 * return false;
						 * }
						 * // @W.I.P @since 2.2.11
						 * // [REQUEST_URI] => /?p=75&elementor-preview=75&ver=1561202861
						 * // */

						$load_elementor  = false;
						$is_admin        = false;
						$builder_support = null; // @since 2.4.12

						/**
						 * W.I.P
						 *
						 * @since 2.2.11 [REQUEST_URI] was changed to `?p=75&preview_id=75&preview_nonce=da660129a7&preview=true`.
						 * @todo  Preview page for draft status.
						 *        [REQUEST_URI] => /?p=75&elementor-preview=75&ver=1561202861
						 * if ( ! empty( $_GET['p'] ) ) {
						 * $load_elementor = true;
						 * $is_admin       = true;
						 * $post_id        = sanitize_text_field( $_GET['p'] );
						 * $post_type        = self::get_post_type($post_id);
						 *
						 * }
						 * // */

					} elseif ( WPGlobus_WP::is_pagenow( 'post.php' ) ) {

						$is_admin = true;
						if ( 'elementor' === WPGlobus_WP::get_http_get_parameter( 'action' ) ) {
							// No need this? $is_admin = false;
							$load_elementor = true;
						}

						/**
						 * $cpt_support = get_option( 'elementor_cpt_support', array('page', 'post') );
						 *
						 * @see_file elementor\includes\plugin.php
						 */
						$cpt_support = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );

						/**
						 * For post-new.php page.
						 */
						$post_type = WPGlobus_WP::get_http_get_parameter( 'post_type' );
						$_GET_post = WPGlobus_WP::get_http_get_parameter( 'post' );

						if ( empty( $post_type ) ) {
							$_REQUEST_post_ID = WPGlobus_WP::get_http_request_parameter( 'post_ID' );
							if ( $_GET_post ) {
								$post_type = self::get_post_type( $_GET_post );
							} elseif ( $_REQUEST_post_ID ) {
								$post_type = self::get_post_type( $_REQUEST_post_ID );
							}
						}

						/**
						 * Set $post_id
						 *
						 * @since 2.4.12
						 */
						if ( $_GET_post ) {
							$post_id = $_GET_post;
						}

						/**
						 * Post type by default.
						 * If we can not define post type then we don't set it to default value.
						 * Because it may cause incorrect behavior later.
						 * // if ( empty( $post_type ) ) {
						 * //$post_type = 'post';
						 * // }
						 */

						if ( in_array( $post_type, $cpt_support, true ) ) {
							$load_elementor = true;
						}

						/**
						 * If $load_elementor
						 *
						 * @since 2.4.12
						 */
						if ( $load_elementor ) {

							if ( ! empty( $post_id ) && (int) $post_id > 0 ) {

								$wpglobus_elementor_support = get_post_meta( $post_id, '_wpglobus_elementor_support', true );
								if ( 'off' === $wpglobus_elementor_support ) {
									$builder_support = false;
								}

								if ( $builder_support ) {

									$elementor_edit_mode = get_post_meta( $post_id, '_elementor_edit_mode', true );

									if ( 'builder' !== $elementor_edit_mode ) {
										/**
										 * Disable elementor support for post, that doesn't use elementor builder.
										 */
										$builder_support = false;
									}
								}
							}
						}

					} else {
						/**
						 * Todo maybe use @see is_built_with_elementor() in elementor\core\base\document.php
						 */
						$load_elementor = true;
					}

					$_attrs = array(
						'id'                           => 'elementor',
						'version'                      => ELEMENTOR_VERSION,
						'is_admin'                     => $is_admin,
						'class'                        => 'WPGlobus_Elementor',
						'post_type'                    => $post_type,
						'post_id'                      => $post_id,
						'builder_page'                 => false,
						'ajax_actions'                 => $ajax_actions,
						'post_css_meta_key'            => '_wpglobus_elementor_css',
						'post_support_meta_key'        => '_wpglobus_elementor_support',
						// @since 2.4.12
						'elementor_data_meta_key'      => '_elementor_data',
						'elementor_css_meta_key'       => '_elementor_css',
						'elementor_edit_mode_meta_key' => '_elementor_edit_mode',
						// @since 2.4.12
						'elementor_css_print_method'   => get_option( 'elementor_css_print_method', 'external' ),
						// @since 2.2.31
					);

					if ( $load_elementor ) {
						$_attrs['builder_page'] = true;
					} else {
						$_attrs['builder_page'] = false;
					}

					/**
					 * Set $_attrs['builder_support']
					 *
					 * @since 2.4.12
					 */
					$_attrs['builder_support'] = $builder_support;

					return self::get_attrs( $_attrs );
				}
			}

			return false;
		}

		/**
		 * WPBakery Page Builder.
		 * https://wpbakery.com/
		 */
		protected static function is_js_composer() {

			if ( ! defined( 'WPB_VC_VERSION' ) ) {
				return false;
			}

			/**
			 * Global
			 *
			 * @global string $pagenow
			 */
			global $pagenow;

			if ( 'post.php' === $pagenow ) {

				$_builder_page = true;

				/**
				 * Note:
				 *
				 * @see vc_editor_post_types() (js_composer\include\helpers\helpers_api.php) doesn't work here.
				 * so let's check the roles.
				 */
				$_opts = wp_roles()->roles;

				if ( ! function_exists( 'wp_get_current_user' ) ) {
					require_once ABSPATH . WPINC . '/pluggable.php';
				}

				$_user = wp_get_current_user();

				$post_id = WPGlobus_Utils::safe_get( 'post' );

				if ( empty( $post_id ) ) {
					/**
					 * Before update post we can get empty $_GET array.
					 * Let's check $_POST.
					 */
					$post_id = WPGlobus_WP::get_http_post_parameter( 'post_ID' );
				}

				/**
				 * Todo add handling this case.
				 * // if ( empty( $post_id ) ) {
				 * // }
				 * // $_post_type = $wpdb->get_col( $wpdb->prepare( "SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id ) );
				 * //
				 * // $post_type = '';
				 * // if ( ! empty( $_post_type[0] ) ) {
				 * //    $post_type = $_post_type[0];
				 * // }*/

				$post      = get_post( $post_id );
				$post_type = ( $post ? $post->post_type : '' );

				if ( ! isset( $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) ) {
					/**
					 * WPBakery Page Builder is available for pages only (settings were not saved yet).
					 */
					if ( 'page' !== $post_type ) {
						$_builder_page = false;
					}
				} elseif ( empty( $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) ) {
					/**
					 * Settings exist but set to False, so all post types are disabled in WPBakery Page Builder.
					 */
					$_builder_page = false;

				} elseif ( true === $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * WPBakery Page Builder is available for pages only.
					 */
					if ( 'page' !== $post_type ) {
						$_builder_page = false;
					}
				} elseif ( 'custom' === $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) {

					/**
					 * Custom settings for post types in WPBakery Page Builder.
					 */
					if ( ! empty( $_opts[ $_user->roles[0] ]['capabilities'][ 'vc_access_rules_post_types/' . $post_type ] ) ) {
						// Setting for this post type exists and set to True.
						$_builder_page = true;
					} else {
						$_builder_page = false;
					}
				} else {
					$_builder_page = false;
				}

				$_attrs = array(
					'id'           => 'js_composer',
					'version'      => WPB_VC_VERSION,
					'class'        => 'WPGlobus_JS_Composer',
					'post_type'    => $post_type,
					'builder_page' => $_builder_page,
				);

				/**
				 * W.I.P
				 *
				 * @since 2.2.11
				 * //self::$admin_attrs['multilingualFields'][] = 'wpb_visual_composer';
				 */

				return self::get_attrs( $_attrs );

			}

			return false;
		}

		/**
		 * Gutenberg.
		 *
		 * @since 1.9.17
		 */
		protected static function is_gutenberg() {

			$load_gutenberg = false;
			$message        = '';

			/**
			 * Globals
			 *
			 * @global string $pagenow
			 */
			global $pagenow, $wp_version;

			if ( version_compare( $wp_version, '4.9.99', '>' ) ) {

				$context = 'core';

				/**
				 * Page post-new.php
				 *
				 * @since 2.0
				 */
				if ( 'post-new.php' === $pagenow ) {

					/**
					 * Load specific language switcher for this page.
					 *
					 * @see get_switcher_box() in wpglobus\includes\builders\gutenberg\class-wpglobus-gutenberg.php
					 */
					//if ( ! isset( $_GET['classic-editor'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
					// Start Gutenberg support if classic editor was not requested.
					//$load_gutenberg = true;
					//}

					$load_gutenberg = true;

					$load_gutenberg = self::get_3rd_party_status_for_gutenberg( $load_gutenberg );

				} elseif ( 'index.php' === $pagenow ) {

					/**
					 * When Update button was clicked.
					 */
					if ( ! is_admin() ) {
						/**
						 * Gutenberg updates post as from front.
						 *
						 * @see $_SERVER['REQUEST_URI']
						 */
						//$actions = array( 'edit' );
						// @todo check 'wp/v2/' in wp.api.versionString (JS).

						// /wp-json/wp/v2/posts/
						// /wp-json/wp/v2/pages/
						/**
						 * We need define post type for correct work.
						 *
						 * @todo check
						 * /wp-json/wp/v2/taxonomies?context=edit
						 * /wp-json/wp/v2/taxonomies?context=edit&_locale=user
						 * /wp-json/wp/v2/types/wp_block?_locale=user
						 * /wp-json/wp/v2/blocks?per_page=100&_locale=user
						 */

						$_request_uri = explode( '/', WPGlobus_WP::request_uri() );
						$post_id      = end( $_request_uri );
						$post_id      = preg_replace( '/\?.*/', '', $post_id );

						/**
						 * Todo @see https://wpglobus.freshdesk.com/a/tickets/4103
						 */

						/**
						 * Added checking $_request_uri[4].
						 *
						 * @since 2.3.5
						 */
						$_continue = false;
						if ( 0 !== (int) $post_id && ! empty( $_request_uri[4] ) ) {

							$GLOBALS['WPGlobus']['builder'] = 'gutenberg';
							$GLOBALS['WPGlobus']['context'] = $context;
							$GLOBALS['WPGlobus']['post_id'] = $post_id;
							switch ( $_request_uri[4] ) {
								case 'posts':
									$post_type = 'post';
									break;
								case 'pages':
									$post_type = 'page';
									break;
								default:
									$post_type = $_request_uri[4];
							}
							$GLOBALS['WPGlobus']['post_type'] = $post_type;
							$_continue                        = true;
						}

						if ( false !== strpos( WPGlobus_WP::request_uri(), 'wp/v2/posts' )
							 || false !== strpos( WPGlobus_WP::request_uri(), 'wp/v2/pages' )
							 || $_continue ) {
							$load_gutenberg = true;
						}
					}
				} elseif ( 'post.php' === $pagenow ) {

					$load_gutenberg = true;

					$post_type = is_null( self::$post_type ) ? '' : self::$post_type;

					/**
					 * Check out $_POST['post_type'] to define post type.
					 *
					 * @since 2.1.6
					 */
					$_POST_post_type = WPGlobus_WP::get_http_post_parameter( 'post_type' );
					if ( empty( $post_type ) && $_POST_post_type ) {
						$post_type = $_POST_post_type;
					}

					$_GET_post = WPGlobus_WP::get_http_get_parameter( 'post' );
					if ( empty( $post_type ) && $_GET_post ) {
						$post_type = self::get_post_type( $_GET_post );
					}

					/**
					 * Todo don't check post type @since 2.1.2
					 * // if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
					 * //    $load_gutenberg = false;
					 * // }
					 */

					/**
					 * Don't start Block Editor support.
					 *
					 * @since 2.2.24
					 */
					if ( ! self::use_block_editor_for_post_type( $post_type ) ) {
						return false;
					}

					$load_gutenberg = self::get_3rd_party_status_for_gutenberg( $load_gutenberg, $post_type );

				}

				$_attrs = array(
					'id'           => 'gutenberg',
					'version'      => $wp_version,
					'class'        => 'WPGlobus_Gutenberg',
					'builder_page' => false,
					'pagenow'      => $pagenow,
					'post_type'    => empty( $post_type ) ? '' : $post_type,
					'message'      => $message,
					'context'      => $context,
				);

				if ( $load_gutenberg ) {
					$_attrs['builder_page'] = true;
				}

				$attrs = self::get_attrs( $_attrs );

				self::$add_on['gutenberg']['admin_bar_builder_label'] = 'Block Editor';

				return $attrs;

			}

			if ( defined( 'GUTENBERG_VERSION' ) ) {

				$__builder = self::get_addon( 'gutenberg' );

				if ( ! $__builder ) {
					return false;
				}

				if ( version_compare( GUTENBERG_VERSION, $__builder['supported_min_version'], '<' ) ) {

					$message = 'Unsupported Gutenberg version.';

				} else {

					if ( self::is_gutenberg_ajax() ) {

						$load_gutenberg = true;

					} else {

						if ( 'post-new.php' === $pagenow ) {

							/**
							 * Load specific language switcher for this page.
							 *
							 * @see get_switcher_box() in wpglobus\includes\builders\gutenberg\class-wpglobus-gutenberg.php
							 */
							if ( ! WPGlobus_WP::is_parameter_in_http_get( 'classic-editor' ) ) {
								// Start Gutenberg support if classic editor was not requested.
								$load_gutenberg = true;
							}

							/**
							 * Set $load_gutenberg
							 *
							 * @since 1.9.30
							 */
							$load_gutenberg = self::get_3rd_party_status_for_gutenberg( $load_gutenberg );

						} elseif ( 'index.php' === $pagenow ) {

							/**
							 * When Update button was clicked.
							 */
							if ( ! is_admin() ) {
								/**
								 * Gutenberg updates post as from front.
								 *
								 * @see $_SERVER['REQUEST_URI']
								 */
								//$actions = array( 'edit' );
								// @todo check 'wp/v2/' in wp.api.versionString (JS).

								// /wp-json/wp/v2/posts/
								// /wp-json/wp/v2/pages/
								// @todo check /wp-json/wp/v2/taxonomies?context=edit
								if ( false !== strpos( WPGlobus_WP::request_uri(), 'wp/v2/posts' )
									 || false !== strpos( WPGlobus_WP::request_uri(), 'wp/v2/pages' ) ) {
									$load_gutenberg = true;
								}
							}
						} elseif ( 'post.php' === $pagenow ) {

							$load_gutenberg = true;

							$actions      = array( 'edit', 'editpost' );
							$_GET_action  = WPGlobus_WP::get_http_get_parameter( 'action' );
							$_POST_action = WPGlobus_WP::get_http_post_parameter( 'action' );
							if ( $_GET_action ) {
								if ( in_array( $_GET_action, $actions, true ) ) {
									if ( WPGlobus_WP::is_parameter_in_http_get( 'classic-editor' ) ) {
										$load_gutenberg = false;
									}
									if ( 1 === (int) WPGlobus_WP::get_http_get_parameter( 'meta_box' ) ) {
										$load_gutenberg = true;
									}
								}
							} elseif ( $_POST_action ) {
								if ( in_array( $_POST_action, $actions, true ) ) {
									if ( WPGlobus_WP::is_parameter_in_http_post( 'classic-editor' ) ) {
										$load_gutenberg = false;
									}
									if ( 1 === (int) WPGlobus_WP::get_http_get_parameter( 'meta_box' ) ) {
										$load_gutenberg = true;
									}
								}
							}

							$post_type = is_null( self::$post_type ) ? '' : self::$post_type;
							$_GET_post = WPGlobus_WP::get_http_get_parameter( 'post' );
							if ( empty( $post_type ) && $_GET_post ) {
								$post_type = self::get_post_type( $_GET_post );
							}

							/**
							 * Since 1.9.17 Gutenberg support will be start for posts and pages only.
							 */
							if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
								$load_gutenberg = false;
							}

							/**
							 * Set $load_gutenberg
							 *
							 * @since 1.9.30
							 */
							$load_gutenberg = self::get_3rd_party_status_for_gutenberg( $load_gutenberg );
						}
					}
				}

				$_attrs = array(
					'id'           => 'gutenberg',
					'version'      => GUTENBERG_VERSION,
					'class'        => 'WPGlobus_Gutenberg',
					'builder_page' => false,
					'pagenow'      => $pagenow,
					'post_type'    => empty( $post_type ) ? '' : $post_type,
					'message'      => $message,
				);

				if ( $load_gutenberg ) {
					$_attrs['builder_page'] = true;
				}

				return self::get_attrs( $_attrs );

			}

			return $load_gutenberg;
		}

		/**
		 * Method get_3rd_party_status_for_gutenberg
		 *
		 * @since 1.9.30
		 *
		 * @param bool   $load_gutenberg
		 * @param string $post_type @since 2.1.6
		 *
		 * @return bool
		 */
		protected static function get_3rd_party_status_for_gutenberg( $load_gutenberg, $post_type = '' ) {

			if ( '' === $post_type ) {
				$post_type = self::get_post_type_2();
			}

			if ( defined( 'WC_PLUGIN_FILE' ) ) {
				/**
				 * WooCommerce.
				 */
				if ( 'product' === $post_type ) {

					$load_gutenberg = false;

				} elseif ( '' === $post_type ) {

					$_POST_post_type = WPGlobus_WP::get_http_post_parameter( 'post_type' );
					if ( $_POST_post_type ) {
						$post_type = $_POST_post_type;
					}

					$_GET_post = WPGlobus_WP::get_http_get_parameter( 'post' );
					if ( empty( $post_type ) && $_GET_post ) {
						$post_type = self::get_post_type( $_GET_post );
					}

					$_GET_post_type = WPGlobus_WP::get_http_get_parameter( 'post_type' );
					if ( empty( $post_type ) && $_GET_post_type ) {
						$post_type = $_GET_post_type;
					}

					if ( 'product' === $post_type ) {
						$load_gutenberg = false;
					}
				}
			}

			/**
			 * Elementor.
			 *
			 * @see   elementor\modules\gutenberg\module.php
			 * @since 2.4.11
			 */
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				$gutenberg_module = new Gutenberg\Module();
				if ( $gutenberg_module->is_active() ) {
					$_GET_action = WPGlobus_WP::get_http_get_parameter( 'action' );
					if ( 'elementor' === $_GET_action ) {
						/**
						 * Prevent init block editor support when elementor edit page is loading.
						 */
						return false;
					}
				}
			}

			if ( function_exists( 'classic_editor_settings' ) ) {
				/**
				 * See ver.0.5
				 *
				 * @link https://wordpress.org/plugins/classic-editor/#developers
				 */
				if ( WPGlobus_WP::is_parameter_in_http_get( 'classic-editor' ) ) {
					/**
					 * Option 'Use the Block editor by default and include optional links back to the Classic editor' was selected.
					 */
					$load_gutenberg = false;
				} else {
					$classic_editor_replace = get_option( 'classic-editor-replace' );
					if ( empty( $classic_editor_replace ) || 'replace' === $classic_editor_replace ) {
						$load_gutenberg = false;
					}
				}
			}

			if ( class_exists( 'Classic_Editor' ) ) {
				/**
				 * Global
				 *
				 * @global string $wp_version
				 */
				global $wp_version;

				if ( ! version_compare( $wp_version, '4.9.99', '>' ) ) {
					/**
					 * Incorrect work with WP 4.9
					 *
					 * @link https://wordpress.org/support/topic/does-nor-work-anymore-since-v-1-0/
					 */
					return $load_gutenberg;
				}

				/**
				 * Ver.1.0
				 *
				 * @link https://wordpress.org/plugins/classic-editor/
				 */
				if ( WPGlobus_WP::is_parameter_in_http_get( 'classic-editor' ) ) {
					/**
					 * Todo
					 * 1. set 'classic-editor-remember' as 'block-editor'.
					 * 2. load your-site/wp-admin/post.php?post=POST_ID&action=edit&classic-editor.
					 * 3. incorrect loading post page.
					 * //update_post_meta( POST_ID, 'classic-editor-remember', 'classic-editor' );
					 */

					$load_gutenberg = false;
				} elseif ( WPGlobus_WP::is_parameter_in_http_get( 'classic-editor__forget' ) ) {
					$load_gutenberg = true;
				} else {
					$post_id = (int) WPGlobus_WP::get_http_get_parameter( 'post' );

					if ( 0 === $post_id ) {
						/**
						 * We need to check $_POST when the saving post in 'classic-editor' mode.
						 * As option we can use $_POST['classic-editor'], but now get 'classic-editor-remember' meta.
						 */
						$post_id = (int) WPGlobus_WP::get_http_post_parameter( 'post_ID' );
					}

					if ( 0 !== $post_id ) {
						$classic_editor_remember = get_post_meta( $post_id, 'classic-editor-remember', true );
						if ( 'classic-editor' === $classic_editor_remember ) {
							// Do not load Gutenberg.
							return false;
						} elseif ( 'block-editor' === $classic_editor_remember ) {
							// Load Gutenberg.
							return true;
						}
						//else {
						/**
						 * Todo meta doesn't exist?
						 */
						//}
					}

					$classic_editor_replace = get_option( 'classic-editor-replace' );
					if ( empty( $classic_editor_replace ) || 'classic' === $classic_editor_replace ) {
						$load_gutenberg = false;
					} elseif ( 'block' === $classic_editor_replace ) {
						$load_gutenberg = true;
					} else {
						$load_gutenberg = false;

					}
				}
			}

			return $load_gutenberg;
		}

		/**
		 * Check for gutenberg ajax.
		 */
		protected static function is_gutenberg_ajax() {
			$result = false;

			$_POST_action = WPGlobus_WP::get_http_post_parameter( 'action' );
			if ( ! $_POST_action ) {
				return $result;
			}

			$actions = array( 'edit', 'editpost' );
			if ( in_array( $_POST_action, $actions, true ) ) {
				if ( WPGlobus_WP::is_parameter_in_http_post( 'gutenberg_meta_boxes' ) ) {
					$result = true;
				}
			}

			return $result;
		}

		/**
		 * Check for Yoast SEO.
		 *
		 * @since 1.9.17
		 */
		protected static function is_yoast_seo() {

			if ( defined( 'WPSEO_VERSION' ) ) {

				/**
				 * Yoast
				 *
				 * @since 2.8.4
				 */
				$id      = 'yoast_seo';
				$version = WPSEO_VERSION;

				if ( version_compare( WPSEO_VERSION, '17.3', '>=' ) ) {
					// See code in addon-yoast_seo.php

					$id = self::$add_on['yoast_seo']['id'];

					if ( defined( 'WPSEO_PREMIUM_VERSION' ) ) {
						$id      = self::$add_on['yoast_seo_premium']['id'];
						$version = WPSEO_PREMIUM_VERSION;
					}
				}

				$wpseo_titles = get_option( 'wpseo_titles' );

				if ( WPGlobus_WP::is_pagenow( 'post.php' ) ) {

					$post_type = is_null( self::$post_type ) ? '' : self::$post_type;

					$_GET_post = WPGlobus_WP::get_http_get_parameter( 'post' );
					if ( empty( $post_type ) && $_GET_post ) {
						$post_type = self::get_post_type( $_GET_post );
					}

					if ( empty( $post_type ) ) {
						/**
						 * Check $_REQUEST when post is updated.
						 */
						$_REQUEST_post_type = WPGlobus_WP::get_http_request_parameter( 'post_type' );
						if ( $_REQUEST_post_type ) {
							$post_type = $_REQUEST_post_type;
						}
					}

					$_attrs = array(
						'id'           => $id,
						'version'      => $version,
						'class'        => 'WPGlobus_Yoast_SEO',
						'builder_page' => false,
						'post_type'    => empty( $post_type ) ? '' : $post_type,
					);

					if ( empty( $post_type ) ) {
						/**
						 * Detect builder page using $pagenow.
						 *
						 * @since 1.9.17
						 */
						$_attrs['builder_page'] = true;
					} else {

						if ( ! array_key_exists( 'display-metabox-pt-' . $post_type, $wpseo_titles ) ) {
							/**
							 * True
							 *
							 * @since 2.2.25
							 */
							$_attrs['builder_page'] = true;
						} elseif ( 0 === (int) $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) {
							$_attrs['builder_page'] = false;
						} else {
							$_attrs['builder_page'] = true;
						}
					}

					return self::get_attrs( $_attrs );

				} elseif ( WPGlobus_WP::is_pagenow( 'term.php' ) ) {

					$tax = WPGlobus_WP::get_http_get_parameter( 'taxonomy' );
					if ( $tax ) {

						$_attrs = array(
							'id'           => $id,
							'version'      => $version,
							'class'        => 'WPGlobus_Yoast_SEO',
							'builder_page' => false,
							'post_type'    => '',
							'taxonomy'     => $tax,
						);

						self::$admin_attrs = array(
							'multilingualFields' => array( 'name', 'description_ifr' ),
							'translatableClass'  => 'wpglobus-translatable',
						);

						if ( isset( $wpseo_titles[ 'display-metabox-tax-' . $tax ] ) && 0 === (int) $wpseo_titles[ 'display-metabox-tax-' . $tax ] ) {
							$_attrs['builder_page'] = false;
						} else {
							$_attrs['builder_page'] = true;
						}

						return self::get_attrs( $_attrs );
					}
				} elseif ( WPGlobus_WP::is_pagenow( 'edit-tags.php' ) ) {
					/**
					 * Case when Update button was clicked on term.php page .
					 */
					$tax = WPGlobus_WP::get_http_post_parameter( 'taxonomy' );
					if ( $tax ) {

						$_attrs = array(
							'id'           => $id,
							'version'      => $version,
							'class'        => 'WPGlobus_Yoast_SEO',
							'builder_page' => false,
							'post_type'    => '',
							'taxonomy'     => $tax,
						);

						self::$admin_attrs = array(
							'multilingualFields' => array( 'name', 'description_ifr' ),
							'translatableClass'  => 'wpglobus-translatable',
						);

						if ( 'editedtag' === WPGlobus_WP::get_http_post_parameter( 'action' ) ) {
							$_attrs['builder_page'] = true;
						}

						return self::get_attrs( $_attrs );
					}
				}
			}

			return false;
		}

		/**
		 * Check for Pods – Custom Content Types and Fields.
		 *
		 * @since 2.3.0
		 */
		protected static function is_pods() {

			if ( ! defined( 'PODS_VERSION' ) ) {
				return false;
			}

			$post_type = self::get_post_type_2();

			$_attrs = array(
				'id'           => 'pods',
				'version'      => PODS_VERSION,
				'class'        => 'WPGlobus_Pods',
				'builder_page' => false,
				'post_type'    => empty( $post_type ) ? '' : $post_type,
			);

			require_once 'pods/class-wpglobus-builder-pods.php';
			$_attrs = WPGlobus_Builder_Pods::get_attrs( self::get_attrs( $_attrs ) );

			if ( ! $_attrs ) {
				return false;
			}

			return $_attrs;
		}

		/**
		 * Check for Rank Math SEO Plugin.
		 *
		 * @since 2.4.3
		 */
		protected static function is_rank_math_seo() {

			if ( ! defined( 'RANK_MATH_VERSION' ) ) {
				return false;
			}

			$post_type = self::get_post_type_2();

			$_attrs = array(
				'id'           => 'rank_math_seo',
				'version'      => RANK_MATH_VERSION,
				'class'        => 'WPGlobus_RankMathSEO',
				'builder_page' => false,
				'post_type'    => empty( $post_type ) ? '' : $post_type,
				'taxonomy'     => '',
			);

			require_once 'rank_math_seo/class-wpglobus-builder-rank_math_seo.php';
			$_attrs = WPGlobus_Builder_RankMathSEO::get_attrs( self::get_attrs( $_attrs ) );

			if ( ! $_attrs ) {
				return false;
			}

			return $_attrs;
		}

		/**
		 * Get attributes.
		 *
		 * @param array $attrs
		 *
		 * @return array
		 */
		protected static function get_attrs( $attrs ) {
			$_attrs = array_merge( self::$attrs, $attrs );
			if ( ! isset( $_attrs['is_admin'] ) || $_attrs['is_admin'] ) {
				$_attrs = array_merge( $_attrs, self::$admin_attrs );
			}

			if ( empty( $_attrs['post_id'] ) ) {
				$_GET_post        = WPGlobus_WP::get_http_get_parameter( 'post' );
				$_REQUEST_post_ID = WPGlobus_WP::get_http_request_parameter( 'post_ID' );
				if ( is_string( $_GET_post ) ) {
					/**
					 * With bulk action (trash, untrash) we get $_GET['post'] as array.
					 *
					 * @since WPGlobus 2.0 we are working with single post only.
					 */
					$_attrs['post_id'] = $_GET_post;
				} elseif ( is_string( $_REQUEST_post_ID ) ) {
					$_attrs['post_id'] = $_REQUEST_post_ID;
					// } else {
					// @todo Check additional ways to get post ID.
				}
			}

			// @todo maybe disable post type here.
			// $_attrs['builder_page'] = false;
			return $_attrs;
		}

		/**
		 * Get post type.
		 *
		 * @param string $id
		 *
		 * @return null|string
		 */
		protected static function get_post_type( $id = '' ) {

			/**
			 * Get post type.
			 *
			 * @since 2.2.11
			 */
			if ( ! is_null( self::$post_type ) ) {
				return self::$post_type;
			}

			/**
			 * W.I.P to use get_post_type_2()
			 *
			 * @since 2.2.11
			 */
			if ( 0 === (int) $id ) {
				return null;
			}

			/**
			 * WPDB
			 *
			 * @global wpdb $wpdb
			 */
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $id ) );
		}

		/**
		 * Get post type 2.
		 *
		 * @since 2.2.11
		 */
		protected static function get_post_type_2() {

			if ( ! is_null( self::$post_type ) ) {
				return self::$post_type;
			}

			$post_type = '';
			$post_id   = '';

			switch ( WPGlobus_WP::pagenow() ) {
				case 'media-new.php':
					$post_type = 'attachment';
					break;
				case 'post-new.php':
					$_GET_post_type = WPGlobus_WP::get_http_get_parameter( 'post_type' );
					$post_type      = $_GET_post_type ? $_GET_post_type : 'post';
					break;
				/**
				 * W.I.P @since 2.2.14
				 * case 'admin-ajax.php' :
				 * $post_type = '';
				 * break;
				 * // */
				default:
					// post.php page.
					$_GET_post        = WPGlobus_WP::get_http_get_parameter( 'post' );
					$_GET_post_id     = WPGlobus_WP::get_http_get_parameter( 'post_id' );
					$_REQUEST_post_ID = WPGlobus_WP::get_http_request_parameter( 'post_ID' );
					if ( $_GET_post ) {
						$post_id = $_GET_post;
					} elseif ( $_GET_post_id ) {
						/**
						 * For example when loading WPBakery PB's front editor.
						 *
						 * @since 2.3.7
						 */
						$post_id = $_GET_post_id;
					} elseif ( $_REQUEST_post_ID ) {
						/**
						 * Case when Update button was clicked.
						 */
						$post_id = $_REQUEST_post_ID;
					}
					break;
			}

			if ( ! empty( $post_type ) ) {
				self::$post_type = $post_type;

				return self::$post_type;
			}

			if ( 0 === (int) $post_id ) {
				return false;
			}

			/**
			 * WPDB
			 *
			 * @global wpdb $wpdb
			 */
			global $wpdb;

			self::$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $post_id ) );

			return self::$post_type;
		}

		/**
		 * Check for post type supports.
		 *
		 * @since 2.2.24
		 * @since 2.2.34 Fix with empty `show_in_rest`.
		 *
		 * @return bool
		 */
		protected static function use_block_editor_for_post_type( $post_type ) {

			$_opts = get_option( self::$init_attrs['options']['register_post_types'] );

			if ( empty( $_opts[ $post_type ] ) ) {
				/**
				 * We don't have info about post type.
				 */
				return true;
			}

			if ( empty( $_opts[ $post_type ]['features']['editor'] ) || 0 === (int) $_opts[ $post_type ]['features']['editor'] ) {
				/**
				 * Don't start Block Editor support.
				 *
				 * @see `use_block_editor_for_post_type()` in wp-admin\includes\post.php
				 */
				return false;
			}

			if ( empty( $_opts[ $post_type ]['show_in_rest'] ) || 0 === (int) $_opts[ $post_type ]['show_in_rest'] ) {
				/**
				 * Don't start Block Editor support.
				 *
				 * @see `use_block_editor_for_post_type()` in wp-admin\includes\post.php
				 */
				return false;
			}

			return true;
		}
	}

endif;
