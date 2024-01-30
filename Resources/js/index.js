import slide from "./slide.js";
import jQuery from "jquery";
import 'jquery-ui/ui/widgets/sortable.js';
import './slug.js';
import './export.js';
import './history.js';
import './acb-slide-collection.js';
import { notifConfirm } from './acb-notification.js';
import { buildCustomLayoutFormData, getFormData, updateFormData } from './form.js';
import { initSortables, calculatePosition, deleteElement } from './layout.js';
import { getCounterFromContainer, getNewPreview } from './utils.js';
import { updateContentPosition, openSlideForNewField, getNewFieldForm, getEditFieldForm, saveExistingField, submitNewRow } from './content.js';

jQuery(function ($) {
    initSortables();

    $('body').on('click', '.acb-collapse-row', function() {
        $(this).closest('.acb-layout-row').toggleClass('collapsed');
        $(this).find('i').toggleClass('fa-caret-down').toggleClass('fa-caret-up');
    });

    $('body').on('click', '.acb-remove-row', function (e) {
        notifConfirm($(this).data('confirm-delete'), function() {
          deleteElement($(this).closest('.acb-row'));
        }.bind(this));
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

    $('body').on('change', '#acb-scopes-select-all', function() {
        $('.acb-scopes').find('option').prop('selected', $(this).is(':checked'));
    });

    $('body').on('click', '.btn-new-field', function () {
        let baseName = $(this).parents('.acb-sortable').parents('.acb-sortable-group').data('base-name');
        let element = $(this).closest('.acb-row');
        let addAfter = !$(this).hasClass('btn-new-field-before');
        updateContentPosition(element, null, false);

        if (element.hasClass('acb-layout-column')) {
            // We want to add a new field before or after a column
            // So we need to create a new column
            getNewFieldForm($('.acb-elements-container').data('edit-url'), 'column', baseName, addAfter);
            return;
        }

        openSlideForNewField(baseName, addAfter);
    });
    $('body').on('click', '.btn-append-field', function () {
        updateContentPosition(null, null, false);
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
                    let colSize = parseInt($(this).data('col-size'));

                    if (Number.isInteger(colSize)) {
                        lastRowSize += colSize;
                    } else {
                        lastRowSize = 0;

                        return false;
                    }

                    if (lastRowSize >= 12) {
                        lastRowSize = 0;
                    }
                });

                addColumnToRow(12 - lastRowSize, layout);

                return;
            } else if (layout.hasClass('acb-layout-column')) {
                // We want to add a field within a column
                // Container will be the column instead of root container
                let usedContainer = layout.find('.acb-column-content').find('> .acb-sortable-group');
                updateContentPosition(null, usedContainer, false);
                baseName = usedContainer.data('base-name');
                if ($(this).closest('.acb-column-toolbar').hasClass('acb-column-toolbar-top')) {
                    addAfter = false;
                }
            }
        } else {
            // If there is no existing layout
            // We will have to create it dynamically after new field is saved
            updateContentPosition(null, null, true);
        }
        openSlideForNewField(baseName, addAfter);
    });

    $('body').on('click', '.acb-edit-row', function (e) {
        e.stopPropagation();
        let row = $(this).closest('.acb-field');
        let url = $('.acb-elements-container').data('edit-url');
        getEditFieldForm(url, row);
        slide.open();
    });

    slide.element.on('click', '.form-button', function () {
      $($(this).data('target')).submit();
    });

    slide.element.on('slideContentUpdated', updateExampleContainer);
    slide.element.on('change, input', '[data-css-property]', function() {
        if ($(this).data('controls')) {
            updateControls(false);
        }
        updateExampleContainer();
    });

    slide.element.on('slideContentUpdated', updateControls);
    slide.element.on('change', '.simplify-controls', updateControls);

    $('body').on('change', '[data-mime-type-restriction]', function (e) {
        let inputFiles = $(this).closest('.acb-widget-container').find('input[type=file]');
        let mimeTypeOptionContainer = $(this).closest('.acb-widget-container');
        if ($(this).closest('.picture-field').length > 0) {
            inputFiles = $(this).closest('.picture-field').find('input[type=file]');
            mimeTypeOptionContainer = $(this).closest('.picture-field');
        }

        if (!inputFiles.length) {
            return;
        }

        let mimeTypeOption = mimeTypeOptionContainer.find('[data-mime-type-restriction-values]').find(':selected');
        let mimeTypeValues = [];

        if (mimeTypeOption.length) {
            mimeTypeValues = mimeTypeOption.data('mime-type');
        } else {
            mimeTypeOptionContainer.find('[data-mime-type-restriction-values]').find('option').each(function() {
                mimeTypeValues = $.merge(mimeTypeValues, $(this).data('mime-type'));
            });
        }

        if (!mimeTypeValues.length) {
            return;
        }

        let allImageType = mimeTypeValues.length === 1 && mimeTypeValues[0] === 'image/*';
        let mimeTypeValuesMessage = mimeTypeValues.map(type => `"${type}"`);
        mimeTypeValuesMessage = mimeTypeValuesMessage.join(', ');

        inputFiles.each(function(index, inputFile) {
            if (!inputFile.files.length) {
                updateFileErrorMessage($(inputFile));

                return;
            }
            if (allImageType && inputFile.files[0].type.includes('image/')) {
                updateFileErrorMessage($(inputFile));
            } else if (mimeTypeValues.includes(inputFile.files[0].type)) {
                updateFileErrorMessage($(inputFile));
            } else {
                updateFileErrorMessage($(inputFile), mimeTypeValuesMessage, inputFile.files[0].type);
            }
        });
    });

  function updateFileErrorMessage(inputFile, mimeTypes, currentType) {
    let errorMessage = inputFile.closest('.acb-widget-container').find('.invalid-feedback');
    if (typeof mimeTypes === 'undefined') {
      errorMessage.removeClass('d-block').addClass('d-none');
    } else {
      let msg = errorMessage.data('error');

      msg = msg.replace('%mime%', currentType).replace('%allowed_mime%', mimeTypes);

      errorMessage.html(msg);
      errorMessage.removeClass('d-none').addClass('d-block');
    }
  }

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

  function addColumnToRow(columnSize, row) {
    let formData = new FormData();
    let data = {
      'elementType': 'column',
      'config': {
        'size': columnSize
      }
    };
    let columnData = buildCustomLayoutFormData(formData, data);

    updateContentPosition(null, row, false);

    submitNewRow(
      columnData,
      $('.acb-elements-container').data('edit-url') + '?type=column',
      'POST',
      row.data('base-name'),
      false,
      true
    );
  }

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
