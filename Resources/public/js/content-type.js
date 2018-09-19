jQuery(function ($) {
    'use strict';

    updateChoiceList();
    hideEmptyOptionsRow();

    function ajaxFailCallback(jqXhr) {
        alert('An error occurred.');
    }

    $('body').on('submit', '.form-create-field', function (e) {
        e.preventDefault();
        var form = $(this);
        var button = form.children('.btn-add-field');
        button.prop('disabled', true);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: form.serialize()
        }).done(function (resp) {
            if (resp.success) {
                $('.acb-fields').append(resp.html);
                $('.acb-fields .panel:last-of-type .edit-field').click();
                hideEmptyOptionsRow();
                var modal = form.closest('.modal');
                if (modal.length) {
                    modal.modal('hide');
                }
            } else {
                $('.add-field-form').replaceWith(resp.html);
            }
        }).always(function () {
            button.prop('disabled', false);
        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-btn-add-field', function () {
        var url = $(this).data('url');
        $.get(url, function (response) {
            $('.acb-modal-add-field .modal-body').html(response);
            $('.acb-modal-add-field').modal();

            var slugInput = $('.acb-modal-add-field').find('.acb-slug');
            var slugSource = $(slugInput.data('slug-source'));
            var timer;
            slugSource.on('change.acb', function () {
                slugInput.val(generateSlug(this.value));
            });
            slugSource.on('keyup.acb', function () {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    slugInput.val(generateSlug(slugSource.val()));
                }, 300);
            });
            slugInput.on('change', function () {
                slugSource.off('change.acb');
                slugSource.off('keyup.acb');
            });
        });
    });

    $('.acb-fields').on('click', '.remove-field', function (e) {
        var fieldRow = $(this).closest('.field-row');
        fieldRow.remove();
    });

    $('.acb-fields').on('change', '.field-type', function (e) {
        var fieldRow = $(this).closest('.field-row');
        var fieldId = fieldRow.data('field-id');
        var data = {
            fieldId: fieldId,
            type: $(this).val()
        };
        $.ajax({
            url: $(this).closest('.edit-content-type').data('change-type-url'),
            type: 'POST',
            data: data,
            context: this
        }).done(function (data) {
            $(this).closest('div.field-row').find('.options').html(data.html);
            updateChoiceList();
            hideEmptyOptionsRow();
        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-add-collection-item', function (e) {
        e.preventDefault();
        var list = $($(this).attr('data-list'));
        var counter = list.data('widget-counter') || list.children().length;
        var newWidget = list.data('prototype');
        newWidget = newWidget.replace(/__name__/g, counter);
        counter++;
        list.data('widget-counter', counter);
        var newElem = $('<div>').html(newWidget);
        newElem.appendTo(list);
    });

    function updateChoiceList() {
        $('.choice-list').find('li').each(function() {
            if ($(this).find('.delete-choice').length === 0) {
                addChoiceRemoveLink($(this));
            }
        });
    }
    $('body').on('click', '.add-another-choice', function (e) {
        e.preventDefault();
        var list = $(this).siblings('.choice-list');
        var counter = list.data('widget-counter');
        var newWidget = list.data('prototype');

        counter++;
        newWidget = newWidget.replace(/__name__/g, counter);
        list.data('widget-counter', counter);

        var newElem = $(list.data('widget-tags')).html(newWidget);
        addChoiceRemoveLink(newElem);
        newElem.appendTo(list);
    });
    $('body').on('click', '.delete-choice', function (e) {
        e.preventDefault();
        $(this).closest('.choice-row').remove();
    });
    function addChoiceRemoveLink(choiceLi) {
        choiceLi.append($('.field-options-remove-link').html());
    }

    function hideEmptyOptionsRow() {
        $('.options-row').each(function() {
            $(this).show();
            if ($(this).find('.no-option').length > 0) {
                $(this).hide();
            }
        });
    }

    function generateSlug (value) {
        // use speakingurl lib if available
        if (typeof getSlug === 'function') {
            return getSlug(value);
        }
        // simple custom slug generator for fallback
        // 1) convert to lowercase
        // 2) remove dashes and pluses
        // 3) replace spaces with dashes
        // 4) remove everything but alphanumeric characters and dashes
        return value.toLowerCase().trim().replace(/-+/g, '').replace(/\s+/g, '-').replace(/[^a-z0-9-]/g, '');
    }
});
