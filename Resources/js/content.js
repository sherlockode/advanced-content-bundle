import $ from "jquery";
import slide from "./slide.js";
import { buildCustomLayoutFormData, getFormData, updateFormData } from "./form";
import { notifAlert } from "./acb-notification";
import { updateCKEditorElement, getCounterFromContainer, replacePlaceholderEditData, replacePlaceholderNewData } from './utils.js';
import { initSortables, calculatePosition } from './layout.js';

let usedAddFieldBlock = null;
let usedContainer = null;
let includeNewFieldInRowCol = false;

function updateContentPosition(usedAddFieldBlockValue, usedContainerValue, includeNewFieldInRowColValue) {
  usedAddFieldBlock = usedAddFieldBlockValue;
  usedContainer = usedContainerValue;
  includeNewFieldInRowCol = includeNewFieldInRowColValue;
}

function ajaxFailCallback(jqXhr) {
  notifAlert('An error occurred.');
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
    slide.enableBackButton(() => openSlideForNewField(baseName, addAfter));
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
        updateContentPosition(null, null, false);

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

export {
  updateContentPosition,
  openSlideForNewField,
  getNewFieldForm,
  getEditFieldForm,
  saveExistingField,
  submitNewRow
};
