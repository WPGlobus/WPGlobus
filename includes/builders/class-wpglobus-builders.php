<?php
/**
 * File: class-wpglobus-builders.php
 *
 * @package WPGlobus\Builders
 * @author  Alex Gor(alexgff)
 */

/**
 * Class WPGlobus_Builders.
 */
if ( ! class_exists( 'WPGlobus_Builders' ) ) :

	class WPGlobus_Builders {

		/**
		 * @var array
		 */
		protected static $attrs = array();

		/**
		 * @var array
		 */
		protected static $admin_attrs = array();

		/**
		 * @var array
		 */
		protected static $add_on = array();

		/**
		 * @var string
		 * @since 2.2.11
		 */		
		protected static $post_type = null;

		/**
		 * @return array
		 */
		public static function get_addons() {

			if ( ! empty( self::$add_on ) ) {
				return self::$add_on;
			}

			global $wp_version;

			self::$add_on['gutenberg'] = array(
				'id'                    => 'gutenberg',
				'role'                  => 'builder',
				'admin_bar_label'       => version_compare( $wp_version, '4.9.99', '>' ) ? 'Core' : 'Builder',
				'supported_min_version' => '4.0.0',
				'const'                 => 'GUTENBERG_VERSION',
				'plugin_name'           => 'Gutenberg',
				'plugin_uri'            => 'https://github.com/WordPress/gutenberg',
				'path'                  => 'gutenberg/gutenberg.php',
				'stage'                 => 'production',
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

			if ( file_exists( WP_PLUGIN_DIR . '/wordpress-seo-premium/wp-seo-premium.php' ) ) {

				self::$add_on['yoast_seo'] = array(
					'id'                    => 'yoast_seo',
					'role'                  => 'builder',
					'admin_bar_label'       => 'Add-on',
					'supported_min_version' => '7.7',
					'const'                 => 'WPSEO_VERSION',
					'plugin_name'           => 'Yoast SEO Premium',
					'plugin_uri'            => 'https://yoast.com/wordpress/plugins/seo/',
					'path'                  => 'wordpress-seo-premium/wp-seo-premium.php',
					'stage'                 => 'production',
				);

			}

			if ( file_exists( WP_PLUGIN_DIR . '/wordpress-seo/wp-seo.php' ) ) {

				if ( ! defined( 'WPSEO_PREMIUM_PLUGIN_FILE' ) ) {

					self::$add_on['yoast_seo'] = array(
						'id'                    => 'yoast_seo',
						'role'                  => 'builder',
						'admin_bar_label'       => 'Add-on',
						'supported_min_version' => '7.7',
						'const'                 => 'WPSEO_VERSION',
						'plugin_name'           => 'Yoast SEO',
						'plugin_uri'            => 'https://wordpress.org/plugins/wordpress-seo/',
						'path'                  => 'wordpress-seo/wp-seo.php',
						'stage'                 => 'production',
					);

				}
			}

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
		 * @param bool  $init
		 * @param array $post_types was added @since 2.2.11 
		 *
		 * @return array|bool
		 */
		public static function get( $init = true, $post_types = array() ) {
				
			// if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			//return false;
			// }

			/** @global string $pagenow */
			global $pagenow;

			self::$attrs = array(
				'id'           => false,
				'context'      => 'add-on',
				'version'      => '',
				'class'        => '',
				'post_type'    => '',
				'post_id'      => '',
				'is_admin'     => true,
				'pagenow'      => $pagenow,
				'builder_page' => false,
				'doing_ajax'   => WPGlobus_WP::is_doing_ajax(),
				'language'     => '',
				'message'      => '',
				'ajax_actions' => '',
			);

			self::$admin_attrs = array(
				'multilingualFields' => array( 'post_title', 'excerpt' ),
				'translatableClass'  => 'wpglobus-translatable',
			);
			 
			/**
			 * @since 2.2.11
			 */
			if ( in_array( $pagenow, array('post.php', 'post-new.php', 'media-new.php') ) ) {
				
				/**
				 * @W.I.P @since 2.2.14
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
				} else if ( ! $post_types[$post_type] ) {
					return self::$attrs;
				}
			}

			if ( $init ) {

				//$builder = false;

				self::get_addons();

				/**
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
							/** @noinspection PhpIncludeInspection */
							include_once WPGlobus::$PLUGIN_DIR_PATH . 'includes/builders/elementor/class-wpglobus-elementor-front.php';
							WPGlobus_Elementor_Front::init( $builder );
						}
					}
				}

				/**
				 * @since 1.9.17
				 * @W.I.P
				 */
				/*
				$builder = self::is_siteorigin_panels();
				if ( $builder ) {
					return $builder;
				}
				// */

				/**
				 * @since 1.9.17
				 */
				$builder = self::is_yoast_seo();
				if ( $builder ) {
					return $builder;
				}
			}

			return self::$attrs;

		}

		/**
		 * Page Builder by SiteOrigin.
		 * @W.I.P
		 * https://wordpress.org/plugins/siteorigin-panels/
		 */
		protected static function is_siteorigin_panels() {}

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

			/** @global string $pagenow */
			global $pagenow;

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

				$attrs = self::get_attrs( $_attrs );

				return $attrs;

			} else {

				if ( in_array( $pagenow, array( 'admin-ajax.php', 'post.php', 'index.php', 'post-new.php' ), true ) ) {

					/**
					 * Init current post type.
					 */
					$post_type = is_null(self::$post_type) ? '' : self::$post_type;

					/**
					 * Init post ID.
					 */
					$post_id = '';

					$ajax_actions = '';
					$is_admin     = true;

					if ( 'admin-ajax.php' === $pagenow ) {

						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( ! isset( $_REQUEST['action'] ) || 'elementor_ajax' !== $_REQUEST['action'] ) {
							return false;
						}
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( false !== strpos( $_REQUEST['actions'], 'save_builder' ) ) {
							$ajax_actions = 'save_builder';
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						} elseif ( false !== strpos( $_REQUEST['actions'], '"action":"render_widget"' ) ) {
							$ajax_actions = 'render_widget';
						} else {
							return false;
						}
						$load_elementor = true;
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$post_id = sanitize_text_field( $_REQUEST['editor_post_id'] );

					} elseif ( 'index.php' === $pagenow ) {

						/**
						 * @todo remove after testing.
						 * if ( ! isset( $_GET['elementor-preview'] ) ) {
						 * return false;
						 * }
						 * // */
						// @W.I.P @since 2.2.11
						// [REQUEST_URI] => /?p=75&elementor-preview=75&ver=1561202861

						$load_elementor = false;
						$is_admin       = false;
						
						/**
						 * @todo Preview page for draft status.
						 * [REQUEST_URI] => /?p=75&elementor-preview=75&ver=1561202861
						 * @W.I.P
						 * @since 2.2.11 [REQUEST_URI] was changed to `?p=75&preview_id=75&preview_nonce=da660129a7&preview=true`.
						 */
						/* 
						if ( ! empty( $_GET['p'] ) ) {
							$load_elementor = true;
							$is_admin       = true;
							$post_id		= sanitize_text_field( $_GET['p'] );
							$post_type 		= self::get_post_type($post_id);
							
						}
						// */

					} elseif ( 'post.php' === $pagenow ) {

						$is_admin = true;
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) {
							//$is_admin = false;
							$load_elementor = true;
						}

						/**
						 * $cpt_support = get_option( 'elementor_cpt_support', array('page', 'post') );
						 *
						 * @see_file elementor\includes\plugin.php
						 */
						$cpt_support = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );

						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						if ( empty( $post_type ) && isset( $_GET['post_type'] ) ) {
							/**
							 * For post-new.php page.
							 */
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended
							$post_type = sanitize_text_field( $_GET['post_type'] );
						}

						if ( empty( $post_type ) ) {
							// phpcs:ignore WordPress.CSRF.NonceVerification
							if ( isset( $_GET['post'] ) ) {
								$post_type = self::get_post_type( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
							} elseif ( isset( $_REQUEST['post_ID'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
								$post_type = self::get_post_type( $_REQUEST['post_ID'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
							}
						}

						// if ( empty( $post_type ) ) {
						/**
						 * Post type by default.
						 * If we can not define post type then we don't set it to default value.
						 * Because it may cause incorrect behavior later.
						 */
						//$post_type = 'post';
						// }

						if ( in_array( $post_type, $cpt_support, true ) ) {
							$load_elementor = true;
						}
					} else {
						/**
						 * @todo may be use @see is_built_with_elementor() in elementor\core\base\document.php
						 */
						$load_elementor = true;
					}

					$_attrs = array(
						'id'                      => 'elementor',
						'version'                 => ELEMENTOR_VERSION,
						'is_admin'                => $is_admin,
						'class'                   => 'WPGlobus_Elementor',
						'post_type'               => $post_type,
						'post_id'                 => $post_id,
						'builder_page'            => false,
						'ajax_actions'            => $ajax_actions,
						'post_css_meta_key'       => '_wpglobus_elementor_css',
						'elementor_data_meta_key' => '_elementor_data',
						'elementor_css_meta_key'  => '_elementor_css',
					);

					if ( $load_elementor ) {
						$_attrs['builder_page'] = true;
					} else {
						$_attrs['builder_page'] = false;
					}

					$attrs = self::get_attrs( $_attrs );

					return $attrs;

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

			/** @global string $pagenow */
			global $pagenow;

			/** @global wpdb $wpdb */
			// global $wpdb;

			if ( 'post.php' === $pagenow ) {

				$_builder_page = true;

				/**
				 * @see vc_editor_post_types() (js_composer\include\helpers\helpers_api.php) doesn't work here.
				 * so let's check the roles.
				 */
				$_opts = wp_roles()->roles;

				if ( ! function_exists( 'wp_get_current_user' ) ) {
					/** @noinspection PhpIncludeInspection */
					require_once ABSPATH . WPINC . '/pluggable.php';
				}

				$_user = wp_get_current_user();

				$post_id = WPGlobus_Utils::safe_get( 'post' );

				if ( empty( $post_id ) ) {
					/**
					 * Before update post we can get empty $_GET array.
					 * Let's check $_POST.
					 */
					// phpcs:ignore WordPress.Security.NonceVerification.Missing
					$post_id = isset( $_POST['post_ID'] ) ? sanitize_text_field( $_POST['post_ID'] ) : '';
				}

				// if ( empty( $post_id ) ) {
				// @todo add handling this case.
				// }

				// $_post_type = $wpdb->get_col( $wpdb->prepare( "SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id ) );
				//
				// $post_type = '';
				// if ( ! empty( $_post_type[0] ) ) {
				// 	$post_type = $_post_type[0];
				// }

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
				 * @since 2.2.11
				 * @W.I.P
				 */
				//self::$admin_attrs['multilingualFields'][] = 'wpb_visual_composer';

				$attrs = self::get_attrs( $_attrs );

				return $attrs;

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

			/** @global string $pagenow */
			global $pagenow, $wp_version;

			if ( version_compare( $wp_version, '4.9.99', '>' ) ) {

				$context = 'core';

				/**
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

						$_request_uri = explode( '/', $_SERVER['REQUEST_URI'] );
						$post_id      = end( $_request_uri );
						$post_id      = preg_replace( '/\?.*/', '', $post_id );

						/**
						 * @todo @see https://wpglobus.freshdesk.com/a/tickets/4103
						 */

						$_continue = false;
						if ( 0 !== (int) $post_id ) {

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

						if ( false !== strpos( $_SERVER['REQUEST_URI'], 'wp/v2/posts' )
							 || false !== strpos( $_SERVER['REQUEST_URI'], 'wp/v2/pages' )
							 || $_continue ) {
							$load_gutenberg = true;
						}
					}
				} elseif ( 'post.php' === $pagenow ) {

					$load_gutenberg = true;

					$post_type = is_null(self::$post_type) ? '' : self::$post_type;
					
					/**
					 * Check out $_POST['post_type'] to define post type.
					 *
					 * @since 2.1.6
					 */
					if ( empty( $post_type ) && ! empty( $_POST['post_type'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = $_POST['post_type']; // phpcs:ignore WordPress.CSRF.NonceVerification					
					}

					if ( empty( $post_type ) && ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = self::get_post_type( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
					}

					/**
					 * @todo don't check post type @since 2.1.2
					 */
					// if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
					//	$load_gutenberg = false;
					// }

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
							if ( ! isset( $_GET['classic-editor'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
								// Start Gutenberg support if classic editor was not requested.
								$load_gutenberg = true;
							}

							/**
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
								if ( false !== strpos( $_SERVER['REQUEST_URI'], 'wp/v2/posts' )
									 || false !== strpos( $_SERVER['REQUEST_URI'], 'wp/v2/pages' ) ) {
									$load_gutenberg = true;
								}
							}
						} elseif ( 'post.php' === $pagenow ) {

							$load_gutenberg = true;

							$actions = array( 'edit', 'editpost' );
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended
							if ( ! empty( $_GET['action'] ) ) {
								// phpcs:ignore WordPress.Security.NonceVerification.Recommended
								if ( in_array( $_GET['action'], $actions, true ) ) {
									// phpcs:ignore WordPress.Security.NonceVerification.Recommended
									if ( array_key_exists( 'classic-editor', $_GET ) ) {
										$load_gutenberg = false;
									}
									// phpcs:ignore WordPress.Security.NonceVerification.Recommended
									if ( isset( $_GET['meta_box'] ) && 1 === (int) $_GET['meta_box'] ) {
										$load_gutenberg = true;
									}
								}
							} elseif ( ! empty( $_POST['action'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
								// phpcs:ignore WordPress.Security.NonceVerification.Missing
								if ( in_array( $_POST['action'], $actions, true ) ) {
									// phpcs:ignore WordPress.Security.NonceVerification.Missing
									if ( array_key_exists( 'classic-editor', $_POST ) ) {
										$load_gutenberg = false;
									}
									// phpcs:ignore WordPress.Security.NonceVerification.Missing
									if ( isset( $_POST['meta_box'] ) && 1 === (int) $_POST['meta_box'] ) {
										$load_gutenberg = true;
									}
								}
							}
							
							$post_type = is_null(self::$post_type) ? '' : self::$post_type;
							
							if ( empty($post_type) && ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
								$post_type = self::get_post_type( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
							}

							/**
							 * Since 1.9.17 Gutenberg support will be start for posts and pages only.
							 */
							if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
								$load_gutenberg = false;
							}

							/**
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

				$attrs = self::get_attrs( $_attrs );

				return $attrs;

			}

			return $load_gutenberg;
		}

		/**
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

					if ( ! empty( $_POST['post_type'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = $_POST['post_type']; // phpcs:ignore WordPress.CSRF.NonceVerification
					}

					if ( empty( $post_type ) && ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = self::get_post_type( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
					}

					if ( empty( $post_type ) && ! empty( $_GET['post_type'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = $_GET['post_type']; // phpcs:ignore WordPress.CSRF.NonceVerification					
					}

					if ( 'product' === $post_type ) {
						$load_gutenberg = false;
					}
				}
			}

			if ( function_exists( 'classic_editor_settings' ) ) {
				/**
				 * @see ver.0.5 https://wordpress.org/plugins/classic-editor/#developers
				 */
				if ( isset( $_GET['classic-editor'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
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
				/** @global string $wp_version */
				global $wp_version;

				if ( version_compare( $wp_version, '4.9.99', '>' ) ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement
					// continue
				} else {
					/**
					 * Incorrect work with WP 4.9
					 *
					 * @see https://wordpress.org/support/topic/does-nor-work-anymore-since-v-1-0/
					 */
					return $load_gutenberg;
				}

				/**
				 * ver.1.0 https://wordpress.org/plugins/classic-editor/
				 */
				if ( isset( $_GET['classic-editor'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
					/**
					 * @todo
					 * 1. set 'classic-editor-remember' as 'block-editor'.
					 * 2. load your-site/wp-admin/post.php?post=POST_ID&action=edit&classic-editor.
					 * 3. incorrect loading post page.
					 */
					//update_post_meta( POST_ID, 'classic-editor-remember', 'classic-editor' );

					$load_gutenberg = false;
				} elseif ( isset( $_GET['classic-editor__forget'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
					$load_gutenberg = true;
				} else {
					$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.CSRF.NonceVerification

					if ( 0 === $post_id ) {
						/**
						 * We need to check $_POST when the saving post in 'classic-editor' mode.
						 * As option we can use $_POST['classic-editor'], but now get 'classic-editor-remember' meta.
						 */
						$post_id = isset( $_POST['post_ID'] ) ? (int) $_POST['post_ID'] : 0; // phpcs:ignore WordPress.CSRF.NonceVerification
					}

					if ( 0 !== $post_id ) {
						$classic_editor_remember = get_post_meta( $post_id, 'classic-editor-remember', true );
						if ( 'classic-editor' === $classic_editor_remember ) {
							$load_gutenberg = false;

							return $load_gutenberg;
						} elseif ( 'block-editor' === $classic_editor_remember ) {
							$load_gutenberg = true;

							return $load_gutenberg;
						}
						//else {
						/**
						 * @todo meta doesn't exist?
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

			// phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( empty( $_POST ) || empty( $_POST['action'] ) ) {
				return $result;
			}

			$actions = array( 'edit', 'editpost' );
			if ( in_array( $_POST['action'], $actions, true ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
				if ( array_key_exists( 'gutenberg_meta_boxes', $_POST ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
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

				/** @global string $pagenow */
				global $pagenow;

				$wpseo_titles = get_option( 'wpseo_titles' );

				if ( 'post.php' === $pagenow ) {
					
					$post_type = is_null(self::$post_type) ? '' : self::$post_type;
					
					if ( empty( $post_type ) && ! empty( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_type = self::get_post_type( $_GET['post'] ); // phpcs:ignore WordPress.CSRF.NonceVerification
					}

					if ( empty( $post_type ) ) {
						/**
						 * Check $_REQUEST when post is updated.
						 */
						if ( ! empty( $_REQUEST['post_type'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
							$post_type = $_REQUEST['post_type']; // phpcs:ignore WordPress.CSRF.NonceVerification
						}
					}

					$_attrs = array(
						'id'           => 'yoast_seo',
						'version'      => WPSEO_VERSION,
						'class'        => 'WPGlobus_Yoast_SEO',
						'builder_page' => false,
						'post_type'    => empty( $post_type ) ? '' : $post_type,
					);

					if ( empty( $post_type ) ) {
						/**
						 * @since 1.9.17 detect builder page using $pagenow.
						 */
						$_attrs['builder_page'] = true;
					} else {

						if ( empty( $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) ) {
							/**
							 * @since 2.2.8
							 */
							$_attrs['builder_page'] = false;
						} elseif ( isset( $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) && 0 === (int) $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) {
							$_attrs['builder_page'] = false;
						} else {
							$_attrs['builder_page'] = true;
						}
						
					}

					$attrs = self::get_attrs( $_attrs );

					return $attrs;

				} elseif ( 'term.php' === $pagenow ) {

					$tax = empty( $_GET['taxonomy'] ) ? false : sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ); // phpcs:ignore WordPress.CSRF.NonceVerification

					if ( $tax ) {

						$_attrs = array(
							'id'           => 'yoast_seo',
							'version'      => WPSEO_VERSION,
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

						$attrs = self::get_attrs( $_attrs );

						return $attrs;
					}
				} elseif ( 'edit-tags.php' === $pagenow ) {
					/**
					 * Case when Update button was clicked on term.php page .
					 */
					// phpcs:ignore WordPress.Security.NonceVerification.Missing
					$tax = empty( $_POST['taxonomy'] ) ? false : sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );

					if ( $tax ) {

						$_attrs = array(
							'id'           => 'yoast_seo',
							'version'      => WPSEO_VERSION,
							'class'        => 'WPGlobus_Yoast_SEO',
							'builder_page' => false,
							'post_type'    => '',
							'taxonomy'     => $tax,
						);

						self::$admin_attrs = array(
							'multilingualFields' => array( 'name', 'description_ifr' ),
							'translatableClass'  => 'wpglobus-translatable',
						);

						if ( isset( $_POST['action'] ) && 'editedtag' === $_POST['action'] ) { // phpcs:ignore WordPress.CSRF.NonceVerification
							$_attrs['builder_page'] = true;
						}

						$attrs = self::get_attrs( $_attrs );

						return $attrs;
					}
				}
			}

			return false;

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
			// phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
			if ( isset( $_attrs['is_admin'] ) && ! $_attrs['is_admin'] ) {
				// do nothing.
			} else {
				$_attrs = array_merge( $_attrs, self::$admin_attrs );
			}

			if ( empty( $_attrs['post_id'] ) ) {
				if ( isset( $_GET['post'] ) && is_string( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
					/**
					 * With bulk action (trash, untrash) we get $_GET['post'] as array.
					 *
					 * @since WPGlobus 2.0 we are working with single post only.
					 */
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$_attrs['post_id'] = sanitize_text_field( $_GET['post'] );
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
				} elseif ( isset( $_REQUEST['post_ID'] ) && is_string( $_REQUEST['post_ID'] ) ) {
					// phpcs:ignore WordPress.Security.NonceVerification.Recommended
					$_attrs['post_id'] = sanitize_text_field( $_REQUEST['post_ID'] );
					// } else {
					// @todo Check additional ways to get post ID.
				}
			}

			// @todo may be disable post type here.
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
			 * @since 2.2.11
			 * @W.I.P to use get_post_type_2()
			 */
			if ( 0 === (int) $id ) {
				return null;
			}

			/** @global wpdb $wpdb */
			global $wpdb;

			$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $id ) );

			return $post_type;
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
			
			/** @global string $pagenow */
			global $pagenow;			
			
			$post_type = '';
			$post_id   = '';

			switch ($pagenow) {
				case 'media-new.php' :
					$post_type = 'attachment';
					break;
				case 'post-new.php':
					if ( isset( $_GET['post_type'] ) ) {
						// phpcs:ignore WordPress.Security.NonceVerification.Recommended
						$post_type = sanitize_text_field( $_GET['post_type'] );
					} else {
						$post_type = 'post';
					}
					break;
				/**	
				 * @W.I.P @since 2.2.14
				case 'admin-ajax.php' :
					$post_type = '';
					break;	
				// */		
				default:
					// post.php page.
					if ( isset( $_GET['post'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						$post_id = $_GET['post']; // phpcs:ignore WordPress.CSRF.NonceVerification
					} else if ( isset( $_REQUEST['post_ID'] ) ) { // phpcs:ignore WordPress.CSRF.NonceVerification
						/**
						 * Case when Update button was clicked.
						 */
						$post_id = $_REQUEST['post_ID']; // phpcs:ignore WordPress.CSRF.NonceVerification
					}
					break;
			}

			if ( ! empty($post_type) ) {
				self::$post_type = $post_type;
				return self::$post_type;
			}
			
			if ( 0 === (int) $post_id ) {
				return false;
			}
			
			/** @global wpdb $wpdb */
			global $wpdb;

			self::$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $post_id ) );

			return self::$post_type;			
		}

	}

endif;
