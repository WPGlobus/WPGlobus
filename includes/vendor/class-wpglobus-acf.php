<?php
/**
 * File: class-wpglobus-acf.php
 *
 * @package WPGlobus\Vendor\ACF.
 */

/**
 * Class WPGlobus_Acf
 *
 * @since 1.2.2
 */
class WPGlobus_Acf {

	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * obsolete @since 2.6.6 
		 * @todo Remove after testing.
		 */
		add_filter(
			'acf/field_group/get_options',
			array(
				'WPGlobus_Acf',
				'filter__acf_get_options'
			), 99, 2
		);

		/**
		 * @since 2.2.27
		 * @see advanced-custom-fields\includes\acf-field-group-functions.php
		 */
		add_filter(
			'acf/get_field_group_style',
			array(
				__CLASS__,
				'filter__get_field_group_style'
			), 10, 2
		);
	}
	
	/**
	 * Filters the generated CSS styles.
	 *
	 * @since 2.2.27
	 *
	 * @param	string $style The CSS styles.
	 * @param	array $field_group The field group array.
	 */	
	public static function filter__get_field_group_style($style, $field_group){
		
		if( is_array($field_group['hide_on_screen']) ) {
			
			if ( in_array( 'the_content', $field_group['hide_on_screen'], true ) ) {
				/**
				 * If editor is hidden by ACF, we hide WPGlobus, too.
				 */
				add_filter(
					'wpglobus_postdivrich_style',
					array(
						__CLASS__,
						'filter__postdivrich_style'
					), 10, 2
				);
			}
			
		}

		return $style;
	}
	
	/**
	 * Filter @see 'acf/field_group/get_options'
	 *
	 * @since 1.2.2
	 * @param array $options ACF options
	 * @param int   $acf_id  Unused
	 * @return array
	 */
	public static function filter__acf_get_options(
		$options,
		/** @noinspection PhpUnusedParameterInspection */
		$acf_id
	) {
		if ( in_array( 'the_content', $options['hide_on_screen'], true ) ) {
			/**
			 * If ACF is hidden, we hide WPGlobus, too
			 */
			add_filter(
				'wpglobus_postdivrich_style',
				array(
					'WPGlobus_Acf',
					'filter__postdivrich_style'
				), 10, 2
			);
		}

		return $options;
	}

	/**
	 * Filter postdivrich style for extra language.
	 *
	 * @since 1.2.2
	 * @param string $style    Current CSS rule
	 * @param string $language Unused
	 * @return string
	 */
	public static function filter__postdivrich_style(
		$style,
		/** @noinspection PhpUnusedParameterInspection */
		$language
	) {
		return $style . ' display:none;';
	}

} // class

# --- EOF
