<?php
/**
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Acf
 * @since 1.2.2
 */

class WPGlobus_Acf {
	
	function __construct() {
		
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
	 * @param array $options
	 * @param int $acf_id
	 * @return array
	 */	
	public static function filter__acf_get_options( $options, $acf_id ){
		if( in_array( 'the_content', $options['hide_on_screen'] ) ) {
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
	 * Filter postdivrich style for extra language
	 * 
	 * @since 1.2.2
	 * @param string $style
	 *
	 * @return string
	 */
	public static function filter__postdivrich_style( $style, $language ){
		return $style . 'display:none;';
	}	

}	