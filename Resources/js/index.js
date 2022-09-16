import Slide from "./slide.js";
import jQuery from "jquery";
import getSlug from "../public/js/speakingurl.min.js";

jQuery(function ($) {
    ////////////
    // Common //
    ////////////

    initSortables();

    function initSortables(parent) {
        if (typeof $(this).sortable !== 'undefined') {
            let groups;
            if (parent) {
                groups = $(parent).find('.acb-sortable-group');
            } else {
                groups = $('.acb-sortable-group');
            }
            groups.each(function () {
                let ckeditorConfigs = {};
                $(this).sortable({
                    containment: "parent",
                    items: '> .acb-sortable',
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
        initSortables();
        calculatePosition();
    });

    $('body').on('focus', '.acb-slug', function(){
        if ($(this).val() === '') {
            $(this).val(generateSlug($(this).closest('.acb-field').find('.acb-name').first().val()));
        }
    });

    /////////////
    // Content //
    /////////////

    initContentSlug();

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

    let slide = new Slide();
    slide.element.on('click', '.form-button', function () {
        $($(this).data('target')).submit();
    });

    $('.acb-add-field-container').find('.btn-new-field').on('click', function () {
        let baseName = $(this).data('base-name');
        slide.empty();

        $.ajax({
            url: $(this).data('new-field-url'),
            type: 'GET'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            let form = slide.content.find('.acb-add-field-form');
            form.on('submit', function (e) {
                e.preventDefault();
                getNewFieldForm(this.action, $(this).find('input[name=type]:checked').val(), baseName);
            });
            form.find('input').on('change', function () {
                form.submit();
            });

            slide.open();
        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-edit-row', function (e) {
        e.stopPropagation();
        let row = $(this).closest('.acb-field');
        let url = $(this).closest('.acb-field-values-container').data('edit-url');
        getEditFieldForm(url, row);
        slide.open();
    });

    function updateCKEditorElement(form)
    {
        if (typeof CKEDITOR === 'undefined') {
            return;
        }
        // look for ckeditor instances in order to update the value
        $(form).find('textarea').each(function () {
            if (typeof CKEDITOR.instances[this.id] === 'undefined') {
                return;
            }
            CKEDITOR.instances[this.id].updateElement();
        });
    }

    // get editing form
    function getNewFieldForm(url, type, baseName) {
        slide.empty();
        $.ajax({
            url: url,
            data: {'type': type},
            type: 'GET'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            slide.setFooter(data.footer);
            slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveNewFieldData(this, baseName);
            });
            initSortables(slide.content);
        });
    }
    // get editing form
    function getEditFieldForm(url, row) {
        slide.empty();
        let typeInputName = row.data('name') + '[fieldType]';
        typeInputName = typeInputName.replaceAll('[', '\\[').replaceAll(']', '\\]');
        let type = row.find('input[name=' +  typeInputName + ']').val();

        let data = {};
        row.find('input, textarea, select').each(function () {
            if (['radio', 'checkbox'].includes($(this).attr('type')) && !$(this).is(':checked')) {
                return;
            }
            data[this.name.replace(row.data('name'), '__field_name__')] = $(this).val();
        });

        $.ajax({
            url: url + '?edit=1&type='+type,
            data: data,
            type: 'POST'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            slide.setFooter(data.footer);
            slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveFieldData(this, row.data('name'), row);
            });
            initSortables();
        });
    }

    // convert slide form to content preview
    function saveFieldData(form, name, row) {
        updateCKEditorElement(form);
        $.ajax({
            url: form.action,
            data: new FormData(form),
            processData: false,
            contentType: false,
            type: form.method
        }).done(function (data) {
            let preview = data;
            preview = preview.replace(/__field_name__/g, name).replace(/field_name__/g, name);

            row.replaceWith(preview);
            calculatePosition();

            slide.close();
        });
    }

    // convert slide form to content preview
    function saveNewFieldData(form, baseName) {
        updateCKEditorElement(form);
        $.ajax({
            url: form.action,
            data: new FormData(form),
            processData: false,
            contentType: false,
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

            slide.close();
        });
    }
});
