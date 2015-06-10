<?php
/**
 * Plugin Name: WPGlobus
 * Plugin URI: https://github.com/WPGlobus/WPGlobus
 * Description: A WordPress Globalization / Multilingual Plugin. Posts, pages, menus, widgets and even custom fields - in multiple languages!
 * Text Domain: wpglobus
 * Domain Path: /languages/
 * Version: 1.1.1.2
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * Network: false
 * License: GPL2
 * Credits: TIV.NET INC, Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 * Copyright 2015 WPGlobus
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGLOBUS_VERSION', '1.1.1.2' );
define( 'WPGLOBUS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/** @todo Get rid of these */
global $WPGlobus;
global $WPGlobus_Options;


require_once 'includes/class-wpglobus-config.php';
require_once 'includes/class-wpglobus-utils.php';
require_once 'includes/class-wpglobus-wp.php';
require_once 'includes/class-wpglobus-widget.php';
require_once 'includes/class-wpglobus.php';

require_once 'includes/class-wpglobus-core.php';

/**
 * Initialize
 */
WPGlobus::$PLUGIN_DIR_PATH = plugin_dir_path( __FILE__ );
WPGlobus::$PLUGIN_DIR_URL  = plugin_dir_url( __FILE__ );
WPGlobus::Config();

require_once 'includes/class-wpglobus-filters.php';
require_once 'includes/wpglobus-controller.php';

if ( defined( 'WPSEO_VERSION' ) ) {
	require_once 'includes/class-wpglobus-wpseo.php';
	WPGlobus_WPSEO::controller();
}

/**
 * Handle special URLs for QA
 * @note CREATES POST, PAGE, CATEGORY and TAG!!! CLEAN AFTER RUNNING!!!
 */
if ( defined( 'WPGLOBUS_QA_ENABLED' ) && ! empty( $_GET['wpglobus'] ) && $_GET['wpglobus'] === 'qa' ) {
	require_once 'includes/class-wpglobus-qa.php';
	add_filter( 'template_include', array( 'WPGlobus_QA', 'filter__template_include' ) );
}

# --- EOF