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

if ( 1 ):
// @example on how to call private methods.
	$method = new ReflectionMethod( 'TIVWP_Updater_Core', 'url_activation' );
	$method->setAccessible( true );
	$url = $method->invoke( $oUpdater );
	TIVWP_Debug::print_var_html( $url );
	echo '<div style="padding-left: 10em"><a href="' . $url . '" target="_blank">Click</a></div>';
endif;

if ( 0 ):
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
	TIVWP_Debug::print_var_html( $oUpdater->activate() );
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
	TIVWP_Debug::print_var_html( $oUpdater->deactivate() );
	TIVWP_Debug::print_var_html( $oUpdater->get_status() );
endif;


/* EOF */
