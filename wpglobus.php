<?php
//@formatter:off
/**
 * Plugin Name: WPGlobus
 * Plugin URI: https://github.com/WPGlobus/WPGlobus
 * Description: A WordPress Globalization / Multilingual Plugin. Posts, pages, menus, widgets and even custom fields - in multiple languages!
 * Text Domain: wpglobus
 * Domain Path: /languages/
 * Version: 1.5.3
 * Author: WPGlobus
 * Author URI: http://www.wpglobus.com/
 * Network: false
 * License: GPL2
 * Credits: TIV.NET INC, Alex Gor (alexgff) and Gregory Karpinsky (tivnet)
 * Copyright 2015-2016 WPGlobus
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
//@formatter:on
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPGLOBUS_VERSION', '1.5.3' );
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

/**
 * Support for Yoast SEO
 */
require_once 'includes/wpglobus-yoastseo.php';

/**
 * Support of theme option panels and customizer
 * @since 1.4.0
 */
require_once 'includes/class-wpglobus-customize140.php';
WPGlobus_Customize::controller();

/**
 * WPGlobus customize options
 * @since 1.4.6
 */
require_once 'includes/admin/class-wpglobus-customize-options.php';
WPGlobus_Customize_Options::controller();
	
require_once 'updater/class-wpglobus-updater.php';

# --- EOF
