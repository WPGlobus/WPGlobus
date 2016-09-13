<?php
/**
 * File: wpglobus-yoastseo.php
 *
 * @package WPGlobus\Yoast
 */


if ( defined( 'WPSEO_VERSION' ) ) {

	if ( version_compare( WPSEO_VERSION, '3.4', '>=' ) ) {

		require_once 'vendor/class-wpglobus-yoastseo34.php';
		WPGlobus_YoastSEO::controller();

	} else {

		if ( version_compare( WPSEO_VERSION, '3.0.0', '<' ) ) {
			require_once 'vendor/class-wpglobus-wpseo.php';
			WPGlobus_WPSEO::controller();
		} else {
			if ( version_compare( WPSEO_VERSION, '3.2.0', '<' ) ) {
				require_once 'vendor/class-wpglobus-yoastseo30.php';
				WPGlobus_YoastSEO::controller();
			} else {
				if ( version_compare( WPSEO_VERSION, '3.3.0', '>=' ) ) {
					require_once 'vendor/class-wpglobus-yoastseo33.php';
					WPGlobus_YoastSEO::controller();
				} else {
					require_once 'vendor/class-wpglobus-yoastseo32.php';
					WPGlobus_YoastSEO::controller();
				}
			}
		}

	}

}
// */
