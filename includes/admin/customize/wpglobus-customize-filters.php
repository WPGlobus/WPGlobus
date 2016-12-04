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

			return $disabled_sections;

		}

	}

endif;
