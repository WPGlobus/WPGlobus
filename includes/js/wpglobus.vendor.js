/*jslint browser: true*/
/*global jQuery, console, WPGlobusVendor */
jQuery(document).ready(function () {

	wpglobus_wpseo = function() {
		if (typeof wpseoMetaboxL10n === "undefined") {
			return;
		}	
		yst_updateSnippet();
	}

});