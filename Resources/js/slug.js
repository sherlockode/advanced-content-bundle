import jQuery from "jquery";
import getSlug from "../public/js/speakingurl.min";

function initContentSlug() {
    let contentName = jQuery('.acb-content-name');
    contentName.each(function(){
        let contentSlug = jQuery('.acb-content-slug[data-slug-token="' + jQuery(this).data('slug-token') + '"]');
        if (contentSlug.length > 0) {
            applySlug(jQuery(this), contentSlug);
        }
    });
}
function initPageSlug() {
    let pageTitle = jQuery('.acb-pagemeta-title');
    if (pageTitle.length > 0) {
        pageTitle.each(function(){
            let pageSlug = jQuery('.acb-pagemeta-slug[data-slug-token="' + jQuery(this).data('slug-token') + '"]');
            if (pageSlug.length > 0) {
                applySlug(jQuery(this), pageSlug);
            }
        });
    }
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
    // 2) replace pluses, quotes and spaces with dashes
    // 3) replace consecutive dashes with a single one
    // 4) remove trailing dashes
    // 5) replace special chars with their slug friendly equivalent
    // 6) remove everything but alphanumeric characters and dashes
    return value
      .toLowerCase().trim()
      .replace(/[+'"\s]/g, '-')
      .replace(/-{2,}/g, '-')
      .replace(/^-/g, '')
      .replace(/-$/g, '')

      .replace(/[àáâãäå]/g, 'a')
      .replace(/æ/g, 'ae')
      .replace(/ç/g, 'c')
      .replace(/[èéêë]/g, 'e')
      .replace(/[ìíîï]/g, 'i')
      .replace(/ñ/g, 'n')
      .replace(/[òóôõö]/g, 'o')
      .replace(/œ/g, 'oe')
      .replace(/ß/g, 'ss')
      .replace(/[ùúûüµ]/g, 'u')
      .replace(/[ýÿ]/g, 'y')

      .replace(/[^a-z0-9-]/g, '')
    ;
}

jQuery(function ($) {
    $('body').on('focus', '.acb-slug', function(){
        if ($(this).val() === '') {
            $(this).val(generateSlug($(this).closest('.acb-field').find('.acb-name').first().val()));
        }
    });

    initContentSlug();
    initPageSlug();
});
