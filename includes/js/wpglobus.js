/*jslint browser: true*/
/*global jQuery, console, WPGlobus, wpCookies */
jQuery(document).ready(function ($) {
    "use strict";
    if (typeof WPGlobus !== 'undefined') {
        wpCookies.set('wpglobus-language', WPGlobus.language, 31536000, '/');
		if(window.location.hash) {
			var hash = window.location.hash;
			$('.wpglobus-selector-link, .wpglobus-selector-link a').each(function() {
				if ( typeof this.value !== 'undefined' ) {
					this.value = this.value + hash;
				}
				if ( typeof this.href !== 'undefined' ) {
					this.href = this.href + hash;
				}
			});
		}		
    }
});