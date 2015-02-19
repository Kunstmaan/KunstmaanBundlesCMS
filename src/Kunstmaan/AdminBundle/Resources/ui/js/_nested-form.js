var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.nestedForm = (function(window, undefined) {

    var init, setupForm,
        addNewItem,
        addNewBtn, addDelBtn, showOrHideAddDelete;


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

        if(allowDelete) {
            $currentItems.each(function() {
                addDelBtn($(this), $form);
            });
        }
    };


    // Show/Hide Add and Delete buttons
    showOrHideAddDelete = function($form) {

    };


    // Add new item
    addNewItem = function($form) {

    };


    // Add "New" button
    addNewBtn = function($form) {
        var $addBtn = $(newButtonHtml);

        $form.append($addBtn);

        $addBtn.on('click', function() {
            console.log('add item');

            addNewItem($form);
        });
    };


    // Add "Delete" button
    addDelBtn = function($item, $form) {
        var $actionBar = $item.find('.js-nested-form__item__header__actions'),
            $delBtn = $(delButtonHtml);

        $actionBar.append($delBtn);

        $delBtn.on('click', function() {
            console.log('del item');
        });
    };


    return {
        init: init
    };

}(window));
