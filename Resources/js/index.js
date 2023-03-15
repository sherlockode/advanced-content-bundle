import Slide from "./slide.js";
import jQuery from "jquery";
import 'jquery-ui/ui/widgets/sortable.js';
import './slug.js';
import './export.js';
import './page.js';
import { notifAlert, notifConfirm } from './acb-notification.js';

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
                    // then you are a field,
                    // so you can be moved to any other column
                    connectWithSelector = '.acb-column-sortable-group';
                } else if ($(this).closest('.acb-lateral-slide').length === 0) {
                    // Otherwise you are in root container,
                    // then you are a row,
                    // so you cannot be moved anywhere other than the root container
                    connectWithSelector = false;
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
                        calculatePosition($(this));
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
                        let oldContainer = $(ui.sender);
                        let newContainer = $(event.target);
                        let counter = getCounterFromContainer(newContainer);

                        previewRow.replaceWith(getNewPreview(previewRow, newContainer, counter));
                        updateFormData(newContainer.data('base-name') + '[' + counter + ']', getFormData(previewRow.data('name')));
                        updateFormData(previewRow.data('name'), undefined);

                        counter++;
                        newContainer.data('widget-counter', counter);

                        calculatePosition(newContainer);
                        calculatePosition(oldContainer);

                        toggleColumnPlaceholderButton(oldContainer, true);
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

    function getNewPreview(preview, newContainer, counter) {
        let newPreview = $('<div></div>').append(preview.clone()).html();
        newPreview = $(newPreview.replace(
            new RegExp(escapeNameForRegExp(preview.data('name')), 'g'),
            newContainer.data('base-name') + '[' + counter + ']'
        ));
        newPreview.find('> .acb-element-toolbar').data('form-index', counter);

        return newPreview;
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

    function calculatePosition(group) {
        if (typeof group === 'undefined') {
            group = $(".acb-sortable-group");
        }
        group.each(function(){
            let sortables = $(this).children('.acb-sortable');
            for (var i=0; i < sortables.length; i++) {
                let newPosition = i+1;
                if ($(sortables[i]).closest('.acb-lateral-slide').length === 0) {
                    updateFormData($(sortables[i]).data('name') + '[position]', newPosition);
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
        notifAlert('An error occurred.');
    }

    $('body').on('click', '.acb-collapse-row', function() {
        $(this).closest('.acb-layout-row').toggleClass('collapsed');
        $(this).find('i').toggleClass('fa-caret-down').toggleClass('fa-caret-up');
    });

    $('body').on('click', '.acb-remove-row', function (e) {
        notifConfirm($(this).data('confirm-delete'), function() {
          deleteElement($(this).closest('.acb-row'));
        }.bind(this));
    });

    function deleteElement(element, canCalculatePosition) {
        updateFormData(element.data('name'), undefined);
        element.remove();
        if (canCalculatePosition !== false) {
            calculatePosition();
        }
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

    $('body').on('click', '.acb-duplicate-row', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let previewRow = $(this).closest('.acb-row');
        let container = previewRow.closest('.acb-sortable').parents('.acb-sortable-group');
        let counter = getCounterFromContainer(container);

        previewRow.after(getNewPreview(previewRow, container, counter));
        updateFormData(container.data('base-name') + '[' + counter + ']', getFormData(previewRow.data('name')));

        counter++;
        container.data('widget-counter', counter);

        calculatePosition(container);
    });

    $('body').on('click', '.change-display-options button', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();

        let row = $(this).closest('.acb-field');
        let colNumber = parseInt($(this).data('col-num'));
        let colSize = Math.round(12 / Math.max(1, colNumber));
        let currentColumns = row.find('> .acb-sortable');
        let currentColNumber = currentColumns.length;

        if (currentColNumber > colNumber) {
            // I currently have 4 columns
            // I want to use only 2 columns
            // I need to move content from columns 3 and 4 into column 2
            // Then remove columns 3 and 4
            // And finally change size of columns 1 and 2
            let targetColumnContainer = $(currentColumns[colNumber - 1]).find('.acb-sortable-group');
            let counter = getCounterFromContainer(targetColumnContainer);
            currentColumns.each(function (index, element) {
                if (index >= colNumber) {
                    let columnElements = $(element).find('.acb-sortable-group > .acb-sortable');
                    columnElements.each(function (fieldIndex, field) {
                        let previewRow = $(field).closest('.acb-row');
                        getNewPreview(previewRow, targetColumnContainer, counter).appendTo(targetColumnContainer);
                        updateFormData(targetColumnContainer.data('base-name') + '[' + counter + ']', getFormData(previewRow.data('name')));

                        counter++;
                    });
                    deleteElement($(element), false);
                } else {
                    updateFormData($(element).data('name') + '[config][size]', colSize);
                }
            });
            targetColumnContainer.data('widget-counter', counter);
            calculatePosition(targetColumnContainer);
            updateRowAfterLayoutUpdate(row);
        } else if (currentColNumber < colNumber) {
            // I currently have 2 columns
            // I want to use 3 columns
            // I need to add a third column
            // Then change size of all columns

            let position = 0;
            currentColumns.each(function (index, element) {
                updateFormData($(element).data('name') + '[config][size]', colSize);
                let currentPosition = parseInt(getFormData($(element).data('name') + '[position]'));
                if (currentPosition > position) {
                    position = currentPosition;
                }
            });
            position++;

            let columnsToAdd = colNumber - currentColNumber;
            let counter = getCounterFromContainer(row);
            for (let i = 0; i < columnsToAdd; i++) {
                updateFormData(row.data('base-name') + '[' + counter + ']', {
                    'elementType': 'column',
                    'position': position,
                    'config': {
                        'size': colSize
                    }
                });

                counter++;
                position++;
            }
            row.data('widget-counter', counter);

            // When all columns have been added
            // Update row data and display updated preview
            updateRowAfterLayoutUpdate(row);
        } else {
            // I currently have 2 columns
            // I want to use 2 columns
            // I need to change size of existing columns

            let shouldUpdateRow = false;
            currentColumns.each(function (index, element) {
                if (parseInt(getFormData($(element).data('name') + '[config][size]')) !== colSize) {
                    updateFormData($(element).data('name') + '[config][size]', colSize);
                    shouldUpdateRow = true;
                }
            });
            if (shouldUpdateRow) {
                updateRowAfterLayoutUpdate(row);
            }
        }
    });

    function updateRowAfterLayoutUpdate(row) {
        saveExistingField(
            $('.acb-elements-container').data('edit-url') + '?type=row',
            buildCustomLayoutFormData(new FormData(), getFormData(row.data('name'))),
            'POST',
            row,
            false,
            true
        );
    }

    let slide = new Slide();
    slide.element.on('click', '.form-button', function () {
        $($(this).data('target')).submit();
    });

    let usedAddFieldBlock = null;
    let usedContainer = null;
    let includeNewFieldInRowCol = false;
    let isFormUpdating = false;
    let hasFormChanged = false;
    let isAddingVersion = false;

    setInterval(function() {
        if (isFormUpdating !== false || isAddingVersion !== false || hasFormChanged === false) {
            return;
        }
        let history = $('.version-history-table');
        if (history.length === 0) {
            return;
        }

        hasFormChanged = false;
        isAddingVersion = true;
        $.ajax({
            url: history.data('save-draft-url'),
            data: {'__field_name__': $('#content-data-json').val()},
            type: 'POST'
        }).done(function (data) {
            if (data.success) {
                $('.version-history').replaceWith(data.html);
            } else {
                hasFormChanged = true;
            }
            isAddingVersion = false;
        });
    }, 20 * 1000);
    $('body').on('click', '.version-history .see-all', function () {
        $(this).remove();
    });
    $('body').on('click', '.version-history .acb-version-load', function() {
        window.location = $(this).closest('.version-history-table').data('load-version-url') + '?versionId=' + $(this).closest('tr').data('version-id');
    });
    $('body').on('click', '.version-history .acb-version-remove', function() {
        let history = $(this).closest('.version-history-table');
        let versionLine = $(this).closest('tr');
        notifConfirm($(this).data('confirm-delete'), function() {
            $.ajax({
                url: history.data('remove-version-url'),
                data: {'versionId': versionLine.data('version-id')},
                type: 'POST'
            }).done(function (data) {
                if (data.success) {
                    $('.version-history').replaceWith(data.html);
                }
            });
        });
    });

    $('body').on('change', '#acb-scopes-select-all', function() {
        $('.acb-scopes').find('option').prop('selected', $(this).is(':checked'));
    });

    $('body').on('click', '.btn-new-field', function () {
        let baseName = $(this).parents('.acb-sortable').parents('.acb-sortable-group').data('base-name');
        usedAddFieldBlock = $(this).closest('.acb-row');
        usedContainer = null;
        let addAfter = !$(this).hasClass('btn-new-field-before');

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
        includeNewFieldInRowCol = false;
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
                addColumnToRow(12 - lastRowSize, layout);

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
        } else {
            // If there is no existing layout
            // We will have to create it dynamically after new field is saved
            includeNewFieldInRowCol = true;
        }
        openSlideForNewField(baseName, addAfter);
    });

    function addColumnToRow(columnSize, row) {
        let formData = new FormData();
        let data = {
            'elementType': 'column',
            'config': {
                'size': columnSize
            }
        };
        let columnData = buildCustomLayoutFormData(formData, data);

        usedAddFieldBlock = null;
        usedContainer = row;

        submitNewRow(
            columnData,
            $('.acb-elements-container').data('edit-url') + '?type=column',
            'POST',
            row.data('base-name'),
            false,
            true
        );
    }

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

        let targetName = row.data('name');
        let data = getFormData(targetName);
        if (data.elements) {
            delete data.elements;
        }

        $.ajax({
            url: url + '?edit=1&type=' + data.elementType,
            data: {'__field_name__': JSON.stringify(data)},
            type: 'POST'
        }).done(function (data) {
            slide.setHeader('<h1>' + data.title + '</h1>');
            slide.setContent(data.content);
            slide.setFooter(data.footer);
            slide.content.find('.acb-edit-field-form').on('submit', function (e) {
                e.preventDefault();
                saveFieldData(this, row, false);
            });
            initSortables(slide.content);
        });
    }

    // convert slide form to content preview
    function saveFieldData(form, row, updateElements) {
        updateCKEditorElement(form);
        saveExistingField(form.action, new FormData(form), form.method, row, true, updateElements);
    }

    function saveExistingField(url, formData, method, row, isSlideOpened, updateElements) {
        $.ajax({
            url: url,
            data: formData,
            processData: false,
            contentType: false,
            type: method
        }).done(function (data) {
            if (data.success) {
                let name = row.data('name');
                let formIndex = row.find('> .acb-element-toolbar').data('form-index');

                let preview = $(replacePlaceholderEditData(data.preview, name, formIndex));
                if (updateElements === false) {
                    let oldContainer = null, newContainer = null, counter = null, elements = [];
                    if (row.hasClass('acb-sortable-group')) {
                        oldContainer = row;
                        newContainer = preview;
                    } else if (row.hasClass('acb-layout-column')) {
                        oldContainer = row.find('> .acb-column-content > .acb-sortable-group');
                        newContainer = preview.find('> .acb-column-content > .acb-sortable-group');
                    }
                    if (oldContainer !== null) {
                        counter = getCounterFromContainer(oldContainer);
                        elements = oldContainer.find('> .acb-sortable');
                        newContainer.append(elements);
                        newContainer.data('widget-counter', counter);
                    }
                }
                row.replaceWith(preview);

                for (const [key, value] of Object.entries(JSON.parse($(data.form).val()))) {
                    if (key === 'elements' && updateElements === false) {
                        continue;
                    }
                    updateFormData(name + '[' + key + ']', value);
                }

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
                        saveFieldData(this, row, updateElements);
                    });
                }
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
                if (includeNewFieldInRowCol) {
                    usedAddFieldBlock = null;
                    usedContainer = null;
                    includeNewFieldInRowCol = false;

                    let layoutData = buildCustomLayoutFormData(new FormData(), {
                        'elementType': 'row',
                        'elements': [
                            {
                                'elementType': 'column',
                                'config': {
                                    'size': 12
                                },
                                'elements': [
                                    JSON.parse($(data.form).val())
                                ]
                            }
                        ]
                    });

                    submitNewRow(
                        layoutData,
                        $('.acb-elements-container').data('edit-url') + '?type=row',
                        'POST',
                        $('.acb-elements-container').find('> .acb-sortable-group').data('base-name'),
                        isSlideOpened,
                        addAfter
                    );
                } else {
                    let container = $('.acb-elements-container').find('> .acb-sortable-group');
                    if (usedContainer) {
                        container = usedContainer;
                    }
                    if (usedAddFieldBlock) {
                        container = usedAddFieldBlock.parents('.acb-sortable-group');
                    }
                    let counter = getCounterFromContainer(container);

                    let preview = replacePlaceholderNewData(data.preview, baseName, counter);
                    updateFormData(baseName + '[' + counter + ']', JSON.parse($(data.form).val()));

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
                    initSortables();
                    calculatePosition();

                    if (isSlideOpened) {
                        slide.close();
                    }
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

    function getCounterFromContainer(container) {
        return typeof container.data('widget-counter') !== 'undefined' ?
            parseInt(container.data('widget-counter')) :
            container.find('> .acb-sortable').length;
    }

    function replacePlaceholderEditData(content, name, formIndex) {
        return content.replace(/__field_name__/g, name).replace(/__name__/g, formIndex);
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
    function escapeNameForRegExp(name) {
        return name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
    }

    function buildCustomLayoutFormData(formData, data, prefix) {
        if (typeof prefix === 'undefined') {
            prefix = '__field_name__';
        }
        for (const [key, value] of Object.entries(data)) {
            if (Array.isArray(value)) {
                for (let i = 0; i < value.length; i++) {
                    formData = buildCustomLayoutFormData(formData, value[i], prefix + '[' + key + ']' + '[' + i + ']');
                }
            } else if (typeof value === 'object' && value !== null) {
                formData = buildCustomLayoutFormData(formData, value, prefix + '[' + key + ']');
            } else {
                if (value !== false) {
                    let newValue = value;
                    if (newValue === true) {
                        newValue = 1;
                    } else if (newValue === null) {
                        newValue = '';
                    }
                    formData.append(prefix + '[' + key + ']', newValue);
                }
            }
        }

        return formData;
    }

    function getFormData(targetName, currentName, data) {
        if (typeof currentName === 'undefined') {
            currentName = $('#content-data-json').attr('name');
        }
        if (typeof data === 'undefined') {
            data = JSON.parse($('#content-data-json').val());
        }
        for (const [key, value] of Object.entries(data)) {
            let name = currentName + '[' + key + ']';
            if (name === targetName) {
                return value;
            }
            if (targetName.match(new RegExp('^' + escapeNameForRegExp(name)))) {
                return getFormData(targetName, name, data[key]);
            }
        }
    }
    function replaceJsonData(data, path, value) {
        const [head, ...rest] = path.split('.');

        if (head) {
            return {
                ...data,
                [head]: rest.length
                    ? replaceJsonData(data[head], rest.join('.'), value)
                    : value
            }
        }

        return {
            ...data,
            ...value
        }
    }
    function updateFormData(name, value) {
        isFormUpdating = true;
        let field = $('#content-data-json');
        let targetName = name.replace(field.attr('name'), '');
        targetName = targetName.replace(/(\]\[)/g, '.')
            .replace(/[\[\]]/g, '.')
            .replace(/\.$/g, '')
            .replace(/^\./g, '');
        let newData = replaceJsonData(JSON.parse(field.val()), targetName, value);
        field.val(JSON.stringify(newData));
        hasFormChanged = true;
        isFormUpdating = false;
    }

    slide.element.on('slideContentUpdated', updateExampleContainer);
    slide.element.on('change, input', '[data-css-property]', function() {
        if ($(this).data('controls')) {
            updateControls(false);
        }
        updateExampleContainer();
    });

    function updateExampleContainer() {
        $('.element-design-form').find('[data-css-property]').each(function(index, element) {
            if ($(element).data('select-color')) {
                if ($(element).val() === 'none') {
                    $('.example').css($(element).data('css-property'), '');
                } else if ($(element).val() === 'transparent') {
                    $('.example').css($(element).data('css-property'), 'transparent');
                }

                return;
            } else if ($('.element-design-form').find('[data-select-color="' + $(element).data('css-property') + '"]').length > 0) {
                if ($('.element-design-form').find('[data-select-color="' + $(element).data('css-property') + '"]').val() !== 'pick') {
                    return;
                }
            }

            $('.example').css($(element).data('css-property'), function() {
                let value = $(element).val();
                if ($(element).closest('.box-model').length > 0 && value !== '') {
                    value = value + 'px';
                }

                return value;
            });
        });
    }

    slide.element.on('slideContentUpdated', updateControls);
    slide.element.on('change', '.simplify-controls', updateControls);

    function updateControls(updateContainer = true) {
        if (slide.element.find('.simplify-controls').is(':checked')) {
            $('.box-model [data-follows]').prop('readonly', true);
            $('.box-model [data-controls]').each(function (index, element) {
                $('.box-model [data-follows="' + $(element).data('controls') + '"]').val($(element).val());
            });
            if (updateContainer !== false) {
                updateExampleContainer();
            }
        } else {
            $('.box-model [data-follows]').prop('readonly', false);
        }
    }
});
