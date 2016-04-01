<?php
/**
 * @package WPGlobus
 * @since 1.5.0
 */
if ( ! function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {

	function qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage(  $text ) {
		
		/**
		 * Revslider
		 */
		if ( empty( $text ) ) {
			return $text;	
		}	

		return WPGlobus_Core::text_filter( $text, WPGlobus::Config()->language );
		
	}	

}
