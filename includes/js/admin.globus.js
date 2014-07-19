/*jslint browser: true*/
/*global jQuery, console, aaAdminGlobus */
jQuery(document).ready(function () {
    "use strict";
    window.globusAdminApp = (function (globusAdminApp, $) {

        // var params = JSON.parse(JSON.stringify(parameters));
        /* Object Constructor
         ========================*/
        globusAdminApp.App = function (config) {

            if (window.globusAdminApp !== undefined) {
                return false;
            }

            this.config = {
                debug: true,
                version: aaAdminGlobus.version
            };

            $.extend(this.config, config);

            this.status = 'ok';

            if (typeof aaAdminGlobus === 'undefined') {
                this.status = 'error';
                if (this.config.debug) {
                    console.log('Error options loading');
                }
            } else {
                if (this.config.debug) {
                    console.dir(aaAdminGlobus);
                }
            }

            if ('ok' === this.status) {
                this.init(this.config);
            }
        };

        globusAdminApp.App.prototype = {

            init: function (config) {
                this.start(config);
            },
            start: function (config) {
                $('#wpglobus_flags').select2({
                    formatResult: this.format,
                    formatSelection: this.format,
                    minimumResultsForSearch: -1,
                    escapeMarkup: function (m) {
                        return m;
                    }
                });

                /** disable checked off first language */
                $('body').on('click', '#enabled_languages-list li:first input', function (event) {
                    event.preventDefault();
                    $('.redux-save-warn').css({'display':'none'});
                    $('#enabled_languages-list li:first > input').val('1');
                    if ($('#disable_first_language').length === 0) {
                        $('<div id="disable_first_language" style="display:block;" class="redux-field-errors notice-red"><strong><span></span>'+aaAdminGlobus.i18n.cannot_disable_language+'</strong></div>').insertAfter('#info_bar');
                    }
                    return false;
                });
            },
            format: function (language) {
                return '<img class="wpglobus_flag" src="' + aaAdminGlobus.flag_url + language.text + '"/>&nbsp;&nbsp;' + language.text;
            }
        };

        new globusAdminApp.App();

        return globusAdminApp;

    }(window.globusAdminApp || {}, jQuery));

});
