<?php
/**
 * @package WPGlobus\ACF
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

		add_filter(
			'acf/field_group/get_options',
			array(
				'WPGlobus_Acf',
				'filter__acf_get_options'
			), 99, 2
		);

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
