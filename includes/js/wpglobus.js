/*jslint browser: true*/
/*global jQuery, console, WPGlobus */
jQuery(document).ready(function () {
	if ( typeof WPGlobus !== 'undefined' ) {
		wpCookies.set('wpglobus-language', WPGlobus.language, 31536000, '/');
	}	
});