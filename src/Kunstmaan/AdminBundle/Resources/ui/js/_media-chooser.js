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
        });
    };

    // Crop btn
    initCropBtn = function () {
        $body.on('click', '.js-media-chooser-crop-preview-btn', function (element) {
            var $this = $(this),
                linkedID = $this.data('linked-id'),
                $mediaCropperModal = $('#' + linkedID + '-mediaCropperModal');

            $mediaCropperModal.modal('show');

            $mediaCropperModal.on('shown.bs.modal', function() {
                $this[0].dispatchEvent(new CustomEvent('modalOpen', {detail: $mediaCropperModal[0]}));
            });
        });
    };

    return {
        init: init
    };

})(window);
