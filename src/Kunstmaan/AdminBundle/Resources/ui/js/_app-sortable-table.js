var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sortableTable = (function(window, undefined) {

    var init,
        goToUrl;

    init = function() {
        $('.js-sortable-link').on('click', function() {
            goToUrl($(this));
        });
    };

    goToUrl = function($this) {
        var url = $this.data('order-url');

        window.location = url;
    };

    return {
        init: init
    };

}(window));
