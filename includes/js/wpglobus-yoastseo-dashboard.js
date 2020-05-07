/**
 * WPGlobus for YoastSeo Dashboard v.14
 * Interface JS functions
 *
 * @since 2.4.5
 *
 * @package WPGlobus
 */
/*jslint browser: true*/
/*global jQuery, console*/

jQuery(document).ready(function($){
	'use strict';

	if ( 'undefined' === typeof WPGlobusYoastSeoDashboard ) {
		return;
	}

	var api = {
		messageBox: '<div style="text-align:center;font-weight:bold;background-color:#d3e4f4;color:#000;padding:10px 0;">{{warning}}</div>',
		init: function() {
			var parent = $('.wpseo_table_page');
			if ( parent.length == 1 ) {
				var wrng = WPGlobusYoastSeoDashboard.i18n.preWarning+WPGlobusYoastSeoDashboard.i18n.bulkEditorWarning;
				var box = api.messageBox.replace( '{{warning}}', wrng );
				parent.before(box);
			}
		}
	}
	WPGlobusYoastSeoDashboard = $.extend({}, WPGlobusYoastSeoDashboard, api);	
	WPGlobusYoastSeoDashboard.init();		
});