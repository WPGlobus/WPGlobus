/*jslint browser: true*/
/*global jQuery, console */
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
                version: '0.0'
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
                $('#flags').select2({
                    formatResult: this.format,
                    formatSelection: this.format,
                    minimumResultsForSearch: -1,
                    escapeMarkup: function (m) {
                        return m;
                    }
                });
            },
            format: function (language) {
                return '<img class="flag" src="' + aaAdminGlobus.flag_url + language.text + '"/>&nbsp;&nbsp;' + language.text;
            }
        };

        /**
         * @todo http://jslinterrors.com/do-not-use-new-for-side-effects
         */
        new globusAdminApp.App();

        return globusAdminApp;

    }(window.globusAdminApp || {}, jQuery));

});
