<?php
/**
 * WPGlobus Uninstall
 * Deletes options
 * @package   WPGlobus
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/** @global wpdb $wpdb */
//global $wpdb;

/**
 * Delete options
 * Disabled as of 1.3.1:
 * @todo Do it only when User sets a special flag in settings. Write code that works on multisite. Think about cleaning all multilingual strings.
 */
//$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpglobus_option%';" );

# --- EOF
