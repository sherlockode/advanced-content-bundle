import Slide from "./slide.js";
import jQuery from "jquery";
import './slug.js';

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

    function ajaxFailCallback(jqXhr) {
        alert('An error occurred.');
    }

    $('body').on('click', '.acb-remove-row', function (e) {
        let group = $(this).closest('.acb-field-values-container .acb-sortable-group');
        let fieldRow = $(this).closest('.acb-row');
        fieldRow.remove();
        calculatePosition();
        if (group.length) {
            updateAddButtons(group);
        }
    });

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

    $('body').on('click', '.acb-duplicate-row', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let oldRow = usedAddFieldBlock = $(this).closest('.acb-row');
        let formData = new FormData();
        for (const [key, value] of Object.entries(extractRowData(oldRow))) {
            formData.append(key, value);
        }

        submitNewRow(
            formData,
            oldRow.data('duplicate-url'),
            'POST',
            oldRow.closest('.acb-field-values-container').find('.btn-new-field').data('base-name'),
            false
        );
    });

    function updateAddButtons(group) {
        let lastElementType = null;
        group.children().each(function () {
            if ($(this).hasClass('acb-add-field-container')) {
                if (lastElementType === 'add') {
                    $(this).remove();
                }
                lastElementType = 'add';
            } else if ($(this).hasClass('acb-field')) {
                if (lastElementType === 'field' || lastElementType === null) {
                    $(this).before(group.find('.acb-add-field-container').first().clone());
                }
                lastElementType = 'field';
            }
        });
        if (lastElementType) {
            group.append(group.find('.acb-add-field-container').first().clone());
        }
    }

    $('body').on('sortstop', '.acb-field-values-container .acb-sortable-group', function () {
        updateAddButtons($(this));
    });

    ////////////////
    // Standalone //
    ////////////////

    let slide = new Slide();
    slide.element.on('click', '.form-button', function () {
        $($(this).data('target')).submit();
    });

    let usedAddFieldBlock = null;

    $('body').on('click', '.acb-add-field-container .btn-new-field', function () {
        let baseName = $(this).data('base-name');
        slide.empty();
        usedAddFieldBlock = $(this).closest('.acb-add-field-container');

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

        $.ajax({
            url: url + '?edit=1&type='+type,
            data: extractRowData(row),
            type: 'POST'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            slide.setFooter(data.footer);
            slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveFieldData(this, row.data('name'), row);
            });
            initSortables(slide.content);
        });
    }

    function extractRowData(row) {
        let data = {};
        row.find('input, textarea, select').each(function () {
            if (['radio', 'checkbox'].includes($(this).attr('type')) && !$(this).is(':checked')) {
                return;
            }
            data[this.name.replace(row.data('name'), '__field_name__')] = $(this).val();
        });

        return data;
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
            if (data.success) {
                let preview = data.preview.replace(/__field_name__/g, row.data('name')).replace(/field_name__/g, row.data('form-id'));
                preview = $(preview);
                preview.data('form-index', row.data('form-index'));

                row.replaceWith(preview);
                calculatePosition();

                slide.close();
            } else {
                slide.setContent(data.content);
                slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                    e.preventDefault();
                    saveFieldData(this, row.data('name'), row);
                });
            }
        });
    }

    // convert slide form to content preview
    function saveNewFieldData(form, baseName) {
        updateCKEditorElement(form);
        submitNewRow(new FormData(form), form.action, form.method, baseName, true);
    }

    function submitNewRow(formData, action, method, baseName, isSlideOpened) {
        $.ajax({
            url: action,
            data: formData,
            processData: false,
            contentType: false,
            type: method
        }).done(function (data) {
            if (data.success) {
                let preview = data.preview;
                let name = baseName + '[__name__]';
                preview = preview.replace(/__field_name__/g, name)
                    .replace(
                        /field_name__/g,
                        name.replace(/(\]\[)/g, '_')
                            .replace(/[\[\]]/g, '_')
                            .replace(/_{3}$/g, '__')
                    ); // replace placeholder in HTML "id"

                let container = $('.acb-field-values-container').children('.acb-sortable-group');
                let form = container.closest('form');
                let counter = container.data('widget-counter') || form.find('.acb-field-values-container > .acb-sortable-group').first().children('.acb-row').length;
                preview = preview.replace(/__name__/g, counter++);
                container.data('widget-counter', counter);

                if (usedAddFieldBlock) {
                    usedAddFieldBlock.after(preview);
                } else {
                    container.append(preview);
                }
                updateAddButtons(container);
                calculatePosition();

                if (isSlideOpened) {
                    slide.close();
                }
            } else {
                if (isSlideOpened) {
                    slide.setContent(data.content);
                    slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                        e.preventDefault();
                        saveNewFieldData(this, baseName);
                    });
                }
            }
        });
    }
});
