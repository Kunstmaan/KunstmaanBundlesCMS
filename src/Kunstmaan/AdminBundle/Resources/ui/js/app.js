var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.app = (function($, window, undefined) {

    var init,
        $mainActions = $('#page-main-actions-top');

    init = function() {
        cargobay.toggle.init();

        appScroll();

        kunstmaanbundles.sidebartoggle.init();
        kunstmaanbundles.sidebartree.init();
        kunstmaanbundles.filter.init();
        kunstmaanbundles.sortableTable.init();
        kunstmaanbundles.checkIfEdited.init();
        kunstmaanbundles.preventDoubleClick.init();

        kunstmaanbundles.pageEditor.init();
        kunstmaanbundles.pagepartEditor.init();
    };


    // On Scroll
    appScroll = function() {
        if($mainActions) {
            var _onScroll, _requestTick, _update,
                latestKnownScrollY = 0,
                ticking = false;

            _onScroll = function() {
                latestKnownScrollY = window.pageYOffset;
                _requestTick();
            };

            _requestTick = function() {
                if(!ticking) {
                    window.requestAnimationFrame(_update);
                }
                ticking = true;
            };

            _update = function() {
                ticking = false;

                var currentScrollY = latestKnownScrollY;

                kunstmaanbundles.mainActions.updateScroll(currentScrollY, $mainActions);
            };

            window.onscroll = function(e) {
                _onScroll();
            };
        }

    };


    return {
        init: init
    };

}(jQuery, window));

$(function() {
    kunstmaanbundles.app.init();
});
