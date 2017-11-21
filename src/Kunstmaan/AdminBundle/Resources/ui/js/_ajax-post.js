var kunstmaanbundles = kunstmaanbundles || {};


kunstmaanbundles.ajaxPost = (function ($) {
    var init;

    init = function () {

        $(document).on('click', '.ajax-upload-media', function (e) {
            e.preventDefault();

            // get the form action url
            var formAction = $(this).parent().prop('action');

            // create a js form
            var jform = new FormData();

            jform.append('kunstmaan_mediabundle_filetype[name]', $('#kunstmaan_mediabundle_filetype_name').val());
            jform.append('kunstmaan_mediabundle_filetype[copyright]', $('#kunstmaan_mediabundle_filetype_copyright').val());
            jform.append('kunstmaan_mediabundle_filetype[description]', $('#kunstmaan_mediabundle_filetype_description').val());
            jform.append('kunstmaan_mediabundle_filetype[file]', $('#kunstmaan_mediabundle_filetype_file').get(0).files[0]);
            jform.append('kunstmaan_mediabundle_filetype[_token]', $('#kunstmaan_mediabundle_filetype__token').val());

            // post it via ajax
            $.ajax({
                url: formAction,
                type: 'POST',
                data: jform,
                dataType: 'json',
                mimeType: 'multipart/form-data',
                contentType: false,
                cache: false,
                processData: false,
                success: function (data, status, jqXHR) {
                    var json = jqXHR.responseJSON;
                    var $modal = $(window.frameElement).closest('.js-ajax-modal');
                    var modalId = $modal.attr('id');
                    var linkedInputId = $modal.data('linked-input-id');
                    var $previewImg = parent.$('#' + linkedInputId + '__preview__img');
                    var $previewTitle = parent.$('#' + linkedInputId + '__preview__title');
                    var $input = parent.$('#'+linkedInputId);

                    $previewImg.attr('src', json.url);
                    $previewImg.attr('style', 'height: 150px;');
                    $previewImg.parent().parent().removeClass('media-chooser__preview');
                    $previewTitle.html(json.title);
                    $input.val(json.id);

                    var $button = $input.parent().find('button.media-chooser__choose-btn');
                    $button.hide();

                    parent.$('#' + modalId).modal('hide');

                },

                error: function (jqXHR, status, error) {
                    // Hopefully we should never reach here
                    alert('Problem uploading media');
                },
            });
        });
    };

    return {
        init: init,
    };

})(jQuery, window);
