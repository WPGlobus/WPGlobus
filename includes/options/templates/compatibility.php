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

/** @noinspection PhpIncludeInspection */
require_once WPGlobus::plugin_dir_path() . 'includes/builders/class-wpglobus-builders.php';

if ( ! function_exists( 'is_plugin_active' ) ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
}

$add_ons = WPGlobus_Builders::get_addons();

$compatibility = '<h3>' . esc_html__( 'List of supported add-ons', 'wpglobus' ) . ':</h3>';

$compatibility .= '<table id="wpglobus-options-compatibility">';
$compatibility .= '<thead>';
$compatibility .= '<tr>';
$compatibility .= '<th>' . esc_html__( 'Add-on', 'wpglobus' ) . '</th>';
$compatibility .= '<th>' . esc_html__( 'Current version', 'wpglobus' ) . '</th>';
$compatibility .= '<th>' . esc_html__( 'Supported minimum version', 'wpglobus' ) . '</th>';
$compatibility .= '<th>' . esc_html__( 'Stage', 'wpglobus' ) . '</th>';
$compatibility .= '<th>' . esc_html__( 'Status', 'wpglobus' ) . '</th>';
$compatibility .= '</tr>';
$compatibility .= '</thead>';

$compatibility .= '<tbody>';
foreach ( $add_ons as $add_on ) {

	$_version = '';
	$_status  = '';
	$_file    = WP_PLUGIN_DIR . '/' . $add_on['path'];
	if ( file_exists( $_file ) ) {

		$_fd      = get_file_data( $_file, array( 'version' => 'Version' ) );
		$_version = $_fd['version'];

		if ( is_plugin_active( $add_on['path'] ) ) {
			$_status = esc_html__( 'Active', 'wpglobus' );
		} else {
			$_status = esc_html__( 'Installed, inactive', 'wpglobus' );
		}
	} else {
		$_status = esc_html__( 'Not installed', 'wpglobus' );
	}

	$_stage = '';
	if ( empty( $add_on['stage'] ) ) {
		$_stage = 'production';
	} else {
		if ( 'beta' === $add_on['stage'] ) {
			$_stage = $add_on['stage'] . ' *)';
		} else {
			$_stage = $add_on['stage'];
		}
	}

	$compatibility .= '<tr>';
	$compatibility .= '<td>' . $add_on['plugin_name'] . '</td>';
	$compatibility .= '<td>' . $_version . '</td>';
	$compatibility .= '<td>' . $add_on['supported_min_version'] . '</td>';
	$compatibility .= '<td>' . $_stage . '</td>';
	$compatibility .= '<td>' . $_status . '</td>';
	$compatibility .= '</tr>';

}
$compatibility .= '</tbody>';
$compatibility .= '</table>';

return $compatibility;
