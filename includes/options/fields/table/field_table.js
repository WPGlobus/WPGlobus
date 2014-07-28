/*jslint browser: true*/
/*global redux_change, jQuery */
(function ($) {
    "use strict";

    $.redux = $.redux || {};

    $(document).ready(function () {
        $.redux.table();
    });

    /**
     * Table
     */
    $.redux.table = function () {
        var $t_flag = $('.wpglobus_flag_table_wrapper').html(),
            $t_form_table = $('.wpglobus_flag_table_wrapper').parents('table');

        $t_form_table.wrap('<div style="overflow:hidden;" class="wpglobus_flag_table"><' + '/div>');
        $t_form_table.remove();
        $('.wpglobus_flag_table').html($t_flag);

    };
}(jQuery));