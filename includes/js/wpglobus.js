/*jslint browser: true*/
/*global jQuery, WPGlobus, wpCookies */
jQuery(document).ready(function ($) {
    "use strict";
    var wpglobus_language_old;
    //noinspection JSLint
    if (typeof WPGlobus !== 'undefined') {

        /**
         * Store previous value of the current language in a cookie,
         * and trigger an event when the language has been changed.
         *
         * Example of binding:
         * (Must run before the `triggerHandler` below)
         * <code>
         *     $(document).on('wpglobus_current_language_changed', function (event, args) {
         *         console.log(args.oldLang);
         *         console.log(args.newLang);
         *     });
         * </code>
         *
         * @since 1.5.5
         */
        wpglobus_language_old = wpCookies.get('wpglobus-language-old');

        if (wpglobus_language_old !== WPGlobus.language) {
            $(document).triggerHandler('wpglobus_current_language_changed', {
                oldLang: wpglobus_language_old,
                newLang: WPGlobus.language
            });
        }

        wpCookies.set('wpglobus-language-old', WPGlobus.language, 31536000, '/');

        wpCookies.set('wpglobus-language', WPGlobus.language, 31536000, '/');

        /**
         * Add hash (`#`) to the menu items when the current URL has it.
         * There is no way to detect it in PHP, because it's not a SERVER variable
         * and is known to the browser only.
         */
        if (window.location.hash) {
            //noinspection JSDeclarationsAtScopeStart,JSLint
            var hash = window.location.hash;
            $('.wpglobus-selector-link, .wpglobus-selector-link a').each(function () {
                if (typeof this.value === 'string') {
                    this.value += hash;
                }
                if (typeof this.href === 'string') {
                    this.href += hash;
                }
            });
        }

    }

});
