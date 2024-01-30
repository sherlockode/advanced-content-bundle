import $ from 'jquery';

let collectionClass = 'acb-collection-list';
let Collection = {};
Collection.init = function(element) {
  if (element.hasClass(collectionClass) && element.data('index') === undefined) {
    element.data('index', element.children('.acb-collection-item').length);
  }

  element.find('.' + collectionClass).each(function() {
    Collection.init($(this));
  });
};

$(function () {
  'use strict';

  $('body').on('click', '.btn-add-acb-collection', function () {
    let collectionHolder = $(this).closest('.acb-collection-widget').find('.' + collectionClass);
    let prototypeTarget = $(this).data('target') || 'prototype';

    if (collectionHolder.length) {
      addCollectionRow(collectionHolder.first(), prototypeTarget);
    }
  });

  $('body').on('click', '.btn-remove-acb-collection', function () {
    let collectionHolder = $(this).closest('.acb-collection-widget').find('.' + collectionClass);
    $(this).closest('.acb-collection-item').remove();
    collectionHolder.trigger('acb-collection-remove-item');
  });

  function addCollectionRow(collectionHolder, prototypeTarget = 'prototype') {
    let prototype = collectionHolder.data(prototypeTarget);
    let index = collectionHolder.data('index');

    let newRow = $(prototype.replace(/__name__/g, index));
    collectionHolder.data('index', index + 1);

    collectionHolder.append(newRow);
    collectionHolder.trigger('acb-collection-append-new-item', [newRow]);
  }
});

export default Collection;
