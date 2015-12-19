<?php
/**
 * Multilingual Customizer
 * @package    WPGlobus
 * @subpackage WPGlobus/Admin
 * @since      1.4.0
 */

if ( ! class_exists( 'WPGlobus_Customize' ) ) :

	/**
	 * Class WPGlobus_Customize
	 */
	class WPGlobus_Customize {

		public static function controller() {
			/**
			 * @see \WP_Customize_Manager::wp_loaded
			 * It calls the `customize_register` action first,
			 * and then - the `customize_preview_init` action
			 */
			/* 
			add_action( 'customize_register', array(
				'WPGlobus_Customize',
				'action__customize_register'
			) ); */
			add_action( 'customize_preview_init', array(
				'WPGlobus_Customize',
				'action__customize_preview_init'
			) );

			/**
			 * This is called by wp-admin/customize.php
			 */
			add_action( 'customize_controls_enqueue_scripts', array(
				'WPGlobus_Customize',
				'action__customize_controls_enqueue_scripts'
			), 1000 );
			
			if ( WPGlobus_WP::is_admin_doing_ajax() ) {
				add_filter( 'clean_url', array(
					'WPGlobus_Customize',
					'filter__clean_url'
				), 10, 2 );
			}
			
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
		 * @param WP_Customize_Manager $wp_customize
		 */
		public static function action__customize_register( WP_Customize_Manager $wp_customize ) {}
		
		/**
		 * Load Customize Preview JS
		 * Used by hook: 'customize_preview_init'
		 * @see 'customize_preview_init'
		 */
		public static function action__customize_preview_init() {
			wp_enqueue_script(
				'wpglobus-customize-preview',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-preview' .
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
					'blogdescription' => WPGlobus_Core::text_filter( get_option( 'blogdescription' ), WPGlobus::Config()->language )
				)
			);
		}

		/**
		 * Load Customize Control JS
		 */
		public static function action__customize_controls_enqueue_scripts() {
			
			$disabled_instance_mask = array();
			$disabled_instance_mask[] = 'nav_menu';
			$disabled_instance_mask[] = 'new_menu_name';
			$disabled_instance_mask[] = 'widget';
			$disabled_instance_mask[] = 'color';
			$disabled_instance_mask[] = 'wpseo';
			$disabled_instance_mask[] = 'css';
			$disabled_instance_mask[] = 'facebook';
			$disabled_instance_mask[] = 'twitter';
			$disabled_instance_mask[] = 'linkedin';
			$disabled_instance_mask[] = 'behance';
			$disabled_instance_mask[] = 'dribbble';
			
			wp_enqueue_script(
				'wpglobus-customize-control140',
				WPGlobus::$PLUGIN_DIR_URL . 'includes/js/wpglobus-customize-control140' . WPGlobus::SCRIPT_SUFFIX() . '.js',
				array( 'jquery' ),
				WPGLOBUS_VERSION,
				true
			);
			wp_localize_script(
				'wpglobus-customize-control140',
				'WPGlobusCustomize',
				array(
					'version' => WPGLOBUS_VERSION,
					'disabledInstanceMask' 	=> $disabled_instance_mask,
					'elementSelector'		=> array( 'input[type=text]', 'textarea'),
					'findLinkBy'			=> array( 'link', 'url' )
				)
			);
			
		}

	} // class

endif;
# --- EOF
