<?php
/**
 * Class WPGlobus_Config_Vendor
 *
 * @package WPGlobus
 * @author  Alex Gor(alexgff)
 */

if ( ! class_exists( 'WPGlobus_Config_Vendor' ) ) :

	/**
	 * Vendor configuration.
	 */
	class WPGlobus_Config_Vendor {

		const PLUGIN_CONFIG_FILES = 'configs/*.json';

		const PLUGIN_CONFIG_DIR = 'configs/';

		/**
		 * Instance of this class.
		 *
		 * @var WPGlobus_Config_Vendor
		 */
		protected static $instance;

		/**
		 * @var array
		 */
		protected static $config = array();

		/**
		 * @var array|null
		 */
		protected static $post_meta_fields = null;

		/**
		 * @var array|null
		 */
		protected static $post_ml_fields = null;

		/**
		 * @var array|null
		 */
		protected static $wp_options = null;

		/**
		 * Builder.
		 *
		 * @var WPGlobus_Config_Builder
		 */
		protected static $builder = null;

		/**
		 * Array of registered vendors.
		 *
		 * @var string[]
		 */
		protected static $vendors = array();

		/**
		 * Constructor.
		 *
		 * @param WPGlobus_Config_Builder $builder
		 */
		protected function __construct( $builder ) {

			self::$builder = $builder;

			self::get_config_files();
			self::parse_config();

		}

		/**
		 * Get instance of this class.
		 *
		 * @param WPGlobus_Config_Builder $builder
		 *
		 * @return WPGlobus_Config_Vendor
		 */
		public static function get_instance( $builder ) {
			if ( ! ( self::$instance instanceof WPGlobus_Config_Vendor ) ) {
				self::$instance = new self( $builder );
			}

			return self::$instance;
		}

		/**
		 * Get meta fields.
		 *
		 * @return array|false
		 */
		public static function get_meta_fields() {
			if ( is_null( self::$post_meta_fields ) ) {
				return false;
			}

			return self::$post_meta_fields;
		}

		/**
		 * Get multilingual fields.
		 *
		 * @return array|false
		 */
		public static function get_ml_fields() {
			if ( is_null( self::$post_ml_fields ) ) {
				return false;
			}

			return self::$post_ml_fields;
		}

		/**
		 * Get wp_options.
		 *
		 * @return array|false
		 */
		public static function get_wp_options() {
			if ( is_null( self::$wp_options ) ) {
				return false;
			}

			return self::$wp_options;
		}

		/**
		 * Get config files.
		 */
		public static function get_config_files() {

			$config_plugin_dir = WPGlobus::$PLUGIN_DIR_PATH . self::PLUGIN_CONFIG_DIR;

			/**
			 * WPGlobus SEO.
			 */
			if ( function_exists( 'wpglobus_seo__init' ) ) {
				self::$vendors[] = 'wpglobus-seo.json';
			}

			/**
			 * Yoast SEO.
			 * https://wordpress.org/plugins/wordpress-seo/
			 */
			if ( defined( 'WPSEO_VERSION' ) ) {
				/**
				 * check 'WPSEO_PREMIUM_PLUGIN_FILE' for premium add-on.
				 */
				self::$vendors[] = 'yoast-seo.json';
			}

			/**
			 * All in One SEO Pack.
			 * https://wordpress.org/plugins/all-in-one-seo-pack/
			 */
			if ( defined( 'AIOSEOP_VERSION' ) ) {
				/**
				 * Load config file for builder page only.
				 */
				if ( self::$builder->is_builder_page() ) {
					self::$vendors[] = 'all-in-one-seo-pack.json';
				}
			}

			/**
			 * Elementor.
			 * https://wordpress.org/plugins/elementor/
			 */
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				self::$vendors[] = 'elementor.json';
			}

			/**
			 * Advanced Custom Fields.
			 * https://wordpress.org/plugins/advanced-custom-fields/
			 */
			if ( function_exists( 'acf' ) ) {
				/**
				 * Check the existence of function to prevent getting fatal error in older version (checked with 4.4.12).
				 */
				if ( function_exists( 'acf_maybe_get_field' ) ) {
					self::$vendors[] = 'acf.json';
				}
			}

			/**
			 * Page Builder by SiteOrigin.
			 * https://wordpress.org/plugins/siteorigin-panels/
			 * // if ( defined('SITEORIGIN_PANELS_VERSION') )  {
			 * //    self::$vendors[] = 'siteorigin-panels.json';
			 * // }
			 */

			// TODO: builder ID can be a string or `false`. Need to refactor this condition or the `get_id` method.
			// phpcs:ignore WordPress.PHP.StrictComparisons
			if ( self::$builder->get_id() != '' && self::$builder->is_builder_page() ) {

				$addons = WPGlobus_Builders::get_addons();

				if ( ! empty( $addons ) ) {
					foreach ( $addons as $id => $addon ) {
						if ( 'add-on' === $addon['role'] ) {
							if ( ! empty( $addon['const'] ) && defined( $addon['const'] ) ) {
								self::$vendors[] = $addon['config_file'];
							}
						}
					}
				}
			}

			/**
			 * Now handle with config files.
			 */
			foreach ( self::$vendors as $file ) {

				if ( is_readable( $config_plugin_dir . $file ) ) {
					$file_name = pathinfo( $file, PATHINFO_FILENAME );

					self::$config[ $file_name ] = json_decode( file_get_contents( $config_plugin_dir . $file ), true );
				}
			}

			/**
			 * Filter vendor's config.
			 *
			 * @since 2.1.10
			 *
			 * @param array  $config  Config.
			 * @param object $builder An object WPGlobus_Config_Builder.
			 *
			 * @return array
			 */
			self::$config = apply_filters( 'wpglobus_config_vendors', self::$config, self::$builder );

		}

		/**
		 * Get multilingual fields for post.
		 *
		 * @param mixed $_meta Unused.
		 * @param array $_init
		 *
		 * @return array|false
		 */
		public static function get_post_ml_fields(
			/** @noinspection PhpUnusedParameterInspection */
			$_meta, $_init
		) {

			if ( ! self::$builder->is_builder_page() ) {
				/**
				 * Prevent getting multilingual fields for no builder page.
				 *
				 * @since 2.1.11
				 */
				return false;
			}

			$_post_ml_fields = array();

			if ( empty( $_init ) ) {
				return $_post_ml_fields;
			}

			// phpcs:ignore
			// $file = empty( $_init['file'] ) ? '' : WPGlobus::$PLUGIN_DIR_PATH . 'includes/' . $_init['file']; // TODO remove

			/** @var WPGlobus_Acf_2 $class */
			$class = empty( $_init['class'] ) ? '' : $_init['class'];

			if ( ! empty( $class ) && class_exists( $class ) ) {
				$_post_ml_fields = $class::get_post_multilingual_fields();
			}

			return $_post_ml_fields;
		}

		/**
		 * Get meta fields for post.
		 *
		 * @param mixed $_meta Unused.
		 * @param array $_init
		 *
		 * @return array|false
		 */
		public static function get_post_meta_fields(
			/** @noinspection PhpUnusedParameterInspection */
			$_meta, $_init
		) {

			if ( ! self::$builder->is_builder_page() ) {
				/**
				 * Prevent getting meta fields for no builder page.
				 *
				 * @since 2.1.11
				 */
				return false;
			}

			$_post_meta_fields = array();

			if ( empty( $_init ) ) {
				return $_post_meta_fields;
			}

			$file = empty( $_init['file'] ) ? '' : WPGlobus::$PLUGIN_DIR_PATH . 'includes/' . $_init['file'];

			/** @var WPGlobus_Acf_2 $class */
			$class = empty( $_init['class'] ) ? '' : $_init['class'];

			if ( ! empty( $file ) && file_exists( $file ) ) {
				/** @noinspection PhpIncludeInspection */
				include_once $file;
				if ( ! empty( $class ) && class_exists( $class ) ) {
					/**
					 * @since 2.1.3 Added post type parameter.
					 */
					$_post_meta_fields = $class::get_post_meta_fields( self::$builder->get( 'post_id' ), self::$builder->get( 'post_type' ) );
				}
			}

			return $_post_meta_fields;

		}

		/**
		 * Parse config files.
		 */
		public static function parse_config() {

			/**
			 * Parse post meta fields.
			 */
			if ( is_null( self::$post_meta_fields ) ) {

				self::$post_meta_fields = array();
				self::$post_ml_fields   = array();

				foreach ( self::$config as $vendor => $data ) {

					if ( isset( $data['post_meta_fields'] ) && is_array( $data['post_meta_fields'] ) ) :

						foreach ( $data['post_meta_fields'] as $_meta => $_init ) {

							if ( isset( $data['post_meta_fields'][ $_meta ] ) ) {

								if ( '*' === $_meta ) {
									$_arr = self::get_post_meta_fields( $_meta, $_init );
									if ( ! empty( $_arr ) ) {
										self::$post_meta_fields = array_merge( self::$post_meta_fields, $_arr );
									}
								} else {
									self::$post_meta_fields[] = $_meta;
								}
							}
						}

					endif;

					if ( isset( $data['post_ml_fields'] ) && is_array( $data['post_ml_fields'] ) ) :
						foreach ( $data['post_ml_fields'] as $_meta => $_init ) {
							if ( isset( $data['post_ml_fields'][ $_meta ] ) ) {

								if ( '*' === $_meta ) {
									$_arr = self::get_post_ml_fields( $_meta, $_init );
									if ( ! empty( $_arr ) ) {
										self::$post_ml_fields = array_merge( self::$post_ml_fields, $_arr );
									}
								} else {
									self::$post_ml_fields[] = $_meta;
								}
							}
						}
					endif;

				}

			}

			/**
			 * Parse WP options.
			 */
			if ( is_null( self::$wp_options ) ) {

				foreach ( self::$config as $vendor => $data ) {

					if ( isset( $data['wp_options'] ) && is_array( $data['wp_options'] ) ) :
						foreach ( $data['wp_options'] as $_option => $_init ) {
							if ( isset( $data['wp_options'][ $_option ] ) ) {
								self::$wp_options[] = $_option;
							}
						}
					endif;

				}

				if ( ! is_null( self::$wp_options ) ) {
					self::$wp_options = array_unique( self::$wp_options );
				}
			}

		}

	}

endif;
