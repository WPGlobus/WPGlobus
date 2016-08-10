<?php
/**
 * @package WPGlobus
 */

/**
 * Class WPGlobus_Widget_Logic
 */
class WPGlobus_Widget_Logic {
	
	public static function controller() {
		add_filter( 'widget_logic_eval_override', array ( __CLASS__, 'filter__data' ), 0 );
	}
	
	/**
	 * Filter for Widget Logic string
	 * 
	 * @since 1.6.0
	 *
	 * @param string $wl_value
	 *
	 * @return string
	 */
	public static function filter__data( $wl_value ) {

		if ( empty( $wl_value ) ) {
			return $wl_value;
		}
		
		return WPGlobus_Core::text_filter( $wl_value, WPGlobus::Config()->language );	
		
	}

}
