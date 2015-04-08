var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.preventDoubleClick = (function($, window, undefined) {

    var init, _checkDoubleClick;


    init = function() {
        $('.js-prevent-double-click').on('click', function(e) {
            e.preventDefault();

            _checkDoubleClick($(this));
        });
    };


    _checkDoubleClick = function($link) {
        if ($link.hasClass('click-disabled')) {
            return false;
        }

        $link.addClass('click-disabled');

        window.location = $link.attr('href');
    };


    return {
        init: init
    };

}(jQuery, window));
