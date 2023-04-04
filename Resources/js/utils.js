import $ from 'jquery';

function updateCKEditorElement(form) {
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

function getNewPreview(preview, newContainer, counter) {
  let newPreview = $('<div></div>').append(preview.clone()).html();
  newPreview = $(newPreview.replace(
    new RegExp(escapeNameForRegExp(preview.data('name')), 'g'),
    newContainer.data('base-name') + '[' + counter + ']'
  ));
  newPreview.find('> .acb-element-toolbar').data('form-index', counter);

  return newPreview;
}

export {
  updateCKEditorElement,
  getCounterFromContainer,
  replacePlaceholderEditData,
  replacePlaceholderNewData,
  escapeNameForRegExp,
  getNewPreview
};
