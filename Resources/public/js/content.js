jQuery(function ($) {
    'use strict';

    initDatePicker();

    function initDatePicker() {
        $('body').find('.acb-date').each(function() {
            var format = 'DD/MM/YYYY';
            if ($(this).hasClass('datetimepicker')) {
                format = format + ' HH:mm:ss';
            }
            $(this).datetimepicker({
                format: format
            });
        });
    }
});
