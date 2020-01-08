<?php
/**
 * Filters for customizer
 *
 * All add_filter and add_action calls should be placed here
 *
 * @package WPGlobus\Admin\Customizer
 */
add_filter( 'wpglobus_customize_disabled_sections', array( 'WPGlobus_Customize_Filters', 'disable_sections' ) );

if ( ! class_exists( 'WPGlobus_Customize_Filters' ) ) :

	/**
	 * Class WPGlobus_Customize_Filters
	 */
	class WPGlobus_Customize_Filters {

		/**
		 * Disable sections in customizer.
		 *
		 * @param array $disabled_sections
		 *
		 * @return array
		 */
		public static function disable_sections( $disabled_sections ) {

			if ( class_exists( 'Easy_Google_Fonts' ) ) {
				/**
				 * @see https://wordpress.org/plugins/easy-google-fonts/
				 */
				$disabled_sections[] = 'tt_font_typography';
			}

			/**
			 * @since 2.2.30
			 */			
			if ( defined( 'GFP_VERSION' ) // premium
				|| 
				defined( 'OGF_VERSION' ) // free
			) {
				/**
				 * @see premium https://fontsplugin.com/
				 * @see free https://wordpress.org/plugins/olympus-google-fonts/
				 */
				$disabled_sections[] = 'ogf_custom';
				$disabled_sections[] = 'ogf_advanced__css';
			}
			
			/**
			 * @since 2.2.28
			 */
			if ( class_exists( 'WPGlobus_Customize_Themes' ) ) {
				
				/**
				 * @see W.I.P. `Prevent using element with no id attribute` in includes\js\wpglobus-customize-control190.js 
				 * to getting sections for disabling.
				 */
				$theme_name = WPGlobus_Customize_Themes::get_theme_name_lc();
				
				if ( 'startkit' == $theme_name ) {
					/**
					 * @see https://wordpress.org/themes/startkit/
					 */
					$disabled_sections[] = 'slider_setting';
					$disabled_sections[] = 'service_setting';
					$disabled_sections[] = 'testimonial_setting';
					$disabled_sections[] = 'footer_icon';
					$disabled_sections[] = 'Body_typography';
					$disabled_sections[] = 'paragraph_typography';
					$disabled_sections[] = 'H1_typography';
					$disabled_sections[] = 'H2_typography';
					$disabled_sections[] = 'H3_typography';
					$disabled_sections[] = 'H4_typography';
					$disabled_sections[] = 'H5_typography';
					$disabled_sections[] = 'H6_typography';
					$disabled_sections[] = 'menu_typography';
					$disabled_sections[] = 'section_typography';
				}
			}
			
			return $disabled_sections;

		}

	}

endif;
