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

		protected static $attrs = array();

		protected static $admin_attrs = array();
		
		protected static $add_on = array();
		
		public static function get_addons() {
			
			self::$add_on['gutenberg'] = array(
				'id' => 'gutenberg',
				'supported_min_version' => '3.9.0',
				'const' => 'GUTENBERG_VERSION',
				'plugin_name' => 'Gutenberg',
				'plugin_uri' => 'https://github.com/WordPress/gutenberg'
			);

			self::$add_on['js_composer'] = array(
				'id' => 'js_composer',
				'supported_min_version' => '4.0.0',
				'const' => 'WPB_VC_VERSION',
				'plugin_name' => 'WPBakery Page Builder',
				'plugin_uri' => 'https://wpbakery.com/'
			);
			
			return self::$add_on;
		}
		
		public static function get_addon( $builder = false ) {
			if ( ! $builder ) {
				return false;
			}
			if ( isset( self::$add_on[$builder] ) ) {
				return self::$add_on[$builder];
			}
			return false;
		}
		
		public static function get( $init = true ) {

			// if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
				//return false;
			// }

			/** @global string $pagenow */
			global $pagenow;

			self::$attrs = array(
				'id'           => false,
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

			if ( $init ) {

				//$builder = false;
				
				self::get_addons();

				/**
				 * @since 1.9.17
				 */
				$builder = self::is_gutenberg();
				if ( $builder ) {
					if ( $builder['builder_page'] ) {
						return $builder;
					}
				}

				/**
				 * @since 1.9.17
				 */
				$builder = self::is_js_composer();
				if ( $builder ) {
					return $builder;
				}

				/**
				 * @since 1.9.17
				 */
				$builder = self::is_elementor();
				if ( $builder ) {
					return $builder;
				}

				/**
				 * @since 1.9.17
				 * @todo  WIP
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
		 * https://wordpress.org/plugins/siteorigin-panels/
		 */
		protected static function is_siteorigin_panels() {

			if ( ! defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
				return false;
			}

			/** @global string $pagenow */
			global $pagenow;

			if ( version_compare( SITEORIGIN_PANELS_VERSION, '2.8.1', '<=' ) ) {

				$message = 'Unsupported Page Builder by SiteOrigin version ' . SITEORIGIN_PANELS_VERSION . '.';

				$_attrs = array(
					'id'           => 'siteorigin_panels',
					'version'      => SITEORIGIN_PANELS_VERSION,
					'class'        => 'WPGlobus_Siteorigin_Panels',
					'is_admin'     => false,
					'builder_page' => false,
					'message'      => $message,
				);

				$attrs = self::get_attrs( $_attrs );

				return $attrs;

			} else {

				/**
				 * Init current post type.
				 */
				$post_type = '';

				//$ajax_actions = '';
				//$is_admin     = false;
				//$load_builder = false;

				if ( is_admin() ) {

					$is_admin     = true;
					$load_builder = false;

					if ( 'post.php' === $pagenow ) {

						$opts = get_option( 'siteorigin_panels_settings' );

						$cpt_support = array( 'page', 'post' );
						if ( ! empty( $opts['post-types'] ) ) {
							$cpt_support = $opts['post-types'];
						}

						if ( isset( $_GET['post'] ) ) {
							$post_type = self::get_post_type( $_GET['post'] ); // WPCS: input var ok, sanitization ok.
						} elseif ( isset( $_REQUEST['post_ID'] ) ) {
							/**
							 * Case when Update button was clicked.
							 */
							$post_type = self::get_post_type( $_REQUEST['post_ID'] ); // WPCS: input var ok, sanitization ok.
						}

						if ( in_array( $post_type, $cpt_support, true ) ) {
							$load_builder = true;
						}

						$_attrs = array(
							'id'       => 'siteorigin_panels',
							'version'  => SITEORIGIN_PANELS_VERSION,
							'class'    => 'WPGlobus_Siteorigin_Panels',
							'is_admin' => $is_admin,
						);

						if ( $load_builder ) {
							$_attrs['builder_page'] = true;
						} else {
							$_attrs['builder_page'] = false;
						}
						$attrs = self::get_attrs( $_attrs );

						return $attrs;

					}
				} else {

					$_attrs = array(
						'id'           => 'siteorigin_panels',
						'version'      => SITEORIGIN_PANELS_VERSION,
						'class'        => 'WPGlobus_Siteorigin_Panels',
						'is_admin'     => false,
						'builder_page' => true,
					);

					return self::get_attrs( $_attrs );

				}
			}

			return false;

		}

		/**
		 * Elementor Page Builder.
		 * https://wordpress.org/plugins/elementor/
		 */
		protected static function is_elementor() {

			if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
				return false;
			}

			/** @global string $pagenow */
			global $pagenow;

			$load_elementor = false;

			if ( version_compare( ELEMENTOR_VERSION, '2.2.0', '<=' ) ) {

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
					$post_type = '';

					/**
					 * Init post ID.
					 */
					$post_id = '';

					$ajax_actions = '';
					$is_admin     = true;

					if ( 'admin-ajax.php' === $pagenow ) {

						if ( ! isset( $_REQUEST['action'] ) || 'elementor_ajax' !== $_REQUEST['action'] ) {
							return false;
						}
						if ( false !== strpos( $_REQUEST['actions'], 'save_builder' ) ) {
							$ajax_actions = 'save_builder';
						} elseif ( false !== strpos( $_REQUEST['actions'], '"action":"render_widget"' ) ) {
							$ajax_actions = 'render_widget';
						} else {
							return false;
						}
						$load_elementor = true;
						$post_id        = sanitize_text_field( $_REQUEST['editor_post_id'] );

					} elseif ( 'index.php' === $pagenow ) {

						/**
						 * @todo remove after testing.
						 * if ( ! isset( $_GET['elementor-preview'] ) ) {
						 * return false;
						 * }
						 * // */

						$load_elementor = false;
						$is_admin       = false;

					} elseif ( 'post.php' === $pagenow ) {

						$is_admin = true;
						if ( isset( $_GET['action'] ) && 'elementor' === $_GET['action'] ) { // WPCS: input var ok, sanitization ok.
							//$is_admin = false;
							$load_elementor = true;
						}

						/**
						 * $cpt_support = get_option( 'elementor_cpt_support', array('page', 'post') );
						 *
						 * @see_file elementor\includes\plugin.php
						 */
						$cpt_support = get_option( 'elementor_cpt_support', array( 'page', 'post' ) );

						if ( isset( $_GET['post_type'] ) ) {
							/**
							 * For post-new.php page.
							 */
							$post_type = sanitize_text_field( $_GET['post_type'] );
						}

						if ( empty( $post_type ) ) {
							if ( isset( $_GET['post'] ) ) {
								$post_type = self::get_post_type( $_GET['post'] ); // WPCS: input var ok, sanitization ok.
							} elseif ( isset( $_REQUEST['post_ID'] ) ) {
								$post_type = self::get_post_type( $_REQUEST['post_ID'] ); // WPCS: input var ok, sanitization ok.
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
						'id'           => 'elementor',
						'version'      => ELEMENTOR_VERSION,
						'is_admin'     => $is_admin,
						'class'        => 'WPGlobus_Elementor',
						'post_type'    => $post_type,
						'post_id'      => $post_id,
						'builder_page' => false,
						'ajax_actions' => $ajax_actions,
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
			global $wpdb;

			if ( 'post.php' === $pagenow ) {

				$_builder_page = true;

				/**
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
					$post_id = isset( $_POST['post_ID'] ) ? sanitize_text_field( $_POST['post_ID'] ) : '';
				}

				// if ( empty( $post_id ) ) {
					// @todo add handling this case.
				// }

				$_post_type = $wpdb->get_col( $wpdb->prepare( "SELECT post_type FROM {$wpdb->prefix}posts WHERE ID = %d", $post_id ) );

				$post_type = '';
				if ( ! empty( $_post_type[0] ) ) {
					$post_type = $_post_type[0];
				}

				if ( ! isset( $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) ) {
					/**
					 * WPBakery Page Builder is available for pages only (settings was not saved yet).
					 */
					if ( 'page' !== $post_type ) {
						$_builder_page = false;
					}
					// TODO compare booleans or check for empty and not ''==??.
				} elseif ( '' == $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) {
					/**
					 * All post types are disabled in WPBakery Page Builder.
					 */
					$_builder_page = false;

					// TODO compare booleans and not '1'==true.
				} elseif ( '1' == $_opts[ $_user->roles[0] ]['capabilities']['vc_access_rules_post_types'] ) {
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
					if ( ! empty( $_opts[ $_user->roles[0] ]['capabilities'][ 'vc_access_rules_post_types/' . $post_type ] )
						 && '1' == $_opts[ $_user->roles[0] ]['capabilities'][ 'vc_access_rules_post_types/' . $post_type ] ) {

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
			global $pagenow;

			if ( defined( 'GUTENBERG_VERSION' ) ) {

				$__builder = self::get_addon('gutenberg');

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
							$load_gutenberg = true;

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
								if ( false !== strpos( $_SERVER['REQUEST_URI'], 'wp/v2/posts' ) ) {
									$load_gutenberg = true;
								}
							}
						} elseif ( 'post.php' === $pagenow ) {

							$load_gutenberg = true;

							$actions = array( 'edit', 'editpost' );
							if ( ! empty( $_GET['action'] ) ) {
								if ( in_array( $_GET['action'], $actions, true ) ) {
									if ( array_key_exists( 'classic-editor', $_GET ) ) {
										$load_gutenberg = false;
									}
									if ( isset( $_GET['meta_box'] ) && 1 === (int) $_GET['meta_box'] ) {
										$load_gutenberg = true;
									}
								}
							} elseif ( ! empty( $_POST['action'] ) ) {
								if ( in_array( $_POST['action'], $actions, true ) ) {
									if ( array_key_exists( 'classic-editor', $_POST ) ) {
										$load_gutenberg = false;
									}
									if ( isset( $_POST['meta_box'] ) && 1 === (int) $_POST['meta_box'] ) {
										$load_gutenberg = true;
									}
								}
							}

							$post_type = '';
							if ( ! empty( $_GET['post'] ) ) {
								$post_type = self::get_post_type( $_GET['post'] );
							}

							/**
							 * Since 1.9.17 Gutenberg support will be start for posts and pages only.
							 */
							if ( ! in_array( $post_type, array( 'post', 'page' ), true ) ) {
								$load_gutenberg = false;
							}
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
		 * Check for gutenberg ajax.
		 */
		protected static function is_gutenberg_ajax() {
			$result = false;

			if ( empty( $_POST ) || empty( $_POST['action'] ) ) {
				return $result;
			}

			$actions = array( 'edit', 'editpost' );
			if ( in_array( $_POST['action'], $actions, true ) ) {
				if ( array_key_exists( 'gutenberg_meta_boxes', $_POST ) ) {
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

				if ( 'post.php' === $pagenow ) {

					$wpseo_titles = get_option( 'wpseo_titles' );

					$post_type = '';
					if ( ! empty( $_GET['post'] ) ) {
						$post_type = self::get_post_type( $_GET['post'] );
					}

					if ( empty( $post_type ) ) {
						/**
						 * Check $_REQUEST when post is updated.
						 */
						if ( ! empty( $_REQUEST['post_type'] ) ) {
							$post_type = $_REQUEST['post_type'];
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

						if ( isset( $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) && 0 === (int) $wpseo_titles[ 'display-metabox-pt-' . $post_type ] ) {
							$_attrs['builder_page'] = false;
						} else {
							$_attrs['builder_page'] = true;
						}
					}

					$attrs = self::get_attrs( $_attrs );

					return $attrs;

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
			if ( isset( $_attrs['is_admin'] ) && ! $_attrs['is_admin'] ) {
				// do nothing.
			} else {
				$_attrs = array_merge( $_attrs, self::$admin_attrs );
			}

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
			if ( 0 === (int) $id ) {
				return null;
			}

			/** @global wpdb $wpdb */
			global $wpdb;

			$post_type = $wpdb->get_var( $wpdb->prepare( "SELECT post_type FROM $wpdb->posts WHERE ID = %d", $id ) );

			return $post_type;
		}

	}

endif;
