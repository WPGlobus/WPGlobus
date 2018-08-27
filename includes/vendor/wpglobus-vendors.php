<?php

/**
 * All In One SEO Pack.
 */
if ( defined( 'AIOSEOP_VERSION' ) ) {
	require_once( dirname( __FILE__ ) . '/aioseopack/class-wpglobus-aioseopack.php' );
	WPGlobus_All_in_One_SEO_Pack::get_instance();	
}

# --- EOF