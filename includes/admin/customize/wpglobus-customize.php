<?php
/**
 * File: wpglobus-customize.php
 *
 * @package WPGlobus\Admin\Customizer
 */

global $wp_version;

if ( !defined('WPGLOBUS_CUSTOMIZE') || WPGLOBUS_CUSTOMIZE ) {
		
	if ( version_compare( $wp_version, '4.9-Beta1', '>=' ) ) {
		require_once 'class-wpglobus-customize190.php';
	} else if( version_compare( $wp_version, '4.6', '>=' ) ) { 	
		require_once 'class-wpglobus-customize170.php';
	} else {
		require_once 'class-wpglobus-customize140.php';
	}			
	WPGlobus_Customize::controller();
	
}
# --- EOF