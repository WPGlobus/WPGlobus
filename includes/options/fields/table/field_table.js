/* global redux_change */
(function ($) {
    "use strict";

    $.redux = $.redux || {};

    $(document).ready(function () {
        $.redux.table();
    });

    /**
     * Table
     * Dependencies: jquery
     */
    $.redux.table = function () {
        var $t_flag = $('.flag-table-wrapper').html(),
            $t_form_table = $('.flag-table-wrapper').parents('table');

        $t_form_table.wrap('<div style="overflow:hidden;" class="flag-table"></div>');
        $t_form_table.remove();
        $('.flag-table').html($t_flag);

    };
})(jQuery);