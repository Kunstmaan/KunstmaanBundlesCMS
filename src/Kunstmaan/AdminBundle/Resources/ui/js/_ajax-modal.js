var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.ajaxModal = (function($, window, undefined) {

    var init;

    init = function() {
        $('.js-ajax-modal').on('show.bs.modal', function(e) {
            var $modal = $(this),
                $btn = $(e.relatedTarget),
                link = $btn.data('link');

            if(!$modal.data('loaded')) {
                $modal.data('loaded', true);
                $modal.find('.js-ajax-modal-body').append('<iframe class="ajax-modal__body__iframe" frameborder="0" src="' + link + '"></iframe>');
            }
        });
    };


    return {
        init: init
    };

}(jQuery, window));
