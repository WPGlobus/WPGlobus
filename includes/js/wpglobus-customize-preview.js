/**
 * WPGlobus Customize Preview
 * Interface JS functions
 *
 * @since 1.2.1
 *
 * @package WPGlobus
 * @subpackage Customize Preview
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusCustomize */
jQuery(document).ready(function ($) {	

	wp.customize( 'wpglobus_blogname', function( value ) {
		value.bind( function( newval ) {
			$( '.site-title a, #site-title a' ).html( newval );
			$( 'a.site-title' ).html( newval ); // https://wordpress.org/themes/customizr/
		} );
	} );
	
	wp.customize( 'wpglobus_blogdescription', function( value ) {
		value.bind( function( newval ) {
			$( '.site-description, #site-description' ).html( newval );
		} );
	} );
	
	setTimeout(function(){
		$( '.site-title a, #site-title a' ).html( WPGlobusCustomize.blogname );
		$( '.site-description, #site-description' ).html( WPGlobusCustomize.blogdescription );
		$( 'a.site-title' ).html( WPGlobusCustomize.blogname ); // https://wordpress.org/themes/customizr/
	}, 500);
});	
