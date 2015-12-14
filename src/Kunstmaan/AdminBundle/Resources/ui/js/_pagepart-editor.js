var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pagepartEditor = (function(window) {

    var events = {
        add: [],
        edit: [],
        delete: []
    };

    var init, addPagePart, editPagePart, deletePagePart, movePagePartUp, movePagePartDown, subscribeToEvent, unSubscribeToEvent, executeEvent;

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
                var elem;
                if (firstSelect) {
                    elem = $('#parts-' + context).prepend(data);
                } else {
                    elem = $select.closest('.js-sortable-item').after(data);
                }

                // Create a temporary node of the new PP
                var $temp = $('<div>');
                $temp.append(data);

                // Check if some javascript needs to be reinitialised for this PP
                $temp.find('*[data-reinit-js]').each(function() {
                    // Get modules from data attribute
                    var modules = $(this).data('reinit-js');

                    if (modules) {
                        for (var i = 0; i < modules.length; i++) {
                            // Check if there really is a module with the given name and it if has a public reInit function
                            if (typeof kunstmaanbundles[modules[i]] === 'object' && typeof kunstmaanbundles[modules[i]].reInit === 'function') {
                                kunstmaanbundles[modules[i]].reInit();
                            }
                        }
                    }
                });

                // Remove Loading
                kunstmaanbundles.appLoading.removeLoading();

                // Enable leave-page modal
                kunstmaanbundles.checkIfEdited.edited();

                // Reinit custom selects
                kunstmaanbundles.advancedSelect.init();

                // Reset ajax-modals
                kunstmaanbundles.ajaxModal.resetAjaxModals();

                executeEvent('add')
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
        var container = $('#pp-' + targetId);
        container.addClass('pp--edit-active');

        // Reinit custom selects
        kunstmaanbundles.advancedSelect.init();

        // Set Active Edit
        window.activeEdit = targetId;

        executeEvent('edit', container);
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
        executeEvent('delete', $container);
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
    // subsribe to an event.
    subscribeToEvent = function(eventName, callBack) {
        if (!events.hasOwnProperty(eventName)) {
            throw new Error("PagePartEditor: I cannot let you subscribe to the unknown event named: " + eventName);
        }
        events[eventName].push(callBack);
    };
    unSubscribeToEvent = function(eventName, callback) {
        if (!events.hasOwnProperty(eventName)) {
            throw new Error("PagePartEditor: I cannot let you unSubscribe to the unknown event named: " + eventName);
        }
        events = events.filter(function(cb) { return cb !== callback});
    };
    executeEvent = function(eventName, target) {
        events[eventName].forEach(function(cb) {
            cb({target: target})
        })
    };
    return {
        init: init,
        subscribeToEvent: subscribeToEvent,
        unSubscribeToEvent: unSubscribeToEvent
    };

}(window));
