<?php
/**
 * @package WPGlobus\Vendor
 *
 * @since 1.9.17
 */

/**
 * All In One SEO Pack.
 */
if ( defined( 'AIOSEOP_VERSION' ) ) {
	require_once( dirname( __FILE__ ) . '/aioseopack/class-wpglobus-aioseopack.php' );
	WPGlobus_All_in_One_SEO_Pack::get_instance();	
}

/**
 * ACF.
 * https://wordpress.org/plugins/advanced-custom-fields/
 * @todo W.I.P
 */
/* 
if ( $this->is_script_active('ACF') || $this->is_script_active('ACFPRO') ) {
	require_once( dirname( __FILE__ ) . '/acf/class-wpglobus-acf.php' );
	WPGlobus_Acf_1::get_instance();	
}
// */


# --- EOF