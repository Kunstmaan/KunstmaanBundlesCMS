var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pagepartEditor = (function(window, undefined) {

    var init,
        addPagePart, editPagePart, deletePagePart;

    init = function() {
        // Add
        $('.js-add-pp-select').on('change', function() {
            addPagePart($(this));
        });

        // Edit
        $('.js-edit-pp-btn').on('click', function() {
            editPagePart($(this));
        });

        // Del
        $('.js-delete-pp-btn').on('click', function() {
            deletePagePart($(this));
        });
    };


    // Add
    addPagePart = function($select) {
        var $targetContainer = $select.closest('.js-pp-container'),
            url = $select.data('url');

        // Get necessary data
        var pageClassName = $targetContainer.data('pageclassname'),
            pageId = $targetContainer.data('pageid'),
            context = $targetContainer.data('context'),
            ppType = $select.val();

        // Ajax Request


        // Reset select
        $select.val('');

        // OLD
        // addPagepart: function (select) {
        //     pagepartscontainer = $(select).closest('.pagepartscontainer');

        //     $.ajax({
        //         url: '{{ path('KunstmaanPagePartBundle_admin_newpagepart') }}',
        //         data: {
        //             'pageclassname': pagepartscontainer.data('pageclassname'),
        //             'pageid': pagepartscontainer.data('pageid'),
        //             'context': pagepartscontainer.data('context'),
        //             'type': $(select).val()
        //         },
        //         async: true,
        //         success: function (data) {
        //             var result = null;
        //             if ($(select).parent().hasClass('first')) {
        //                 result = $('#parts_' + pagepartscontainer.data('context')).prepend(data);
        //             } else {
        //                 result = $(select).closest('section').after(data);
        //             }
        //             result.find('.prop_bar').mousedown(PagePartEditor.propBarMouseDownHandler);
        //             disableCKEditors();
        //             enableCKEditors();
        //             initCustomSelect();
        //         }
        //     });
        //     $(select).val('');
        //     return false;
        // }
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
