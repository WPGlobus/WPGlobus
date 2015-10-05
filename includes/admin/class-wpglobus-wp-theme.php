<?php
/**
 * Theme compatibility
 * @package   WPGlobus/Admin
 */

if ( ! class_exists( 'WPGlobus_WP_Theme' ) ) :

	/**
	 * Class WPGlobus_WP_Theme
	 */
	class WPGlobus_WP_Theme {

		/**
		 * WPGlobus config file
		 */
		public $wpglobus_config_file = 'wpglobus-config.json';

		/**
		 * Config file from WPML
		 */
		public $wpml_config_file = 'wpml-config.xml';

		/**
		 * Full path to config file
		 */
		public $config_dir_file = '';

		/**
		 * Array of paths to themes
		 */
		public $theme_dir = array();

		/**
		 * Config
		 */
		public $config = array();

		/**
		 * Source of config
		 */
		public $config_from = '';

		/** */
		public function __construct() {

			/**
			 * get the absolute path to the child theme directory
			 */
			$this->theme_dir['child'] = get_stylesheet_directory();

			/**
			 * in the case a child theme is being used, the absolute path to the parent theme directory will be returned
			 */
			$this->theme_dir['parent'] = get_template_directory();

			$this->get_config();

			if ( ! empty( $this->config ) ) {

				add_filter( 'wpglobus_localize_custom_data', array( $this, 'custom_data' ) );

				add_filter( 'wpglobus_enabled_pages', array( $this, 'enable_page' ) );

			}

		}

		/**
		 * Add custom fields to WPGlobusDialog
		 *
		 * @param array $data
		 * @return array
		 */
		public function custom_data( $data ) {

			$elements = array();

			if ( $this->config_from === $this->wpml_config_file ) {

				foreach ( $this->config['wpml-config']['admin-texts']['key']['key'] as $elem ) {
					if ( empty( $elem['attr'] ) ) {
						/**
						 * single element in wpml-config.xml file
						 */
						$elements[] = $elem['name'];
					} else {
						$elements[] = $elem['attr']['name'];
					}
				}

			} elseif ( $this->config_from === $this->wpglobus_config_file ) {

				if ( ! empty( $this->config['admin_texts'] ) ) {
					foreach ( $this->config['admin_texts'] as $field_name => $field_type ) {
						$elements[] = $field_name;
					}
				}

			}

			if ( ! empty( $elements ) ) {
				if ( empty( $data['addElements'] ) ) {
					$data['addElements'] = $elements;
				} else {
					$data['addElements'] = array_merge(
						$data['addElements'],
						$elements
					);
				}
			}

			return $data;

		}

		/**
		 * Get config from file
		 */
		public function get_config() {

			$config_files = array();

			/**
			 * First look for WPGlobus config
			 */
			$config_files[] = $this->wpglobus_config_file;

			/**
			 * and then look for a WPML config
			 */
			$config_files[] = $this->wpml_config_file;

			$config_file = '';

			foreach ( $config_files as $config_file ) :

				$this->config_dir_file = '';

				/**
				 * First priority: look for a config file in WP_LANG_DIR folder.
				 * For example, for the theme slug `my-theme`, the file path is:
				 * wp-content/languages/themes/my-theme-wpglobus-config.json
				 * *
				 * We'll check for both child and parent theme slugs
				 */
				foreach ( array( get_stylesheet(), get_template() ) as $theme_slug ) {
					$config_in_wp_lang_dir = WP_LANG_DIR . '/themes/' . $theme_slug . '-' . $config_file;

					if ( is_file( $config_in_wp_lang_dir ) && is_readable( $config_in_wp_lang_dir ) ) {
						$this->config_dir_file = $config_in_wp_lang_dir;
						break 2;
					}
				}

				/**
				 * Then, check for the config file provided by the theme author:
				 *
				 * @example wp-content/themes/my-theme/wpglobus-config.json
				 */

				if ( $this->theme_dir['parent'] === $this->theme_dir['child'] ) {
					$file = $this->theme_dir['parent'] . '/' . $config_file;
					if ( file_exists( $file ) ) {
						$this->config_dir_file = $file;
					}
				} else {
					foreach ( $this->theme_dir as $relation => $dir ) {

						$file = $dir . '/' . $config_file;
						if ( 'child' === $relation && file_exists( $file ) ) {
							/**
							 * Now config in child theme has highest priority
							 */
							$this->config_dir_file = $file;
							break;
						}
						if ( 'parent' === $relation && file_exists( $file ) ) {
							$this->config_dir_file = $file;
						}

					}
				}

				if ( ! empty( $this->config_dir_file ) ) {
					break;
				}

			endforeach;

			if ( empty( $this->config_dir_file ) ) {
				$config_file = '';
			}

			/**
			 * If a configuration file has been found in the previous loop,
			 * we have `$this->config_dir_file` containing the file path,
			 * and the loop iterator, `$config_file`, pointing to the file type.
			 */
			switch ( $config_file ) {
				case $this->wpglobus_config_file :
					$this->config      = $this->json2array( file_get_contents( $this->config_dir_file ) );
					$this->config_from = $this->wpglobus_config_file;
					break;
				case $this->wpml_config_file :

					/**
					 * Compatibility with WPML configuration file:
					 * their XML is parsed using their function, `icl_xml2array`,
					 * which we copied as-is to the `lib` folder.
					 *
					 * @link https://wpml.org/documentation/support/language-configuration-files/
					 */

					/** @noinspection PhpIncludeInspection */
					require_once WPGlobus::$PLUGIN_DIR_PATH . 'lib/xml2array.php';
					/** @noinspection PhpUndefinedFunctionInspection */
					$this->config      = icl_xml2array( file_get_contents( $this->config_dir_file ) );
					$this->config_from = $this->wpml_config_file;
					break;
			};


		}

		/**
		 * Enable `themes.php` page to load our scripts and styles
		 *
		 * @param array $pages List of pages already enabled
		 * @return array Modified list of pages
		 */
		public function enable_page( $pages ) {

			if ( empty( $this->config_dir_file ) ) {
				return $pages;
			}

			if ( ! empty( $_GET['page'] ) && WPGlobus_WP::is_pagenow( 'themes.php' ) ) {
				$pages[] = 'themes.php';
			}

			return $pages;

		}

		/**
		 * Convert JSON to array
		 *
		 * @param string $content JSON
		 * @return array Array
		 */
		public function json2array( $content ) {
			$converted = json_decode( $content, true );

			return $converted;
		}

	} // class

endif;

# --- EOF
