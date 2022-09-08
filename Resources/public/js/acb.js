(function() {
'use strict';
let jQuery;
let getSlug;
if (typeof module === "object" && module.exports) {
    jQuery = require("jquery");
    getSlug = require("speakingurl");
} else {
    jQuery = window.jQuery;
    getSlug = window.getSlug;
}

jQuery(function ($) {
    'use strict';

    ////////////
    // Common //
    ////////////

    updateChoiceList();
    hideEmptyOptionsRow();
    hideEmptyLayoutRow();
    initSortables();

    function initSortables() {
        if (typeof $(this).sortable !== 'undefined') {
            $(".acb-sortable-group").each(function () {
                let ckeditorConfigs = {};
                $(this).sortable({
                    containment: "parent",
                    items: '.acb-sortable[data-sortable-parent-group-id="' + $(this).data('sortable-group-id') + '"]',
                    cursor: "move",
                    axis: "y",
                    update: function (event, ui) {
                        calculatePosition();
                    },
                    start: function (event, ui) {
                        if (typeof CKEDITOR === 'undefined') {
                            return;
                        }
                        // look for ckeditor instances in order to be able to rebuild them after drag
                        ui.item.find('textarea').each(function () {
                            if (typeof CKEDITOR.instances[this.id] === 'undefined') {
                                return;
                            }
                            ckeditorConfigs[this.id] = CKEDITOR.instances[this.id].config;
                            CKEDITOR.instances[this.id].destroy();
                        })
                    },
                    stop: function (event, ui) {
                        // rebuild destroyed ckeditor instances
                        if (typeof CKEDITOR !== 'undefined') {
                            return;
                        }
                        for (let id of Object.keys(ckeditorConfigs)) {
                            CKEDITOR.replace(id, ckeditorConfigs[id]);
                            delete ckeditorConfigs[id];
                        }
                    }
                });
            });
        } else {
            $('.element-position').show();
        }
    }

    function calculatePosition() {
        $(".acb-sortable-group").each(function(){
            let sortables = $(this).children('.acb-sortable');
            for (var i=0; i < sortables.length; i++) {
                let newPosition = i+1;
                $(sortables[i]).find('[name$="[position]"]').first().val(newPosition);
                $(sortables[i]).find('.panel-position').first().html(newPosition);
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

    function ajaxFailCallback(jqXhr) {
        alert('An error occurred.');
    }

    $('body').on('click', '.acb-remove-row', function (e) {
        var fieldRow = $(this).closest('.acb-row');
        fieldRow.remove();
        calculatePosition();
    });

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

    $('body').on('click', '.acb-add-collection-item', function (e) {
        e.preventDefault();

        var list = $($(this).attr('data-list'));
        var counter = list.data('widget-counter') || list.children().length;

        var type = $(this).data('type');
        var newWidget = list.data('prototype');

        if (type === 'field') {
            newWidget = newWidget.replace(/__parent_group_id__/g, list.data('sortable-group-id'));
            newWidget = newWidget.replace(/__name__label__/g, counter);
            newWidget = newWidget.replace(/__name__/g, counter);
            newWidget = newWidget.replace(/__random_id__/g, (Math.random() * 1000)|0);
        } else if (type === 'group') {
            newWidget = newWidget.replace(/__group_name__label__/g, counter);
            newWidget = newWidget.replace(/__group_name__/g, counter);
            newWidget = newWidget.replace(/__group_random_id__/g, (Math.random() * 1000)|0);
        }
        counter++;
        list.data('widget-counter', counter);
        var newElem = $(newWidget);
        newElem.appendTo(list);
        newElem.find('.edit-field').click();
        hideEmptyOptionsRow();
        hideEmptyLayoutRow();
        initSortables();
        calculatePosition();
    });
    $('body').on('click', '.acb-add-flexible-item', function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.acb-flexible-add-wrapper');
        var list = $(wrapper.attr('data-list'));
        var counter = list.data('widget-counter');
        var name = wrapper.attr('data-name-prefix') + '[' + counter + ']';
        var id = wrapper.attr('data-id-prefix') + '_' + counter;

        $.get(wrapper.data('url'), {
            contentTypeId: wrapper.data('content-type'),
            layoutId: $(this).data('layout'),
            parentFormId: wrapper.data('form-id')
        }, function (response) {
            var newWidget = response;
            newWidget = newWidget.replace(/__flexible_name__/g, name);
            newWidget = newWidget.replace(/flexible_name__/g, id);
            counter++;
            list.data('widget-counter', counter);
            var newElem = $(newWidget);
            newElem.appendTo(list);
            initSortables();
            calculatePosition();
        });
    });

    //////////////////
    // Content Type //
    //////////////////

    let contentTypeName = $('.acb-contenttype-name');
    let contentTypeSlug = $('.acb-contenttype-slug');
    if (contentTypeName.length > 0 && contentTypeSlug.length > 0) {
        applySlug(contentTypeName, contentTypeSlug);
    }
    
    $('body').on('change', '.field-type', function (e) {
        var data = {
            type: $(this).val(),
            formPath: $(this).data('form-path')
        };
        $.ajax({
            url: $(this).closest('.edit-content-type').data('change-type-url'),
            type: 'POST',
            data: data,
            context: this
        }).done(function (data) {
            $(this).closest('div.field-row').find('.options').html(data.optionHtml);
            $(this).closest('div.field-row').find('.layout-row').html(data.layoutHtml);
            updateChoiceList();
            hideEmptyOptionsRow();
            hideEmptyLayoutRow();
            initSortables();
            calculatePosition();
        }).fail(ajaxFailCallback);
    });

    function updateChoiceList() {
        $('.choice-list').find('li').each(function() {
            if ($(this).find('.delete-choice').length === 0) {
                addChoiceRemoveLink($(this));
            }
        });
    }
    $('body').on('click', '.add-another-choice', function (e) {
        e.preventDefault();
        var list = $(this).siblings('.choice-list');
        var counter = list.data('widget-counter');
        var newWidget = list.data('prototype');

        counter++;
        newWidget = newWidget.replace(/__name__/g, counter);
        list.data('widget-counter', counter);

        var newElem = $(list.data('widget-tags')).html(newWidget);
        addChoiceRemoveLink(newElem);
        newElem.appendTo(list);
    });
    $('body').on('click', '.delete-choice', function (e) {
        e.preventDefault();
        $(this).closest('.choice-row').remove();
    });

    let contentTypePageTypeList = $('select.acb-contenttype-page-type');
    let contentTypePageList = $('select.acb-contenttype-page');
    let contentTypeAllowSeveralContents = $('input.acb-contenttype-allow-several-contents');
    let contentTypePageTypeValue = contentTypePageTypeList.val();
    let contentTypePageValue = contentTypePageList.val();
    $('body').on('submit', '.edit-content-type', function(e) {
        var validateChoiceLists = true;
        var validateSlugs = true;
        var slugs = {};
        var slug = '';

        $('.acb-error').hide();
        $('.field-error').removeClass('field-error');

        $('.choice-list').each(function() {
            var fieldArea = $(this).closest('.acb-field');
            var isRequired = fieldArea.find('.acb-is-required').val();
            if (isRequired > 0 && $(this).children().length === 0) {
                validateChoiceLists = false;
                fieldArea.find('.acb-collection-error').show();
                fieldArea.addClass('field-error');
                fieldArea.parents('.acb-field').addClass('field-error');
            }
        });

        $('.acb-slug', this).each(function() {
            slug = $(this).val();
            var parent = $(this).data('parent-id');
            if (!slugs[slug]) {
                slugs[slug] = [];
            }
            if (!slugs[slug][parent]) {
                slugs[slug][parent] = [];
            }
            slugs[slug][parent].push($(this));
        });
        for (slug in slugs) {
            for (var parent in slugs[slug]) {
                if (slugs[slug][parent].length > 1) {
                    for (var i = 0; i < slugs[slug][parent].length; i++) {
                        var field = slugs[slug][parent][i];
                        var fieldArea = field.closest('.acb-field');

                        fieldArea.find('.acb-slug-error').show();
                        fieldArea.addClass('field-error');
                        fieldArea.parents('.acb-field').addClass('field-error');
                    }
                    validateSlugs = false;
                }
            }
        }

        if (!validateChoiceLists || !validateSlugs) {
            e.preventDefault();
        }

        if ((contentTypePageTypeValue !== '' && contentTypePageTypeValue !== contentTypePageTypeList.val()) || (contentTypePageValue !== '' && contentTypePageValue !== contentTypePageList.val())) {
            if (!confirm($('.acb-contenttype-change-link').html())) {
                e.preventDefault();
            }
        }
    });

    $('body').on('keyup', '.acb-name, .acb-layout-name', function(){
        $(this).closest('.acb-field').find('.panel-title').first().html($(this).val());
    });
    $('body').on('focus', '.acb-slug', function(){
        if ($(this).val() === '') {
            $(this).val(generateSlug($(this).closest('.acb-field').find('.acb-name').first().val()));
        }
    });

    function addChoiceRemoveLink(choiceLi) {
        choiceLi.append($('.field-options-remove-link').html());
    }

    function hideEmptyOptionsRow() {
        $('.options-row').each(function() {
            $(this).show();
            if ($(this).find('.options > .no-option').length > 0) {
                $(this).hide();
            }
        });
    }

    function hideEmptyLayoutRow() {
        $('.layout-row').each(function() {
            $(this).show();
            if ($(this).children().length == 0) {
                $(this).hide();
            }
        });
    }

    displayContentTypePageList();
    $('.acb-contenttype-link-type').on('change', function(){
        displayContentTypePageList();
    });
    function displayContentTypePageList() {
        let linkType = $('select.acb-contenttype-link-type').val();
        contentTypePageTypeList.closest('.form-group').hide();
        contentTypePageList.closest('.form-group').hide();
        contentTypeAllowSeveralContents.closest('.form-group').hide();

        if (linkType === '0') { // No link
            contentTypePageList.val('');
            contentTypePageTypeList.val('');
            contentTypeAllowSeveralContents.closest('.form-group').show();
        }
        if (linkType === '1') { // Page Type link
            contentTypePageTypeList.closest('.form-group').show();
            contentTypePageList.val('');
        }
        if (linkType === '2') { // Page link
            contentTypePageList.closest('.form-group').show();
            contentTypePageTypeList.val('');
        }
    }

    /////////////
    // Content //
    /////////////

    initContentSlug();
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
    
    function initContentSlug() {
        let contentName = $('.acb-content-name');
        contentName.each(function(){
            let contentSlug = $('.acb-content-slug[data-slug-token="' + $(this).data('slug-token') + '"]');
            if (contentSlug.length > 0) {
                applySlug($(this), contentSlug);
            }
        });
    }

    //////////
    // Page //
    //////////

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

    ////////////
    // Export //
    ////////////

    $('.acb-export-all').on('change', function () {
        let checked = $(this).prop('checked');
        $(this).closest('.acb-export-entities').find('.acb-export-entity input[type="checkbox"]').each(function () {
            $(this).prop('checked', checked);
        });
    });

    ////////////////
    // Standalone //
    ////////////////


    $('.acb-add-field-container').find('.btn-form-part').on('click', function () {
        let form = $(this).closest('form');
        let container = $(this).closest('.acb-field-values-container').children('.acb-sortable-group');
        let counter = form.data('widget-counter') || form.find('.acb-field-values-container > .acb-sortable-group').first().children().length;
        let baseName = $(this).data('base-name');

        $.ajax({
            url: $(this).data('add-field-url'),
            data: {'type': $('.acb-add-field-container').find('select').val()},
            type: 'GET'
        }).done(function (data) {
            let html = data.replace(/__field_name__/g, baseName + '[__name__]');
            html = html.replace(/__name__/g, counter++);
            form.data('widget-counter', counter);
            container.append(html);
            calculatePosition();
        }).fail(ajaxFailCallback);
    });

    let slide = $('<div class="acb-lateral-slide"><button type="button" class="close">x</button><div class="acb-lateral-slide-content"></div></div>');
    slide.find('.close').on('click', function () {
        closeSlide(slide);
    });

    $('.acb-add-field-container').find('.btn-new-field').on('click', function () {
        let baseName = $(this).data('base-name');

        $.ajax({
            url: $(this).data('new-field-url'),
            type: 'GET'
        }).done(function (data) {
            slide.find('.acb-lateral-slide-content').html(data);
            slide.find('.acb-add-field-form').on('submit', function (e) {
                e.preventDefault();
                getNewFieldForm(this.action, $(this).find('input[name=type]:checked').val(), baseName, slide.find('.acb-lateral-slide-content'));
            });

            openSlide(slide);

        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-edit-row', function (e) {
        e.stopPropagation();
        let row = $(this).closest('.acb-field');
        let url = $(this).closest('.acb-field-values-container').data('edit-url');
        getEditFieldForm(url, slide.find('.acb-lateral-slide-content'), row);
        openSlide(slide);
    });

    // get editing form
    function getNewFieldForm(url, type, baseName, container) {
        $.ajax({
            url: url,
            data: {'type': type},
            type: 'GET'
        }).done(function (data) {
            container.html(data);
            container.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveNewFieldData(this, baseName);
            });
        });
    }
    // get editing form
    function getEditFieldForm(url, container, row) {
        let typeInputName = row.data('name') + '[fieldType]';
        typeInputName = typeInputName.replaceAll('[', '\\[').replaceAll(']', '\\]');
        let type = row.find('input[name=' +  typeInputName + ']').val();

        let data = {};
        row.find('input, textarea, select').each(function () {
            if ($(this).attr('type') === 'radio' && !$(this).is(':checked')) {
                return;
            }
            data[this.name.replace(row.data('name'), '__field_name__')] = $(this).val();
        });

        $.ajax({
            url: url + '?edit=1&type='+type,
            data: data,
            type: 'POST'
        }).done(function (data) {
            container.html(data);
            container.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveFieldData(this, row.data('name'), row);
            });
        });
    }

    // convert slide form to content preview
    function saveFieldData(form, name, row) {
        $.ajax({
            url: form.action,
            data: $(form).serialize(),
            type: form.method
        }).done(function (data) {
            let preview = data;
            preview = preview.replace(/__field_name__/g, name).replace(/field_name__/g, name);

            row.replaceWith(preview);
            calculatePosition();

            closeSlide(slide);
        });
    }
    // convert slide form to content preview
    function saveNewFieldData(form, baseName) {
        $.ajax({
            url: form.action,
            data: $(form).serialize(),
            type: form.method
        }).done(function (data) {
            let preview = data;
            let name = baseName + '[__name__]';
            preview = preview.replace(/__field_name__/g, name)
                .replace(/field_name__/g, name.replace(/[\[\]]/g, '_')); // replace placeholder in HTML "id"

            let container = $('.acb-field-values-container').children('.acb-sortable-group');
            let form = container.closest('form');
            let counter = form.data('widget-counter') || form.find('.acb-field-values-container > .acb-sortable-group').first().children().length;
            preview = preview.replace(/__name__/g, counter++);
            form.data('widget-counter', counter);
            container.append(preview);
            calculatePosition();

            closeSlide(slide);
        });
    }

    function openSlide(slide) {
        $('body').append(slide);
        setTimeout(() => $('body').addClass('acb-lateral-slide-open'), 10);
    }

    function closeSlide(slide) {
        $('body').removeClass('acb-lateral-slide-open');
    }
});
})();
