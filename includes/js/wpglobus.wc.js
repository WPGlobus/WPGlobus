/*jslint browser: true*/
/*global jQuery, console, WPGlobusWC */
jQuery(document).ready(function ($) {

	wpglobus_wc = function() {
		if ( typeof(WPGlobusWC) !== 'undefined' ) {
			console.dir(WPGlobusWC);
		}
	}
	wpglobus_wc();
});