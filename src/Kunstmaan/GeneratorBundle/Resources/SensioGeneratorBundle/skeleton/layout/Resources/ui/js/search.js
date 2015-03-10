var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.search = (function($, window, undefined) {

    var init, initSearch;

    init = function() {
        initSearch();
    };

    initSearch = function() {
        $('.js-searchbox-form').on('click', function(e) {
            e.stopPropagation();
            $(this).addClass('searchbox-form--active');
        });
        $(document).on('click', function() {
            $('.js-searchbox-form').removeClass('searchbox-form--active');
        });
        $('.js-searchbox-back').on('click', function() {
            $(this).closest('.js-searchbox-form').removeClass('searchbox-form--active');
            console.log("ja");
        });
    };

    return {
        init: init
    };

}(jQuery, window));
