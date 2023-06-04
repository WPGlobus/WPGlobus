<?php
/**
 * File: wpglobus-customize.php
 *
 * @package WPGlobus\Admin\Customizer
 */

if ( ! defined( 'WPGLOBUS_CUSTOMIZE' ) || WPGLOBUS_CUSTOMIZE ) {
	require_once 'class-wpglobus-customize190.php';
	WPGlobus_Customize::controller();
}
