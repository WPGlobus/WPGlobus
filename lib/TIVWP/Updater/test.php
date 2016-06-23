<?php
/**
 * File: test.php
 *
 * @package TIVWP\Updater
 */

$oUpdater = WPGlobus_Core::get_new_updater();

$oUpdater
	->setProductId( 'WPGlobus Plus' )
	->setInstance( 'd6a1bce56b26' )
	->setUrlProduct( 'http://www.wpglobus.com/product/wpglobus-plus/' )
	->setLicenceKey( TIVWP_Updater_TEST_LICENCE_KEY )
	->setEmail( 'support@wpglobus.com' );

//TIVWP_Debug::print_var_html( $oUpdater );

if ( 0 ):
// @example on how to call private methods.
	$method = new ReflectionMethod( 'TIVWP_Updater_Core', 'url_activation' );
	$method->setAccessible( true );
	$url = $method->invoke( $oUpdater );
	TIVWP_Debug::print_var_html( $url );
	echo '<div style="padding-left: 10em"><a href="' . $url . '" target="_blank">Click</a></div>';
endif;

//TIVWP_Debug::print_var_html( $oUpdater->get_status() );

if ( 0 ):
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
	TIVWP_Debug::print_var_html( $oUpdater->activate() );
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
	TIVWP_Debug::print_var_html( $oUpdater->deactivate() );
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
endif;

// Check For Plugin Updates
$transient = 'update_plugins';
add_filter( 'pre_set_site_transient_' . $transient, array( $oUpdater, 'filter__pre_set_site_transient_update_plugins' ) );

// DO NOT RUN. FORCING TRANSIENTS - UNKNOWN BEHAVIOR.
if ( 0 ):
	$current = get_site_transient( $transient );
//TIVWP_Debug::print_var_html( $current );
	set_site_transient( $transient, $current );
endif;

// Check For Plugin Information to display on the update details page
//add_filter( 'plugins_api', array( $this, 'request' ), 10, 3 );


/* EOF */
