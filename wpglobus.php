<?php
/*
 * Plugin Name: WPGlobus
 * Plugin URI: https://github.com/WPGlobus/WPGlobus
 * Description: WordPress internationalization helper
 * Text Domain: wpglobus
 * Domain Path: /languages/
 * Version: 0.1.0
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * Network: false
 * License: GPL2
 *
	Copyright 2014 WPGlobus -- Alex Gor (alexgff) and Gregory Karpinsky (tivnet)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Force defining debug constants to simplify further code
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', false );
}

global $WPGlobus;
global $WPGlobus_Config;
global $WPGlobus_Options;

require_once 'includes/class-wpglobus-config.php';
require_once 'includes/class-wpglobus-utils.php';
require_once 'includes/class-wpglobus.php';

WPGlobus::$PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );
WPGlobus::$PLUGIN_DIR_URL  = plugin_dir_url( __FILE__ );

$WPGlobus_Config = new WPGlobus_Config();

/**
 * @see WPGlobus::init()
 */
add_action( 'init', 'WPGlobus::init' );

# --- EOF