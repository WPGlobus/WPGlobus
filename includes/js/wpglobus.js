/*jslint browser: true*/
/*global jQuery, console, WPGlobus, wpCookies */
jQuery(document).ready(function () {
    "use strict";
    if (typeof WPGlobus !== 'undefined') {
        wpCookies.set('wpglobus-language', WPGlobus.language, 31536000, '/');
    }
});