import $ from "jquery";
import { getFormData, updateFormData } from "./form";
import { getCounterFromContainer, getNewPreview } from './utils.js';

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

function deleteElement(element, canCalculatePosition) {
  if (element.closest('.acb-lateral-slide').length === 0) {
    updateFormData(element.data('name'), undefined);
  }
  element.remove();
  if (canCalculatePosition !== false) {
    calculatePosition();
  }
}

export {
  initSortables,
  calculatePosition,
  deleteElement
};
