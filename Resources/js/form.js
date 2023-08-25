import $ from "jquery";
import { escapeNameForRegExp } from './utils.js';

let formUpdating = false;
let formChanged = false;

function buildCustomLayoutFormData(formData, data, prefix) {
  if (typeof prefix === 'undefined') {
    prefix = '__field_name__';
  }
  for (const [key, value] of Object.entries(data)) {
    if (Array.isArray(value)) {
      for (let i = 0; i < value.length; i++) {
        if (typeof value[i] === 'object' && value[i] !== null) {
          formData = buildCustomLayoutFormData(formData, value[i], prefix + '[' + key + ']' + '[' + i + ']');
        } else {
          formData = buildScalarFormData(formData, prefix + '[' + key + ']' + '[' + i + ']', value[i]);
        }
      }
    } else if (typeof value === 'object' && value !== null) {
      formData = buildCustomLayoutFormData(formData, value, prefix + '[' + key + ']');
    } else {
      formData = buildScalarFormData(formData, prefix + '[' + key + ']', value);
    }
  }

  return formData;
}

function buildScalarFormData(formData, name, value) {
  if (value !== false) {
    let newValue = value;
    if (newValue === true) {
      newValue = 1;
    } else if (newValue === null) {
      newValue = '';
    }
    formData.append(name, newValue);
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
  formUpdating = true;
  let field = $('#content-data-json');
  let targetName = name.replace(field.attr('name'), '');
  targetName = targetName.replace(/(\]\[)/g, '.')
    .replace(/[\[\]]/g, '.')
    .replace(/\.$/g, '')
    .replace(/^\./g, '');
  let newData = replaceJsonData(JSON.parse(field.val()), targetName, value);
  field.val(JSON.stringify(newData));
  formChanged = true;
  formUpdating = false;
}
function hasFormChanged() {
  return formChanged;
}
function setFormChanged(flag) {
  formChanged = flag;
}
function isFormUpdating() {
  return formUpdating;
}
function setFormUpdating(flag) {
  formUpdating = flag;
}

export {
  buildCustomLayoutFormData,
  getFormData,
  updateFormData,
  hasFormChanged,
  setFormChanged,
  isFormUpdating
};
