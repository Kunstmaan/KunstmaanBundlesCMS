var kunstmaanMediaBundle = kunstmaanMediaBundle || {};

kunstmaanMediaBundle.bulkMove = (function(window, undefined) {

    var init;

    init = function() {
        // Get values and elements
        var $form = $('#bulk-move-modal-form'),
            $mediaInput = $('#kunstmaan_mediabundle_folder_bulk_move_media');

        $form.submit(function(e) {
            e.preventDefault();

            var action = e.currentTarget.action;
            var method = e.currentTarget.method;
            var selectedMedia = $.map($('.js-bulk-move-media'), function (el) {
                if ($(el).is(":checked")) {
                    return $(el).data('media-id');
                }
            });
            $mediaInput.val(selectedMedia);

            $.ajax({
                url: action,
                type: method,
                data: $form.serialize(),
                success: function() {
                    window.location.reload(true);
                }
            });

        });

        $(document).on("change",".js-bulk-move-media",function () {
            $(this).parent().prev('.media-thumbnail').toggleClass('bulk_selected');
        });
    };

    return {
        init: init
    };

}(window));
