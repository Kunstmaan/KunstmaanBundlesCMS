var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.search = (function($, window, undefined) {

    var init, initSearch;

    init = function() {
        initSearch();
    };

    initSearch = function() {
        $('.js-searchbox').on('click', function() {
            $(this).addClass('searchbox--active');
        });
    };

    return {
        init: init
    };

}(jQuery, window));
