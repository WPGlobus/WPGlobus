/* global redux_change */
(function($) {
    "use strict";

    $.redux = $.redux || {};

    $(document).ready(function() {
        $.redux.table();
    });

    /**
     * Table
     * Dependencies		: jquery
     */
    $.redux.table = function() {
        var $t_flag = $('.flag-table-wrapper').html(),
            $t_form_table = $('.flag-table-wrapper').parents('table');

        $t_form_table.wrap( '<div style="overflow:hidden;" class="flag-table"></div>' );
        $t_form_table.remove();
        $('.flag-table').html( $t_flag );


        $('#add_language').on('click', function() {
            //console.log('clicked');
            $('.table-dummy').toggleClass('hidden');

        });
        return;

        $(".cb-enable").click(function() {
            if ($(this).hasClass('selected')) {
                return;
            }

            var parent = $(this).parents('.switch-options');

            $('.cb-disable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox-input', parent).val(1);

            redux_change($('.checkbox-input', parent));

            //fold/unfold related options
            var obj     = $(this);
            var $fold   = '.f_' + obj.data('id');

            $($fold).slideDown('normal', "swing");
        });
        $(".cb-disable").click(function() {

            if ($(this).hasClass('selected')) {
                return;
            }

            var parent = $(this).parents('.switch-options');

            $('.cb-enable', parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox-input', parent).val(0);

            redux_change($('.checkbox-input', parent));

            //fold/unfold related options
            var obj     = $(this);
            var $fold   = '.f_' + obj.data('id');

            $($fold).slideUp('normal', "swing");
        });

        $('.cb-enable span, .cb-disable span').find().attr('unselectable', 'on');
    };
})(jQuery);