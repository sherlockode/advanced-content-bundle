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
        });
    });

    $('.acb-fields').on('click', '.remove-field', function (e) {
        var fieldRow = $(this).closest('.field-row');
        fieldRow.remove();
    });

    $('.acb-fields').on('change', '.field-type', function (e) {
        var fieldRow = $(this).closest('.field-row');
        var fieldId = fieldRow.data('field-id');
        var contentTypeId = fieldRow.data('content-type-id');
        var data = {
            contentTypeId: contentTypeId,
            fieldId: fieldId,
            type: $(this).val()
        };
        $.ajax({
            url: $('.edit-content-type').data('change-type-url'),
            type: 'POST',
            data: data,
            context: this
        }).done(function (data) {
            $(this).closest('div.field-row').find('.options').html(data.html);
            updateChoiceList();
            hideEmptyOptionsRow();
        }).fail(ajaxFailCallback);
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
});
