/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor, wpseoMetaboxL10n, yst_updateSnippet */
"use strict";
var wpglobus_wpseo = function () {
	if (typeof wpseoMetaboxL10n === "undefined") {
		return;
	}
	yst_updateSnippet();
};
