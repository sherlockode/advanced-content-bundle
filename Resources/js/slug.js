import jQuery from "jquery";
import getSlug from "../public/js/speakingurl.min";

function initContentSlug() {
    let contentName = $('.acb-content-name');
    contentName.each(function(){
        let contentSlug = $('.acb-content-slug[data-slug-token="' + $(this).data('slug-token') + '"]');
        if (contentSlug.length > 0) {
            applySlug($(this), contentSlug);
        }
    });
}

function applySlug(refField, slugField) {
    var timer;
    refField.on('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
            slugField.val(generateSlug(refField.val()));
        }, 300);
    });
}

function generateSlug (value) {
    // use speakingurl lib if available
    if (typeof getSlug === 'function') {
        return getSlug(value);
    }
    // simple custom slug generator for fallback
    // 1) convert to lowercase
    // 2) remove dashes and pluses
    // 3) replace spaces with dashes
    // 4) remove everything but alphanumeric characters and dashes
    return value.toLowerCase().trim().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
}

jQuery(function ($) {
    $('body').on('focus', '.acb-slug', function(){
        if ($(this).val() === '') {
            $(this).val(generateSlug($(this).closest('.acb-field').find('.acb-name').first().val()));
        }
    });

    initContentSlug();
});
