/**
 * WPGlobus Media Administration.
 * Interface JS functions
 *
 * @since 1.7.3
 * @since 2.2.22
 *
 * @package WPGlobus
 * @subpackage Media
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusAdmin, WPGlobusMediaInPost*/
(function ($) {
    "use strict";

	if ( 'undefined' === typeof WPGlobusMediaInPost ) {
		return;
	}	
	
	var api = {
		init: function() {
			api.addListeners();
		},
		addListeners: function() {
			$(document).on('wpglobus_after_post_edit', function (evnt) {

				$(document).ajaxSend(function (event, request, settings) {
					if ('undefined' === typeof settings.data) {
						return;
					}

					if ('undefined' === typeof WPGlobusAdmin) {
						return;
					}

					if ( WPGlobusAdmin.builder && 'gutenberg' == WPGlobusAdmin.builder.id ) {
						if (-1 != settings.data.indexOf('action=query-attachments')) {
							settings.data = settings.data + '&wpglobusLanguageTab=' + WPGlobusAdmin.currentTab;
							settings.data = settings.data + '&wpglobusPrepareAttachments=true';
						}
					} else {
						if (-1 != settings.data.indexOf('action=send-attachment-to-editor')) {
							settings.data = settings.data + '&wpglobusLanguageTab=' + WPGlobusAdmin.currentTab;
						}
					}
				});

			});
		}
	}
	
	WPGlobusMediaInPost = $.extend({}, WPGlobusMediaInPost, api);
	WPGlobusMediaInPost.init();

})(jQuery);
