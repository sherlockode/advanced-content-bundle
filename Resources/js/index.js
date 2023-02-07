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
                let connectWithSelector = false;
                let handleSelector = '.sortable-handle';
                if ($(this).hasClass('acb-row-sortable-group')) {
                    // If you are in a row,
                    // then you are a column,
                    // so you can be moved in any other row
                    connectWithSelector = '.acb-row-sortable-group';
                } else if ($(this).hasClass('acb-column-sortable-group')) {
                    // If you are in a column,
                    // then you are a field or a row,
                    // so you can be moved to a column or to root container
                    connectWithSelector = '.acb-root-sortable-group, .acb-column-sortable-group';
                } else if ($(this).closest('.acb-lateral-slide').length === 0) {
                    // Otherwise you are in root container,
                    // then you are a field or a row,
                    // so you can be moved in any column
                    connectWithSelector = '.acb-column-sortable-group';
                } else {
                    handleSelector = false;
                }

                let ckeditorConfigs = {};
                $(this).sortable({
                    connectWith: connectWithSelector,
                    items: '> .acb-sortable',
                    cursor: "move",
                    placeholder: 'acb-sortable-drop-zone',
                    handle: handleSelector,
                    update: function (event, ui) {
                        calculatePosition();
                    },
                    start: function (event, ui) {
                        $('body').addClass('acb-sorting');
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
                        $('body').removeClass('acb-sorting');
                        // rebuild destroyed ckeditor instances
                        if (typeof CKEDITOR !== 'undefined') {
                            return;
                        }
                        for (let id of Object.keys(ckeditorConfigs)) {
                            CKEDITOR.replace(id, ckeditorConfigs[id]);
                            delete ckeditorConfigs[id];
                        }
                    },
                    receive: function (event, ui) {
                        // ui.item => moved element
                        // ui.sender => source group
                        // event.target => target group

                        let previewRow = $(ui.item).closest('.acb-row');
                        let formRow = $('.acb-element-form[data-name="' + previewRow.data('name') + '"]');
                        let formData = new FormData();
                        for (const [key, value] of Object.entries(extractRowData(formRow))) {
                            formData.append(key, value);
                        }

                        moveElementToList(
                            formData,
                            $(ui.item).find('.acb-element-toolbar').data('duplicate-url'),
                            'POST',
                            $(event.target).data('base-name'),
                            previewRow.data('name')
                        );

                        toggleColumnPlaceholderButton($(ui.sender), true);
                    },
                    out: function (event, ui) {
                        toggleColumnPlaceholderButton($(event.target), true);
                    },
                    over: function (event, ui) {
                        toggleColumnPlaceholderButton($(event.target), false);
                    }
                });
            });
        } else {
            $('.element-position').show();
        }
    }

    function toggleColumnPlaceholderButton(column, show) {
        if (column.hasClass('acb-column-sortable-group')) {
            if (column.find('.acb-sortable').length === 0) {
                if (show) {
                    column.find('.btn-append-field').show();
                } else {
                    column.find('.btn-append-field').hide();
                }
            }
        }
    }

    function calculatePosition() {
        $(".acb-sortable-group").each(function(){
            let sortables = $(this).children('.acb-sortable');
            for (var i=0; i < sortables.length; i++) {
                let newPosition = i+1;
                if ($(sortables[i]).closest('.acb-lateral-slide').length === 0) {
                    $('[name="' + $(sortables[i]).data('name') + '[position]"]').first().val(newPosition);
                } else {
                    $(sortables[i]).find('[name$="[position]"]').first().val(newPosition);
                    $(sortables[i]).find('.panel-position').first().html(newPosition);
                }
            }
            if ($(this).closest('.acb-field').hasClass('acb-layout-column')) {
                if (sortables.length === 0) {
                    $(this).closest('.acb-field').addClass('acb-empty-column');
                } else {
                    $(this).closest('.acb-field').removeClass('acb-empty-column');
                }
            }
        });
    }

    function ajaxFailCallback(jqXhr) {
        alert('An error occurred.');
    }

    $('body').on('click', '.acb-collapse-row', function() {
        $(this).closest('.acb-layout-row').toggleClass('collapsed');
        $(this).find('i').toggleClass('fa-caret-down').toggleClass('fa-caret-up');
    });

    $('body').on('click', '.acb-remove-row', function (e) {
        let group = $(this).closest('.acb-elements-container .acb-sortable-group');
        let fieldRow = $(this).closest('.acb-row');
        $('.acb-element-form[data-name="' + fieldRow.data('name') + '"]').remove();
        fieldRow.remove();
        calculatePosition();
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

        usedContainer = null;
        let previewRow = usedAddFieldBlock = $(this).closest('.acb-row');
        let formRow = $('.acb-element-form[data-name="' + previewRow.data('name') + '"]');
        let formData = new FormData();
        for (const [key, value] of Object.entries(extractRowData(formRow))) {
            formData.append(key, value);
        }

        submitNewRow(
            formData,
            $(this).closest('.acb-element-toolbar').data('duplicate-url'),
            'POST',
            previewRow.closest('.acb-sortable').parents('.acb-sortable-group').data('base-name'),
            false,
            true
        );
    });

    $('body').on('click', '.btn-append-layout', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        usedAddFieldBlock = null;
        usedContainer = null;
        let formData = new FormData();
        let elements = [];
        for (let i=0; i < parseInt($(this).data('col-num')); i++) {
            elements.push({
                'elementType': 'column',
                'config': {
                    'size': Math.round(12 / Math.max(1, parseInt($(this).data('col-num'))))
                }
            });
        }
        let data = {
            'elementType': 'row',
            'elements': elements
        };
        let layoutData = buildCustomLayoutFormData(formData, data, '__field_name__');

        submitNewRow(
            layoutData,
            $('.acb-elements-container').data('edit-url') + '?type=row',
            'POST',
            $(this).closest('.acb-add-field-container').data('base-name'),
            false,
            true
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

    $('body').on('sortstop', '.acb-elements-container .acb-sortable-group', function () {
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
    let usedContainer = null;

    $('body').on('click', '.btn-new-field', function () {
        let baseName = $(this).parents('.acb-sortable').parents('.acb-sortable-group').data('base-name');
        usedAddFieldBlock = $(this).closest('.acb-row');
        usedContainer = null;
        let addAfter = !$(this).hasClass('btn-new-field-before');
        usedContainer = null;

        if (usedAddFieldBlock.hasClass('acb-layout-column')) {
            // We want to add a new field before or after a column
            // So we need to create a new column
            getNewFieldForm($('.acb-elements-container').data('edit-url'), 'column', baseName, addAfter);
            return;
        }

        openSlideForNewField(baseName, addAfter);
    });
    $('body').on('click', '.btn-append-field', function () {
        usedAddFieldBlock = null;
        usedContainer = null;
        let baseName = $('.acb-elements-container').find('> .acb-sortable-group').data('base-name');
        let layout = $(this).closest('.acb-field');
        let addAfter = true;
        if (layout.length > 0) {
            if (layout.hasClass('acb-layout-row')) {
                // We want to add a field within a row
                // So we need to create a new column
                let existingColumns = layout.find('> .acb-sortable');
                let lastRowSize = 0;
                existingColumns.each(function() {
                    lastRowSize += parseInt($(this).data('col-size'));
                    if (lastRowSize >= 12) {
                        lastRowSize = 0;
                    }
                });

                let formData = new FormData();
                let data = {
                    'elementType': 'column',
                    'config': {
                        'size': 12 - lastRowSize
                    }
                };
                let columnData = buildCustomLayoutFormData(formData, data, '__field_name__');

                usedContainer = layout;
                submitNewRow(
                    columnData,
                    $('.acb-elements-container').data('edit-url') + '?type=column',
                    'POST',
                    layout.data('base-name'),
                    false,
                    true
                );

                return;
            } else if (layout.hasClass('acb-layout-column')) {
                // We want to add a field within a column
                // Container will be the column instead of root container
                usedContainer = layout.find('.acb-column-content').find('> .acb-sortable-group');
                baseName = usedContainer.data('base-name');
                if ($(this).closest('.acb-column-toolbar').hasClass('acb-column-toolbar-top')) {
                    addAfter = false;
                }
            }
        }
        openSlideForNewField(baseName, addAfter);
    });

    function openSlideForNewField(baseName, addAfter) {
        slide.empty();
        $.ajax({
            url: $('.acb-elements-container').data('new-field-url'),
            type: 'GET'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            let form = slide.content.find('.acb-add-field-form');
            form.on('submit', function (e) {
                e.preventDefault();
                getNewFieldForm(this.action, $(this).find('input[name=type]:checked').val(), baseName, addAfter);
            });
            form.find('input').on('change', function () {
                form.submit();
            });

            slide.open();
        }).fail(ajaxFailCallback);
    }

    $('body').on('click', '.acb-edit-row', function (e) {
        e.stopPropagation();
        let row = $(this).closest('.acb-field');
        let url = $('.acb-elements-container').data('edit-url');
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
    function getNewFieldForm(url, type, baseName, addAfter = true) {
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
                saveNewFieldData(this, baseName, addAfter);
            });
            slide.open();
            initSortables(slide.content);
        });
    }
    // get editing form
    function getEditFieldForm(url, row) {
        slide.empty();
        let typeInputName = row.data('name') + '[elementType]';
        typeInputName = typeInputName.replaceAll('[', '\\[').replaceAll(']', '\\]');
        let type = $('.acb-elements-form-container').find('input[name=' +  typeInputName + ']').val();

        $.ajax({
            url: url + '?edit=1&type='+type,
            data: extractRowData($('.acb-element-form[data-name="' + row.data('name') + '"]')),
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
                let preview = $(replacePlaceholderEditData(data.preview, row));
                preview.find('> .acb-element-toolbar').data('form-index', row.data('form-index'));
                row.replaceWith(preview);

                let elementForm = $(replacePlaceholderEditData(data.form, row));
                elementForm.data('form-index', row.data('form-index'));
                $('.acb-element-form[data-name="' + name + '"]').replaceWith(elementForm);

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
    function saveNewFieldData(form, baseName, addAfter = true) {
        updateCKEditorElement(form);
        submitNewRow(new FormData(form), form.action, form.method, baseName, true, addAfter);
    }

    function submitNewRow(formData, action, method, baseName, isSlideOpened, addAfter = true) {
        $.ajax({
            url: action,
            data: formData,
            processData: false,
            contentType: false,
            type: method
        }).done(function (data) {
            if (data.success) {
                let container = $('.acb-elements-container').find('> .acb-sortable-group');
                if (usedContainer) {
                    container = usedContainer;
                }
                if (usedAddFieldBlock) {
                    container = usedAddFieldBlock.parents('.acb-sortable-group');
                }
                let counter = container.data('widget-counter') || container.find('> .acb-sortable').length;

                let preview = replacePlaceholderNewData(data.preview, baseName, counter);
                let elementForm = replacePlaceholderNewData(data.form, baseName, counter);

                counter++;
                container.data('widget-counter', counter);

                if (usedAddFieldBlock) {
                    if (addAfter) {
                        usedAddFieldBlock.after(preview);
                    } else {
                        usedAddFieldBlock.before(preview);
                    }
                } else {
                    if (addAfter) {
                        container.append(preview);
                    } else {
                        container.prepend(preview);
                    }
                }
                $('[data-form-container-name="' + baseName + '"]').append($(elementForm));
                initSortables();
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

    function moveElementToList(formData, action, method, baseName, oldName) {
        $.ajax({
            url: action,
            data: formData,
            processData: false,
            contentType: false,
            type: method
        }).done(function (data) {
            if (data.success) {
                let container = $('.acb-sortable-group[data-base-name="' + baseName + '"]');
                let counter = container.data('widget-counter') || container.find('> .acb-sortable').length;

                let preview = replacePlaceholderNewData(data.preview, baseName, counter);
                let elementForm = replacePlaceholderNewData(data.form, baseName, counter);

                counter++;
                container.data('widget-counter', counter);

                $('.acb-sortable[data-name="' + oldName + '"]').replaceWith($(preview));
                $('.acb-elements-form-container').find('.acb-element-form[data-name="' + oldName + '"]').remove();
                $('[data-form-container-name="' + baseName + '"]').append($(elementForm));
                calculatePosition();
            }
        });
    }

    function replacePlaceholderEditData(content, row) {
        return content.replace(/__field_name__/g, row.data('name')).replace(/field_name__/g, row.data('form-id'));
    }
    function replacePlaceholderNewData(content, baseName, counter) {
        let name = baseName + '[__name__]';
        content = content.replace(/__field_name__/g, name)
            .replace(
                /field_name__/g,
                name.replace(/(\]\[)/g, '_')
                    .replace(/[\[\]]/g, '_')
                    .replace(/_{3}$/g, '__')
            ); // replace placeholder in HTML "id"
        content = content.replace(/__name__/g, counter++);

        return content;
    }

    function buildCustomLayoutFormData(formData, data, prefix) {
        for (const [key, value] of Object.entries(data)) {
            if (Array.isArray(value)) {
                for (let i = 0; i < value.length; i++) {
                    formData = buildCustomLayoutFormData(formData, value[i], prefix + '[' + key + ']' + '[' + i + ']');
                }
            } else if (typeof value === 'object') {
                formData = buildCustomLayoutFormData(formData, value, prefix + '[' + key + ']');
            } else {
                formData.append(prefix + '[' + key + ']', value);
            }
        }

        return formData;
    }
});
