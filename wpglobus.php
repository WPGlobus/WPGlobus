<?php
/*
 * Plugin Name: WPGlobus
 * Plugin URI: https://github.com/WPGlobus/WPGlobus
 * Description: WordPress internationalization helper
 * Text Domain: wpglobus
 * Domain Path: /languages/
 * Version: 0.2.0
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * Network: false
 * License: GPL2
 *
	Copyright 2014 Alex Gor (alexgff) and Gregory Karpinsky (tivnet)

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
if ( !defined('ABSPATH') ) exit;

include( dirname(__FILE__) . '/includes/wpglobus.config.php' );
global $WPGlobus_Config;
$WPGlobus_Config = new WPGlobus_Config();

include( dirname(__FILE__) . '/includes/functions.php' );

# extract url information
$WPGlobus_Config->url_info = globus_extractURL( $_SERVER['REQUEST_URI'], $_SERVER["HTTP_HOST"], isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '' );
$WPGlobus_Config->language = $WPGlobus_Config->url_info['language'];

require_once 'class-wpglobus.php';
add_action( 'init', 'WPGlobus_init' );

/**
 * Start WPGlobus on "init" hook, so if there is another ReduxFramework, it will be loaded first. Hopefully :-)
 */
function WPGlobus_init() {
	$GLOBALS['WPGlobus'] = new WPGlobus();
}
