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

            //window.open(link, "_blank", "toolbar=yes, scrollbars=yes, resizable=yes, top=500, left=500, width=400, height=400");
        });
    };


    return {
        init: init
    };

}(jQuery, window));
