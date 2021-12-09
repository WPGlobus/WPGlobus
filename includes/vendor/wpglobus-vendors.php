<?php
/**
 * File: wpglobus-vendors.php
 *
 * @package WPGlobus\Vendors
 * 
 * We start this file to support the plugins/add-ons in builder mode.
 * @since 1.9.17
 * @see function maybe_run gutenberg builder in wpglobus\includes\class-wpglobus.php
 */

/**
 * All In One SEO Pack.
 */
if ( defined( 'AIOSEOP_VERSION' ) ) {
	require_once( dirname( __FILE__ ) . '/aioseopack/class-wpglobus-aioseopack.php' );
	WPGlobus_All_in_One_SEO_Pack::get_instance();	
}

/**
 * Pods.
 * @since 2.3.0
 */
if ( defined( 'PODS_VERSION' ) ) {
	require_once( dirname( __FILE__ ) . '/pods/class-wpglobus-vendor-pods.php' );
	WPGlobus_Vendor_Pods::get_instance();
}

/**
 * Rank Math SEO.
 * @since 2.4.3
 */
if ( defined( 'RANK_MATH_VERSION' ) ) {
	require_once( dirname( __FILE__ ) . '/rank-math-seo/class-wpglobus-vendor-rank_math_seo.php' );
	WPGlobus_Vendor_RankMathSEO::get_instance();
}

/**
 * ACF.
 * https://wordpress.org/plugins/advanced-custom-fields/
 * @since 2.6.6
 */
if ( $this->vendors_scripts['ACF'] || $this->vendors_scripts['ACFPRO'] ) {
	require_once( dirname( __FILE__ ) . '/acf/class-wpglobus-vendor-acf.php' );
	WPGlobus_Vendor_Acf::get_instance( 
		array(
			'vendor_scripts' => $this->vendors_scripts
		)
	);
}

# --- EOF