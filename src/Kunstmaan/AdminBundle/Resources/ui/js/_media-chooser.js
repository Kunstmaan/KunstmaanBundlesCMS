var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.mediaChooser = (function (window, undefined) {

    var init, initDelBtn, initCropBtn;

    var $body = $('body');

    init = function () {
        // Save and update preview can be found in url-chooser.js
        initDelBtn();
        initCropBtn();
    };


    // Del btn
    initDelBtn = function () {
        $body.on('click', '.js-media-chooser-del-preview-btn', function (e) {
            var $this = $(this),
                linkedID = $this.data('linked-id'),
                $widget = $('#' + linkedID + '-widget'),
                $input = $('#' + linkedID);

            $this.parent('.media-chooser__preview').find('.media-chooser__preview__img').attr({
                'src': '',
                'srcset': '',
                'alt': ''
            });

            $(".media-thumbnail__icon").remove();

            $widget.removeClass('media-chooser--choosen');
            $input.val('');

            if (typeof $this.data('clear-image-edit') !== 'undefined') {
                var linkedID = $this.data('linked-id');
                var $mediaCropperModal = $('#' + linkedID + '-image-edit-modal');
                var imageEdit = $mediaCropperModal.find('.js-image-edit');
                var imageEditImage = $mediaCropperModal.find('.js-image-edit-image');
                var destroyEvent = new CustomEvent('destroy');

                imageEditImage.attr({
                    'src': '',
                    'srcset': '',
                    'alt': ''
                });

                imageEdit[0].dispatchEvent(destroyEvent);
            }
        });
    };

    // Crop btn
    initCropBtn = function () {
        $body.on('click', '.js-media-chooser-image-edit-btn', function () {
            var $this = $(this),
                linkedID = $this.data('linked-id'),
                $mediaCropperModal = $('#' + linkedID + '-image-edit-modal');

            $mediaCropperModal.modal('show');

            $mediaCropperModal.on('shown.bs.modal', function() {
                document.dispatchEvent(new CustomEvent('modalOpen', {detail: $mediaCropperModal[0]}));
            });
        });
    };

    return {
        init: init
    };

})(window);
