const search = (function() {

    var init, initSearch;

    init = function() {
        initSearch();
    };

    initSearch = function() {
        $('.js-searchbox-content').on('click', function(e) {
            e.stopPropagation();
            $(this).closest('.js-searchbox-form').addClass('searchbox-form--active');
        });

        $(document).on('click', function() {
            $('.js-searchbox-form').removeClass('searchbox-form--active');
        });

        $('.js-searchbox-back').on('click', function() {
            $(this).closest('.js-searchbox-form').removeClass('searchbox-form--active');
        });
    };

    return {
        init: init
    };
}());

module.exports = search;