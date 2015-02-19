var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.nestedForm = (function(window, undefined) {

    var init, setupForm,
        addNewItem, delItem,
        addNewBtn, addDelBtn, showOrHideActions;


    var newButtonHtml = '<button type="button" class="js-nested-form__add-btn btn btn-primary btn--raise-on-hover nested-form__add-btn">Add</button>';
    var delButtonHtml = '<button type="button" class="js-nested-form__del-btn btn--raise-on-hover nested-form__item__header__actions__action nested-form__item__header__actions__action--del"><i class="fa fa-trash-o"></i></button>'


    init = function() {
        $('.js-nested-form').each(function() {
            setupForm($(this));
        });
    };


    // Initial Setup
    setupForm = function($form) {
        var sortable = $form.data('sortable'),
            allowNew = $form.data('allow-new'),
            allowDelete = $form.data('allow-delete'),
            minItems = $form.data('min-items'),
            maxItems = $form.data('max-items'),
            $currentItems = $form.find('.js-nested-form__item');

        console.log('sortable: ' + sortable + ' | allowAdd: ' + allowNew + ' | allowDelete: ' + allowDelete + ' | minItems: ' + minItems + ' | maxItems: ' + maxItems);

        // Set index
        var totalItems = $currentItems.length;
        $form.data('index', totalItems);

        // Add "New" button
        if(allowNew) {
            addNewBtn($form);
        }

        // Add "Delete" button
        if(allowDelete) {
            $currentItems.each(function() {
                addDelBtn($(this), $form);
            });
        }

        // Make sure we have at least as many items than minimally required
        if (minItems > 0 && minItems > totalItems) {
            var newNeeded = minItems - totalItems;

            for (var i=0; i<newNeeded; i++) {
                addNewItem($form);
            }
        }

        // Check Actions
        showOrHideActions($form);
    };


    // Show/Hide Actions (New and Delete buttons)
    showOrHideActions = function($form) {
        var allowNew = $form.data('allow-new'),
            allowDelete = $form.data('allow-delete'),
            minItems = $form.data('min-items'),
            maxItems = $form.data('max-items'),
            totalItems = $form.find('.js-nested-form__item').length;

        var $newBtn = $form.find('.js-nested-form__add-btn'),
            $delBtn = $form.find('.js-nested-form__del-btn');

        // "New" button
        if(allowNew && (maxItems === false || totalItems < maxItems) {
            $newBtn.removeClass('hidden');
        } else {
            $delBtn.addClass('hidden');
        }

        // "Delete" button
        if(allowDelete && totalItems > minItems) {
            $delBtn.removeClass('hidden');
        } else {
            $delBtn.addClass('hidden');
        }
    };


    // Add "New" button
    addNewBtn = function($form) {
        var $newBtn = $(newButtonHtml);

        $form.append($newBtn);

        $newBtn.on('click', function() {
            addNewItem($form);
        });
    };


    // Add "Delete" button
    addDelBtn = function($item, $form) {
        var $actionBar = $item.find('.js-nested-form__item__header__actions'),
            $delBtn = $(delButtonHtml);

        $actionBar.append($delBtn);

        $delBtn.on('click', function() {
            delItem($(this), $form, $item)
        });
    };


    // Add new item
    addNewItem = function($form) {
        var prototype = $form.data('prototype'),
            currentIndex = $form.data('index'),
            sortable = $form.data('sortable');

        // Update prototype with new index
        prototype = prototype.replace(/__name__/g, currentIndex);

        // Increase the index with one for the next item
        $form.data('index', currentIndex + 1);

        // Make item template
        if(sortable) {
            var sortKey = $form.data('sortkey').replace(/__name__/g, currentIndex);
            var $newItem = $('<div class="js-nested-form__item nested-form__item js-sortable-item sortable-item" data-sortkey="' + sortKey + '"><header class="js-sortable-item__handle nested-form__item__header"><i class="fa fa-arrows nested-form__item__header__move-icon"></i><div class="js-nested-form__item__header__actions nested-form__item__header__actions"></div></header><div class="js-nested-form__item__view nested-form__item__view"></div></div>');
        } else {
            var $newItem = $('<div class="js-nested-form__item nested-form__item"><header class="nested-form__item__header"><div class="js-nested-form__item__header__actions nested-form__item__header__actions"></div></header><div class="js-nested-form__item__view nested-form__item__view"></div></div>');
        }

        $newItem.find('.js-nested-form__item__view').append(prototype);

        // Append before "New" button
        var $newBtn = $form.find('.js-nested-form__add-btn');

        $newBtn.before($newItem);

        // Add "Delete" button
        addDelBtn($newItem, $form);

        // Reinit rich Editors
        kunstmaanbundles.richEditor.destroyRichEditors();
        kunstmaanbundles.richEditor.init();
    };


    // Delete item
    delItem = function($btn, $form, $item) {
        var delKey = $item.data('delete-key');

        // Set delKey
        if(delKey) {
            $form.append('<input type="hidden" name="' + delKey + '" value="1">');
        }

        // Remove item
        $item.remove();

        // Check that we need to show/hide the add/delete buttons
        showOrHideActions($form);
    };


    return {
        init: init
    };

}(window));
