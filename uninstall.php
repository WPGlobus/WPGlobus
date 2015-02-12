<?php
/**
 * WPGlobus Uninstall
 * Deletes options
 * @package   WPGlobus
 * @todo Should we clean all languages?
 * @todo Should we ask whether to delete options or not?
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/** @global wpdb $wpdb */
global $wpdb;

/**
 * Delete options
 * @todo Make wpglobus_option a class constant instead of var
 */
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'wpglobus_option%';" );

# --- EOF