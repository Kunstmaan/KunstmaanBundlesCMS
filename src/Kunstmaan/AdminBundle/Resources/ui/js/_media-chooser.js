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

    // Del btn
    initCropBtn = function () {
        $body.on('click', '.js-media-chooser-crop-preview-btn', function (element) {
            var $this = $(this),
                linkedID = $this.data('linked-id'),
                ppRefId = $this.data('pp-ref-id'),
                $mediaCropperModal = $('#' + linkedID + '-mediaCropperModal');

            $mediaCropperModal.modal('show');

            var clicks = [];

            $mediaCropperModal.on('click', '.thumbnail', function (e) {
                var offset_t = $(this).offset().top - $(window).scrollTop();
                var offset_l = $(this).offset().left - $(window).scrollLeft();

                var left = Math.round((e.clientX - offset_l));
                var top = Math.round((e.clientY - offset_t));

                clicks.push([left, top]);

                console.log(clicks);
                if (clicks.length >= 2) {
                    var cropUrl = $mediaCropperModal.find('.crop-path-container').data('crop-url');
                    var start = clicks[0].join();
                    var end = clicks[1].join();
                    $.ajax({
                        url: cropUrl,
                        type: 'GET',
                        data: {'start': start, 'end': end, 'pp_ref_id': ppRefId},
                        success: function (response) {
                            alert('yay cropped image');
                        }
                    });
                }
            });
        });
    };

    return {
        init: init
    };

})(window);
