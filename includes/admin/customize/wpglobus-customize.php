<?php
/**
 * File: wpglobus-customize.php
 *
 * @package WPGlobus\Admin\Customizer
 */

global $wp_version;
if ( version_compare( $wp_version, '4.6', '>=' ) ) {
	require_once 'class-wpglobus-customize170.php';
	WPGlobus_Customize::controller();
} else {
	require_once 'class-wpglobus-customize140.php';
	WPGlobus_Customize::controller();
}
# --- EOF