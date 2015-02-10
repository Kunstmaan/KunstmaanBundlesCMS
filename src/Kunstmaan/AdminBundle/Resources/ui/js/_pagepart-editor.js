var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.pagepartEditor = (function(window, undefined) {

    var init,
        addPagePart, editPagePart, deletePagePart;

    init = function() {
        $('.js-edit-pagepart-btn').on('click', function() {
            editPagePart($(this));
        })
    };


    // Add
    addPagePart = function() {

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
        $(targetId + '-edit-view').show();
        $(targetId + '-preview-view').hide();

        // Set Active Edit
        window.activeEdit = targetId;

        // OLD
        // editPagepart: function (id) {
        //     isEdited = true; // enabling the "leave page" popup
        //     document.getElementById(id + '_edit').style.display = '';
        //     document.getElementById(id + '_view').style.display = 'none';
        //     window.activeEdit = id;
        //     return false;
        // }
    };


    // Delete
    deletePagePart = function() {

        // OLD
        // deletePagepart: function (id) {
        //     isEdited = true; // enabling the "leave page" popup
        //     document.getElementById(id + '_deleted').checked = 'checked';
        //     var container = $('#' + id + '_container');
        //     container.slideUp(function () {
        //         container.html('');
        //     });
        //     return true;
        // }
    };


    return {
        init: init
    };

}(window));
