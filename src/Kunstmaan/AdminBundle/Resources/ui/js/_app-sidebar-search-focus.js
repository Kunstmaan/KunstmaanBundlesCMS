var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.sidebarsearchfocus = (function(window, undefined) {

    var init,
    focus;

    init = function() {
        focus();
    };

    focus = function() {
        var $toggleButton = $('.app__sidebar__search-toggle-btn'),
            $searchInput = $('#app__sidebar__search');

        $toggleButton.on('click touchstart mousedown', function(e) {
            e.preventDefault();
        }).on('touchend mouseup', function() {
           $searchInput.focus();
        });
    };

    return {
        init: init
    };

})(window);
