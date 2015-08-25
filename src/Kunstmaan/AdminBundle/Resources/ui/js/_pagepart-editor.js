var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pagepartEditor = (function(window) {

    var init, addPagePart, editPagePart, deletePagePart, movePagePartUp, movePagePartDown;

    init = function() {
        var $body = $('body');

        // Add
        $body.on('change', '.js-add-pp-select', function() {
            addPagePart($(this));
        });

        // Edit
        $body.on('click', '.js-edit-pp-btn', function() {
            editPagePart($(this));
        });

        // Del
        $body.on('click', '.js-delete-pp-btn', function() {
            deletePagePart($(this));
        });

        // Move up
        $body.on('click', '.js-move-up-pp-btn', function() {
            movePagePartUp($(this));
        });

        // Move down
        $body.on('click', '.js-move-down-pp-btn', function() {
            movePagePartDown($(this));
        });
    };


    // Add
    addPagePart = function($select) {
        if (!$select.val()) {
            return false;
        }

        var $targetContainer = $select.closest('.js-pp-container'),
            requestUrl = $select.data('url');

        // Get necessary data
        var pageClassName = $targetContainer.data('pageclassname'),
            pageId = $targetContainer.data('pageid'),
            context = $targetContainer.data('context'),
            ppType = $select.val();

        // Set Loading
        kunstmaanbundles.appLoading.addLoading();

        // Ajax Request
        $.ajax({
            url: requestUrl,
            data: {
                'pageclassname': pageClassName,
                'pageid': pageId,
                'context': context,
                'type': ppType
            },
            async: true,
            success: function (data) {
                // Add PP
                var firstSelect = $select.hasClass('js-add-pp-select--first');
                if (firstSelect) {
                    $('#parts-' + context).prepend(data);
                } else {
                    $select.closest('.js-sortable-item').after(data);
                }

                // Remove Loading
                kunstmaanbundles.appLoading.removeLoading();

                // Enable leave-page modal
                kunstmaanbundles.checkIfEdited.edited();

                // Enable new Rich Editors
                kunstmaanbundles.richEditor.init();

                // Init new tooltips
                kunstmaanbundles.tooltip.init();

                // Init new colorpickers
                kunstmaanbundles.colorpicker.init();

                // Reinit custom selects
                kunstmaanbundles.advancedSelect.init();

                // Reinit nested forms
                kunstmaanbundles.nestedForm.init();

                // Rest ajax-modals
                kunstmaanbundles.ajaxModal.resetAjaxModals();

                // Reinitialise the datepickers
                kunstmaanbundles.datepicker.reInitialise();
            }
        });

        // Reset select
        $select.val('');
    };


    // Edit
    editPagePart = function($btn) {
        var targetId = $btn.data('target-id');

        // Enable "leave page" modal
        kunstmaanbundles.checkIfEdited.edited();

        // Show edit view and hide preview
        $('#' + targetId + '-edit-view').removeClass('pp__view__block--hidden');
        $('#' + targetId + '-preview-view').addClass('pp__view__block--hidden');

        // Add edit active class
        $('#pp-' + targetId).addClass('pp--edit-active');

        // Reinit custom selects
        kunstmaanbundles.advancedSelect.init();

        // Set Active Edit
        window.activeEdit = targetId;
    };


    // Delete
    deletePagePart = function($btn) {
        var targetId = $btn.data('target-id'),
            $container = $('#' + targetId + '-pp-container');

        // Enable "leave page" modal
        kunstmaanbundles.checkIfEdited.edited();

        // Slideup and empty container
        $container.velocity('slideUp', {
            duration: 300
        });

        $container.empty();

        // Check is-deleted checkbox
        $('#' + targetId + '-is-deleted').prop('checked', true);

        // Hide delete modal
        $('#delete-pagepart-modal-' + targetId).modal('hide');
        $('body').removeClass('modal-open');
    };


    // Move up
    movePagePartUp = function($btn) {
        var targetId = $btn.data('target-id');

        var currentPp = $('#' + targetId + '-pp-container');
        var previousPp = currentPp.prevAll('.sortable-item:first');
        if (previousPp.length) {
            $(previousPp).before(currentPp);

            // Enable "leave page" modal
            kunstmaanbundles.checkIfEdited.edited();
        }

        // Set Active Edit
        window.activeEdit = targetId;
    };


    // Move down
    movePagePartDown = function($btn) {
        var targetId = $btn.data('target-id');

        var currentPp = $('#' + targetId + '-pp-container');
        var nextPp = currentPp.nextAll('.sortable-item:first');
        if (nextPp.length) {
            $(nextPp).after(currentPp);

            // Enable "leave page" modal
            kunstmaanbundles.checkIfEdited.edited();
        }

        // Set Active Edit
        window.activeEdit = targetId;
    };


    return {
        init: init
    };

}(window));
