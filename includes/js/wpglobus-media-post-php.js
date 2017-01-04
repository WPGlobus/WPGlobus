/**
 * WPGlobus Media Administration.
 * Interface JS functions
 *
 * @since 1.7.3
 *
 * @package WPGlobus
 * @subpackage Media
 */
/*jslint browser: true*/
/*global jQuery, console, WPGlobusAdmin*/
(function ($) {
    "use strict";

    $(document).on('wpglobus_after_post_edit', function (evnt) {

        $(document).ajaxSend(function (event, request, settings) {
            if ('undefined' === typeof settings.data) {
                return;
            }

            if ('undefined' === typeof WPGlobusAdmin) {
                return;
            }

            if (-1 != settings.data.indexOf('action=send-attachment-to-editor')) {
                settings.data = settings.data + '&wpglobusLanguageTab=' + WPGlobusAdmin.currentTab;
            }
        });

    });

})(jQuery);
