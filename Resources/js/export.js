import jQuery from "jquery";

jQuery(function ($) {
    $('.acb-export-all').on('change', function () {
        let checked = $(this).prop('checked');
        $(this).closest('.acb-export-entities').find('.acb-export-entity input[type="checkbox"]').each(function () {
            $(this).prop('checked', checked);
        });
    });
});

