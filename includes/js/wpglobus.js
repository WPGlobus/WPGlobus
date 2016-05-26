/*jslint browser: true*/
/*global jQuery, console, WPGlobus, wpCookies */
jQuery(document).ready(function ($) {
    "use strict";
    if (typeof WPGlobus !== 'undefined') {

		/**
		 * Example of binding
		 * Must be running earlier than triggerHandler
		 * @todo remove in production
		 */
		$(document).on( 'wpglobus_current_language_changed', function(event, args){
			console.log( args.oldLang );
			console.log( args.newLang );
		});	
	
        /**
         * Store previous value of the current language in a cookie,
         * and trigger an event when the language has been changed.
         *
         * @since 1.5.5
         */
        var wpglobus_language_old = wpCookies.get('wpglobus-language-old');
		
        if ( wpglobus_language_old !== WPGlobus.language ) {
            $( document ).triggerHandler( 'wpglobus_current_language_changed', {oldLang:wpglobus_language_old, newLang:WPGlobus.language} );
        }
		
        wpCookies.set('wpglobus-language-old', WPGlobus.language, 31536000, '/');

        wpCookies.set('wpglobus-language', WPGlobus.language, 31536000, '/');

        if (window.location.hash) {
            var hash = window.location.hash;
            $('.wpglobus-selector-link, .wpglobus-selector-link a').each(function () {
                if (typeof this.value !== 'undefined') {
                    this.value = this.value + hash;
                }
                if (typeof this.href !== 'undefined') {
                    this.href = this.href + hash;
                }
            });
        }
		

		
    }
	
});
