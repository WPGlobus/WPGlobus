<?php
/**
 * Multilingual Customizer.
 *
 * @since      1.9.0
 *
 * @package    WPGlobus\Admin\Customizer
 */

if ( ! class_exists( 'WPGlobus_Customize' ) ) :

	/**
	 * Class WPGlobus_Customize
	 */
	class WPGlobus_Customize {

		/**
		 * Controller.
		 */
		public static function controller() {

			add_action( 'admin_init', array( __CLASS__, 'on__admin_init' ), 1 );

			/**
			 * It calls the `customize_register` action first,
			 * and then - the `customize_preview_init` action.
			 *
			 * @see \WP_Customize_Manager::wp_loaded
			 *
			 * add_action( 'customize_register', array(
			 * 'WPGlobus_Customize',
			 * 'action__customize_register'
			 * ) );
			 */

			/**
			 * Customizer filters.
			 *
			 * @since 1.5.0
			 */
			if ( WPGlobus_WP::is_pagenow( 'customize.php' ) ) {
				require_once 'wpglobus-customize-filters.php';
			}

			add_action(
				'customize_preview_init',
				array( 'WPGlobus_Customize', 'action__customize_preview_init' )
			);

			/**
			 * This is called by wp-admin/customize.php
			 */
			add_action(
				'customize_controls_enqueue_scripts',
				array( 'WPGlobus_Customize', 'action__customize_controls_enqueue_scripts' ),
				1000
			);

			if ( WPGlobus_WP::is_admin_doing_ajax() ) {
				add_filter(
					'clean_url',
					array( 'WPGlobus_Customize', 'filter__clean_url' ),
					10,
					2
				);
			}

			/**
			 * Filter customize_changeset_save_data.
			 *
			 * @see   wp-includes\class-wp-customize-manager.php
			 * @since 1.9.3
			 */
			add_filter(
				'customize_changeset_save_data',
				array( __CLASS__, 'filter__customize_changeset_save_data' ),
				1,
				2
			);

		}

		/**
		 * Action on admin init.
		 *
		 * @since 1.9.3
		 */
		public static function on__admin_init() {

			$excluded_mods = array(
				'0',
				'nav_menu_locations',
				'sidebars_widgets',
				'custom_css_post_id',
				'wpglobus_blogname',
				'wpglobus_blogdescription',
			);

			$mods = get_theme_mods();

			$filtered_mods = array();

			if ( $mods ) {
				foreach ( $mods as $mod_key => $mod_value ) {

					if ( in_array( $mod_key, $excluded_mods, true ) ) {
						continue;
					}

					if ( ! is_string( $mod_value ) ) {
						continue;
					}

					$filtered_mods[ $mod_key ] = $mod_value;

				}
			}

			/**
			 * Filters the theme mods before save.
			 *
			 * @since 1.9.3
			 *
			 * @param array      $filtered_mods Filtered theme modifications.
			 * @param array|void $mods          Theme modifications.
			 */
			$filtered_mods = apply_filters( 'wpglobus_customize_filtered_mods', $filtered_mods, $mods );

			foreach ( $filtered_mods as $mod_key => $mod_value ) {

				/**
				 * Filter {@see filter "pre_set_theme_mod_{$name}" in \wp-includes\theme.php}.
				 */
				add_filter(
					"pre_set_theme_mod_{$mod_key}",
					array( __CLASS__, 'filter__pre_set_theme_mod' ),
					1,
					2
				);

			}
		}

		/**
		 * Filter a theme mod.
		 *
		 * @since 1.9.3
		 *
		 * @param string $value     The value.
		 * @param string $old_value Unused.
		 *
		 * @return bool|string
		 */
		public static function filter__pre_set_theme_mod(
			$value,
			/**
			 * Unused.
			 *
			 * @noinspection PhpUnusedParameterInspection
			 */
			$old_value
		) {

			if ( ! is_string( $value ) ) {
				return $value;
			}

			$new_value = self::_build_multilingual_string( $value );

			if ( $new_value ) {
				return $new_value;
			}

			return $value;

		}

		/**
		 * Save/update a changeset.
		 *
		 * @since 1.9.3
		 *
		 * @param array  $data           The data.
		 * @param string $filter_context Unused.
		 *
		 * @return mixed
		 */
		public static function filter__customize_changeset_save_data(
			$data,
			/**
			 * Unused.
			 *
			 * @noinspection PhpUnusedParameterInspection
			 */
			$filter_context
		) {

			foreach ( $data as $option => $value ) {

				$new_value = self::_build_multilingual_string( $value['value'] );

				if ( $new_value ) {
					$data[ $option ]['value'] = $new_value;
				}
			}

			return $data;

		}

		/**
		 * Build standard WPGlobus multilingual string.
		 *
		 * @since 1.9.3
		 *
		 * @param string $value The value.
		 *
		 * @return bool|string
		 */
		public static function _build_multilingual_string( $value ) { // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore

			/**
			 * Ignore if not a string.
			 *
			 * @since 1.9.6
			 */
			if ( ! is_string( $value ) ) {
				return $value;
			}

			// $new_value = '';

			if ( false === strpos( $value, '|||' ) ) {
				$new_value = false;
			} else {

				$arr1 = array();
				$arr  = explode( '|||', $value );
				foreach ( $arr as $k => $val ) {
					// Note: 'null' is a string, not real `null`.
					if ( 'null' !== $val ) {
						$arr1[ WPGlobus::Config()->enabled_languages[ $k ] ] = $val;
					}
				}

				$new_value = WPGlobus_Utils::build_multilingual_string( $arr1 );

			}

			return $new_value;

		}

		/**
		 * Filter a string to check translations for URL.
		 * // We build multilingual URLs in customizer using the ':::' delimiter.
		 * We build multilingual URLs in customizer using the '|||' delimiter.
		 * See wpglobus-customize-control.js
		 *
		 * @note  To work correctly, value of $url should begin with URL for default language.
		 * @see   esc_url() - the 'clean_url' filter
		 * @since 1.3.0
		 *
		 * @param string $url          The cleaned URL.
		 * @param string $original_url The URL prior to cleaning.
		 *
		 * @return string
		 */
		public static function filter__clean_url( $url, $original_url ) {

			if ( false !== strpos( $original_url, '|||' ) ) {
				$arr1 = array();
				$arr  = explode( '|||', $original_url );
				foreach ( $arr as $k => $val ) {
					// Note: 'null' is a string, not real `null`.
					if ( 'null' !== $val ) {
						$arr1[ WPGlobus::Config()->enabled_languages[ $k ] ] = $val;
					}
				}

				return WPGlobus_Utils::build_multilingual_string( $arr1 );
			}

			return $url;
		}

		/**
		 * Add multilingual controls.
		 * The original controls will be hidden.
		 *
		 * @param WP_Customize_Manager $wp_customize Customize Manager.
		 */
		public static function action__customize_register( WP_Customize_Manager $wp_customize ) {
		}

		/**
		 * Load Customize Preview JS
		 * Used by hook: 'customize_preview_init'
		 *
		 * @see 'customize_preview_init'
		 */
		public static function action__customize_preview_init() {
			wp_enqueue_script(
				'wpglobus-customize-preview',
				WPGlobus::plugin_dir_url() . 'includes/js/wpglobus-customize-preview' .
				WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery', 'customize-preview' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_localize_script(
				'wpglobus-customize-preview',
				'WPGlobusCustomize',
				array(
					'version'         => WPGLOBUS_VERSION,
					'blogname'        => WPGlobus_Core::text_filter( get_option( 'blogname' ), WPGlobus::Config()->language ),
					'blogdescription' => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language ),
				)
			);
		}

		/**
		 * Load Customize Control JS
		 */
		public static function action__customize_controls_enqueue_scripts() {

			global $wp_version;

			/**
			 * See wp.customize.control elements
			 * for example wp.customize.control('blogname');
			 */
			$disabled_setting_mask = array();

			// navigation menu elements.
			$disabled_setting_mask[] = 'nav_menu_item';
			$disabled_setting_mask[] = 'nav_menu[';
			$disabled_setting_mask[] = 'nav_menu_locations';
			$disabled_setting_mask[] = 'new_menu_name';

			// widgets.
			$disabled_setting_mask[] = 'widgets';

			// color elements.
			$disabled_setting_mask[] = 'color';

			// yoast seo.
			$disabled_setting_mask[] = 'wpseo';

			// css elements.
			$disabled_setting_mask[] = 'css';

			// social networks elements.
			$disabled_setting_mask[] = 'facebook';
			$disabled_setting_mask[] = 'twitter';
			$disabled_setting_mask[] = 'linkedin';
			$disabled_setting_mask[] = 'behance';
			$disabled_setting_mask[] = 'dribbble';
			$disabled_setting_mask[] = 'instagram';
			/**
			 * Tumblr.
			 *
			 * @since 1.4.4
			 */
			$disabled_setting_mask[] = 'tumblr';
			$disabled_setting_mask[] = 'flickr';
			$disabled_setting_mask[] = 'wordpress'; // phpcs:ignore WordPress.WP.CapitalPDangit.Misspelled
			$disabled_setting_mask[] = 'youtube';
			$disabled_setting_mask[] = 'pinterest';
			$disabled_setting_mask[] = 'github';
			$disabled_setting_mask[] = 'rss';
			$disabled_setting_mask[] = 'google';
			$disabled_setting_mask[] = 'email';
			/**
			 * Dropbox.
			 *
			 * @since 1.5.9
			 */
			$disabled_setting_mask[] = 'dropbox';
			$disabled_setting_mask[] = 'foursquare';
			$disabled_setting_mask[] = 'vine';
			$disabled_setting_mask[] = 'vimeo';
			/**
			 * Yelp.
			 *
			 * @since 1.6.0
			 */
			$disabled_setting_mask[] = 'yelp';

			/**
			 * Exclude fields from Static Front Page section.
			 * It may be added to customizer in many themes.
			 *
			 * @since 1.7.6
			 */
			$disabled_setting_mask[] = 'page_on_front';
			$disabled_setting_mask[] = 'page_for_posts';

			/**
			 * Filter to disable fields in customizer.
			 * See wp.customize.control elements
			 * Returning array.
			 *
			 * @since 1.4.0
			 *
			 * @param array $disabled_setting_mask An array of disabled masks.
			 */
			$disabled_setting_mask = apply_filters( 'wpglobus_customize_disabled_setting_mask', $disabled_setting_mask );

			$element_selector = array( 'input[type=text]', 'textarea' );

			/**
			 * Filter for element selectors.
			 * Returning array.
			 *
			 * @since 1.4.0
			 *
			 * @param array $element_selector An array of selectors.
			 */
			$element_selector = apply_filters( 'wpglobus_customize_element_selector', $element_selector );

			$set_link_by = array( 'link', 'url' );

			/**
			 * Filter of masks to determine links.
			 * See value data-customize-setting-link of element
			 * Returning array.
			 *
			 * @since 1.4.0
			 *
			 * @param array $set_link_by An array of masks.
			 */
			$set_link_by = apply_filters( 'wpglobus_customize_setlinkby', $set_link_by );

			/**
			 * Filter of disabled sections.
			 *
			 * Returning array.
			 *
			 * @since 1.5.0
			 *
			 * @param array $disabled_sections An array of sections.
			 */
			$disabled_sections = array();

			$disabled_sections = apply_filters( 'wpglobus_customize_disabled_sections', $disabled_sections );

			/**
			 * Generate language select button for customizer
			 *
			 * @since 1.6.0
			 *
			 * @todo  http://stackoverflow.com/questions/9607252/how-to-detect-when-an-element-over-another-element-in-javascript
			 */
			$attributes['href']  = '#';
			$attributes['style'] = 'margin-left:48px;';
			$attributes['class'] = 'customize-controls-close wpglobus-customize-selector';

			/**
			 * Filter of attributes to generate language selector button.
			 * For example @see Divi theme http://www.elegantthemes.com/gallery/divi/ .
			 *
			 * Returning array.
			 *
			 * @since 1.6.0
			 *
			 * @param array $attributes An array of attributes.
			 * @param string Name of current theme.
			 */
			$attributes = apply_filters( 'wpglobus_customize_language_selector_attrs', $attributes, WPGlobus_Customize_Options::get_theme( 'name' ) );

			$string = '';

			foreach ( $attributes as $attribute => $value ) {
				if ( null !== $value ) {
					$string .= esc_attr( $attribute ) . '="' . esc_attr( $value ) . '" ';
				}
			}

			$selector_button = sprintf(
				'<a %1$s data-language="' . WPGlobus::Config()->default_language . '">%2$s</a>',
				trim( $string ),
				'<span class="wpglobus-globe"></span>'
			);

			/**
			 * Since 1.7.9
			 */
			$changeset_uuid = null;
			if ( ! empty( $_GET['changeset_uuid'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				$changeset_uuid = sanitize_text_field( wp_unslash( $_GET['changeset_uuid'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			}

			/**
			 * Since 1.9.0
			 */
			$selector_type  = 'dropdown';
			$selector_types = array( 'dropdown', 'switch' );

			/**
			 * Filter selector type.
			 *
			 * @since 1.9.0
			 *
			 * @param string $selector_type  Name of the current selector type.
			 * @param array  $selector_types An array of existing selector types.
			 *
			 * @return string
			 */
			$selector_type = apply_filters( 'wpglobus_customize_language_selector_type', $selector_type, $selector_types );

			if ( ! in_array( $selector_type, $selector_types, true ) ) {
				$selector_type = 'dropdown';
			}

			/**
			 * Adjust for WP 5.2+.
			 *
			 * @since 2.2.0
			 */
			$selector_html = '<span style="margin-left:5px;" class="wpglobus-icon-globe"></span><span class="current-language" style="font-weight:bold;">{{language}}</span>';
			if ( version_compare( $wp_version, '5.1.999', '>' ) ) {
				$selector_html = '<span style="position:fixed;top:-7px;">' . $selector_html . '</span>';
			}

			wp_enqueue_script(
				'wpglobus-customize-control190',
				WPGlobus::plugin_dir_url() . 'includes/js/wpglobus-customize-control190' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_localize_script(
				'wpglobus-customize-control190',
				'WPGlobusCustomize',
				array(
					'version'             => WPGLOBUS_VERSION,
					'selectorType'        => $selector_type,
					'selectorButton'      => $selector_button,
					'languageAdmin'       => WPGlobus::Config()->language,
					'disabledSettingMask' => $disabled_setting_mask,
					'elementSelector'     => $element_selector,
					'setLinkBy'           => $set_link_by,
					'disabledSections'    => $disabled_sections,
					'controlClass'        => 'wpglobus-customize-control',
					'changeset_uuid'      => $changeset_uuid,
					'selector_html'       => $selector_html,
				)
			);

		}

	} // class

endif;
