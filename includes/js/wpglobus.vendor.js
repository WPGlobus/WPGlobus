/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor, wpseoMetaboxL10n, yst_updateSnippet */
jQuery(document).ready(function () {
    "use strict";

    wpglobus_wpseo = function () {
        if (typeof wpseoMetaboxL10n === "undefined") {
            return;
        }
        yst_updateSnippet();
    };

});