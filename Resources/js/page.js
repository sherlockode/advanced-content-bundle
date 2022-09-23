import jQuery from "jquery";

jQuery(function ($) {
    let pageTypeList = $('select.acb-page-page-type');
    let pageTypeValue = pageTypeList.val();
    $('body').on('submit', '.edit-page', function(e) {
        if (pageTypeValue !== $(this).find('select.acb-page-page-type').val()) {
            if (!confirm($('.acb-page-change-type').html())) {
                e.preventDefault();
            }
        }
    });

    let pageTitle = $('.acb-pagemeta-title');
    if (pageTitle.length > 0) {
        pageTitle.each(function(){
            let pageSlug = $('.acb-pagemeta-slug[data-slug-token="' + $(this).data('slug-token') + '"]');
            if (pageSlug.length > 0) {
                applySlug($(this), pageSlug);
            }
        });
    }

    $('.acb_translations .acb-duplicate-locale-content').on('click', function () {
        $('.acb_translations .acb-duplicate-dropdown button').data('locale', $(this).data('locale'));
    });
    $('.acb_translations .acb-duplicate-dropdown .dropdown-menu').on('click', function (e) {
        // prevent dropdown from closing if clicked inside
        e.stopPropagation();
    });
    $('.acb_translations .acb-duplicate-dropdown button').on('click', function () {
        let button = $(this);

        let data = {
            id: button.closest('.dropdown-menu').find('select').val(),
            locale: button.data('locale')
        };

        $.ajax(button.data('url'), {
            method: 'POST',
            data: data
        }).done(function () {
            window.location.reload();
        });
    });
    $('.acb_translations .acb-delete-locale-content').on('click', function () {
        if (confirm($(this).data('confirm'))) {
            $.ajax($(this).data('url'), {
                method: 'POST',
                data: {id: $(this).data('entityId')},
            }).done(function () {
                window.location.reload();
            });
        }
    });
});
