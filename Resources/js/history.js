import $ from "jquery";
import { notifConfirm } from './acb-notification.js';
import { hasFormChanged, setFormChanged, isFormUpdating } from './form.js';

let isAddingVersion = false;
let pageDraftFields = $('[data-page-draft]');
if (pageDraftFields.length > 0) {
  pageDraftFields.on('change', function() {
    setFormChanged(true);
  });
}

setInterval(function() {
  if (isFormUpdating() !== false || isAddingVersion !== false || hasFormChanged() === false) {
    return;
  }
  let history = $('.version-history-table');
  if (history.length === 0) {
    return;
  }

  let dataToPost = {'__field_name__': $('#content-data-json').val()};
  if (pageDraftFields.length > 0) {
    let pageDraftData = {};
    pageDraftFields.each(function (index, element) {
      pageDraftData[$(element).data('page-draft')] = $(element).val();
    });
    dataToPost['__page_meta__'] = pageDraftData;
  }

  setFormChanged(false);
  isAddingVersion = true;
  $.ajax({
    url: history.data('save-draft-url'),
    data: dataToPost,
    type: 'POST'
  }).done(function (data) {
    if (data.success) {
      $('.version-history > table tbody').replaceWith($(data.html).find('table tbody'));
    } else {
      setFormChanged(true);
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
        $('.version-history > table tbody').replaceWith($(data.html).find('table tbody'));
      }
    });
  });
});
