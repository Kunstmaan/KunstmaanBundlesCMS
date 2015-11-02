var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.appLoading = (function($, window, undefined) {

    var init,
        addLoading, addLoadingForms, removeLoading;

    var $body = $('body');

    init = function() {
        $('.js-add-app-loading').on('click', addLoading);
        $('.js-add-app-loading--forms').on('click', addLoadingForms);
    };

    addLoading = function() {
        $body.addClass('app--loading');
    };

    addLoadingForms = function() {
        var valid = $(this).parents('form')[0].checkValidity();

        if(valid) {
            addLoading();
        }
    };

    removeLoading = function() {
        $body.removeClass('app--loading');
    };

    return {
        init: init,
        addLoading: addLoading,
        removeLoading: removeLoading
    };

}(jQuery, window));
