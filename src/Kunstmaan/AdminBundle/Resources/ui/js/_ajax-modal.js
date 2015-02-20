var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.ajaxModal = (function($, window, undefined) {

    var init,
        initModals, resetAjaxModals;

    init = function() {
        $('.js-ajax-modal').on('show.bs.modal', initModals);
    };


    initModals = function(e) {
        console.log('boeja');

        var $modal = $(this),
            $btn = $(e.relatedTarget),
            link = $btn.data('link');

        if(!$modal.data('loaded')) {
            $modal.data('loaded', true);
            $modal.find('.js-ajax-modal-body').append('<iframe class="ajax-modal__body__iframe" frameborder="0" src="' + link + '" width="100%" height="100%" scrolling="auto"></iframe>');
        }
    }


    resetAjaxModals = function() {
        $('.js-ajax-modal').off('show.bs.modal', initModals);
        $('.js-ajax-modal').on('show.bs.modal', initModals);
    };


    return {
        init: init,
        resetAjaxModals: resetAjaxModals
    };

}(jQuery, window));
