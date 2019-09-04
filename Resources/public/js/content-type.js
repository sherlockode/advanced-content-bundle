jQuery(function ($) {
    'use strict';

    updateChoiceList();
    hideEmptyOptionsRow();
    hideEmptyLayoutRow();
    initSortables();

    function initSortables() {
        $(".acb-sortable-group").each(function(){
            $(this).sortable({
                containment: "parent",
                items: '.acb-sortable[data-sortable-parent-group-id="' + $(this).data('sortable-group-id') + '"]',
                cursor: "move",
                axis: "y",
                update: function(event, ui) {
                    calculatePosition();
                }
            });
        });
    }

    function calculatePosition() {
        $(".acb-sortable-group").each(function(){
            let sortables = $(this).find('.acb-sortable[data-sortable-parent-group-id="' + $(this).data('sortable-group-id') + '"]');
            for (var i=0; i < sortables.length; i++) {
                let newPosition = i+1;
                $(sortables[i]).find('[name$="[position]"]').first().val(newPosition);
                $(sortables[i]).find('.panel-position').first().html(newPosition);
            }
        });
    }

    var fieldsList;

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
                var newElement = resp.html;
                newElement = newElement.replace(/__random_id__/g, (Math.random() * 1000)|0);
                newElement = $(newElement);
                newElement.find('[name$="[position]"]').val(fieldsList.children().length + 1);
                fieldsList.append(newElement);
                newElement.find('.edit-field').click();
                hideEmptyOptionsRow();
                hideEmptyLayoutRow();
                var modal = form.closest('.modal');
                if (modal.length) {
                    modal.modal('hide');
                }
                initSortables();
            } else {
                $('.add-field-form').replaceWith(resp.html);
            }
        }).always(function () {
            button.prop('disabled', false);
        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-btn-add-field', function () {
        var url = $(this).data('url');
        fieldsList = $('.acb-fields');
        openAddFieldModal(url);
    });

    function openAddFieldModal(url) {
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
    }

    $('body').on('click', '.acb-remove-row', function (e) {
        var fieldRow = $(this).closest('.acb-row');
        fieldRow.remove();
        calculatePosition();
    });

    $('.acb-fields').on('change', '.field-type', function (e) {
        var fieldRow = $(this).closest('.field-row');
        var data = {
            type: $(this).val(),
            formPath: $(this).data('form-path')
        };
        $.ajax({
            url: $(this).closest('.edit-content-type').data('change-type-url'),
            type: 'POST',
            data: data,
            context: this
        }).done(function (data) {
            $(this).closest('div.field-row').find('.options').html(data.optionHtml);
            $(this).closest('div.field-row').find('.layout-row').html(data.layoutHtml);
            updateChoiceList();
            hideEmptyOptionsRow();
            hideEmptyLayoutRow();
        }).fail(ajaxFailCallback);
    });

    $('body').on('click', '.acb-add-collection-item', function (e) {
        e.preventDefault();

        var list = $($(this).attr('data-list'));
        var counter = list.data('widget-counter') || list.children().length;

        var type = $(this).data('type');
        if (type === 'group') {
            var newWidget = list.data('prototype');
            newWidget = newWidget.replace(/__name__label__/g, counter);
            newWidget = newWidget.replace(/__name__/g, counter);
            newWidget = newWidget.replace(/__random_id__/g, (Math.random() * 1000)|0);
            counter++;
            list.data('widget-counter', counter);
            var newElem = $(newWidget);
            newElem.appendTo(list);
            initSortables();
            calculatePosition();
        } else {
            counter++;
            var url = $(this).data('url');
            url = url.replace(/__name__/g, counter);
            fieldsList = list;

            openAddFieldModal(url);
            list.data('widget-counter', counter);
        }
    });
    $('body').on('click', '.acb-add-flexible-item', function (e) {
        e.preventDefault();
        var wrapper = $(this).closest('.acb-flexible-add-wrapper');
        var list = $(wrapper.attr('data-list'));
        var counter = list.data('widget-counter') || list.children().length;
        var name = wrapper.attr('data-name-prefix') + '[' + counter + ']';
        var id = wrapper.attr('data-id-prefix') + '_' + counter;

        $.get(wrapper.data('url'), {
            contentTypeId: wrapper.data('content-type'),
            layoutId: $(this).data('layout'),
            parentFormId: wrapper.data('form-id')
        }, function (response) {
            var newWidget = response;
            newWidget = newWidget.replace(/__flexible_name__/g, name);
            newWidget = newWidget.replace(/flexible_name__/g, id);
            counter++;
            list.data('widget-counter', counter);
            var newElem = $(newWidget);
            newElem.appendTo(list);
            initSortables();
            calculatePosition();
        });
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

    $('body').on('submit', '.edit-content-type', function(e) {
        var validateChoiceLists = true;
        var validateSlugs = true;
        var slugs = {};
        var slug = '';

        $('.acb-error').hide();
        $('.field-error').removeClass('field-error');

        $('.choice-list').each(function() {
            var fieldArea = $(this).closest('.acb-field');
            var isRequired = fieldArea.find('.acb-is-required').val();
            if (isRequired > 0 && $(this).children().length === 0) {
                validateChoiceLists = false;
                fieldArea.find('.acb-collection-error').show();
                fieldArea.addClass('field-error');
                fieldArea.parents('.acb-field').addClass('field-error');
            }
        });

        $('.acb-slug', this).each(function() {
            slug = $(this).val();
            if (!slugs[slug]) {
                slugs[slug] = [];
            }
            slugs[slug].push($(this));
        });
        for (slug in slugs) {
            if (slugs[slug].length > 1) {
                for (var i=0; i< slugs[slug].length; i++) {
                    var field = slugs[slug][i];
                    var fieldArea = field.closest('.acb-field');

                    fieldArea.find('.acb-slug-error').show();
                    fieldArea.addClass('field-error');
                    fieldArea.parents('.acb-field').addClass('field-error');
                }
                validateSlugs = false;
            }
        }

        if (!validateChoiceLists || !validateSlugs) {
            e.preventDefault();
        }
    });

    function addChoiceRemoveLink(choiceLi) {
        choiceLi.append($('.field-options-remove-link').html());
    }

    function hideEmptyOptionsRow() {
        $('.options-row').each(function() {
            $(this).show();
            if ($(this).find('.options > .no-option').length > 0) {
                $(this).hide();
            }
        });
    }

    function hideEmptyLayoutRow() {
        $('.layout-row').each(function() {
            $(this).show();
            if ($(this).children().length == 0) {
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
