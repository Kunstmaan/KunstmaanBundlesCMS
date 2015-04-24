var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pagepartEditor = (function(window, undefined) {

    var init,
        addPagePart, editPagePart, deletePagePart;

    var $body = $('body');

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
    };


    // Add
    addPagePart = function($select) {
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


    return {
        init: init
    };

}(window));
