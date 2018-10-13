<?php
/**
 * File: compatibility.php
 *
 * @package WPGlobus/Options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WPGlobus::$PLUGIN_DIR_PATH . 'includes/builders/class-wpglobus-builders.php';

if ( ! function_exists('is_plugin_active') ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

$add_ons = WPGlobus_Builders::get_addons();

$compatibility  = '<h3>' . esc_html__( 'List of supported add-ons', 'wpglobus' ) . ':</h3>';
$compatibility .= '<table>';
$compatibility .= '<thead>';
$compatibility .= '<tr>';
$compatibility .= 	'<th>Add on</th>';
$compatibility .= 	'<th>Current version</th>';
$compatibility .= 	'<th>Supported minimum version</th>';
$compatibility .= 	'<th>Status</th>';
$compatibility .= '</tr>';
$compatibility .= '</thead>';

$compatibility .= '<tbody>';
foreach( $add_ons as $add_on ) {
	
	$_version 	= '';
	$_status 	= '';
	$_file = WP_PLUGIN_DIR . '/' . $add_on['path'];
	if ( file_exists( $_file ) ) {
	
		$_fd = get_file_data( $_file, array('version'=>'Version') );
		$_version = $_fd['version'];
		
		if ( is_plugin_active( $add_on['path'] ) ) {
			$_status = 'active';
		} else {
			$_status = 'present, inactive';
		}
		
	} else {
		$_status = 'not present';
	}	
	
	$compatibility .= '<tr>';
	$compatibility .= 	'<td>' . $add_on['plugin_name'] . '</td>';
	$compatibility .= 	'<td>' . $_version . '</td>';
	$compatibility .= 	'<td>' . $add_on['supported_min_version'] . '</td>';
	$compatibility .= 	'<td>' . $_status . '</td>';
	$compatibility .= '</tr>';
	
}
$compatibility .= '</tbody>';
$compatibility .= '</table>';

return $compatibility;

# --- EOF