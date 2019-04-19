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

global $wp_version;

$add_ons = WPGlobus_Builders::get_addons();

if ( ! empty( $add_ons['gutenberg'] ) ) {

	/**
	 * We have Gutenberg in core since WP 5.0.
	 *
	 * @since 2.0
	 */
	if ( version_compare( $wp_version, '4.9.99', '>' ) ) {
		$add_ons['gutenberg']['context'] = 'core';
	} else {
		$add_ons['gutenberg']['context'] = '';
	}
}

$compatibility = '';

if ( ! empty( $add_ons['gutenberg'] ) ) {

	if ( 'core' === $add_ons['gutenberg']['context'] ) {

		$_status = esc_html__( 'In the WordPress core', 'wpglobus' );

		$compatibility .= '<h3>' . esc_html__( 'Core', 'wpglobus' ) . ':</h3>';
		$compatibility .= '<table id="wpglobus-options-compatibility-core" style="border-collapse: collapse;">';

		$compatibility .= '<thead>';
		$compatibility .= '<tr>';
		$compatibility .= '<th>&nbsp;</th>';
		$compatibility .= '<th>' . esc_html__( 'Current version', 'wpglobus' ) . '</th>';
		$compatibility .= '<th>' . esc_html__( 'Stage', 'wpglobus' ) . '</th>';
		$compatibility .= '<th>' . esc_html__( 'Status', 'wpglobus' ) . '</th>';
		$compatibility .= '</tr>';
		$compatibility .= '</thead>';

		$compatibility .= '<tbody>';
		$compatibility .= '<tr>';
		$compatibility .= '<td>' . esc_html__( 'Block editor', 'wpglobus' ) . '</td>';
		$compatibility .= '<td style="text-align: right">' . $wp_version . '</td>';
		$compatibility .= '<td>' . esc_html__( 'production', 'wpglobus' ) . '</td>';
		$compatibility .= '<td>' . $_status . '</td>';
		$compatibility .= '</tr>';
		$compatibility .= '</tbody>';

		$compatibility .= '</table>';
	} else {
		$compatibility .= '<h3>' . esc_html__( 'WordPress version', 'wpglobus' ) . ' ' . $wp_version . '</h3>';
	}
}

$compatibility .= '<h3>' . esc_html__( 'List of supported add-ons', 'wpglobus' ) . ':</h3>';

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

foreach ( $add_ons as $_id => $add_on ) {

	$_version = '';
	$_status  = '';

	$_file = WP_PLUGIN_DIR . '/' . $add_on['path'];
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
			if ( ! empty( $add_on['beta_version'] ) ) {
				$_stage = $add_on['stage'] . '-' . $add_on['beta_version'];
			} else {
				$_stage .= $add_on['stage'];
			}
			$_stage .= ' *)';
		} else {
			$_stage = $add_on['stage'];
		}
	}

	$compatibility .= '<tr>';
	$compatibility .= '<td>' . $add_on['plugin_name'] . '</td>';
	$compatibility .= '<td style="text-align: right">' . $_version . '</td>';
	$compatibility .= '<td style="text-align: right">' . $add_on['supported_min_version'] . '</td>';
	$compatibility .= '<td>' . $_stage . '</td>';
	$compatibility .= '<td>' . $_status . '</td>';
	$compatibility .= '</tr>';

}

$compatibility .= '</tbody>';
$compatibility .= '</table>';

return $compatibility;
