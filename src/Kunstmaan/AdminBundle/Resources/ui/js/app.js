var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.app = (function($, window, undefined) {

    var init, appScroll, initModules,
        $mainActions = $('#page-main-actions-top');


    // General App init
    init = function() {
        cargobay.toggle.init();
        cargobay.scrollToTop.init();

        appScroll();

        kunstmaanbundles.sidebartoggle.init();
        kunstmaanbundles.sidebartree.init();
        kunstmaanbundles.urlchoosertree.init();
        kunstmaanbundles.sidebarsearchfocus.init();
        kunstmaanbundles.filter.init();
        kunstmaanbundles.sortableTable.init();
        kunstmaanbundles.checkIfEdited.init();
        kunstmaanbundles.preventDoubleClick.init();
        kunstmaanbundles.autoCollapseButtons.init();
        kunstmaanbundles.autoCollapseTabs.init();
        kunstmaanbundles.ajaxModal.init();
        kunstmaanbundles.topNav.init();

        initModules();

        kunstmaanbundles.pageEditor.init();
        kunstmaanbundles.pagepartEditor.init();

        kunstmaanbundles.slugChooser.init();
        kunstmaanbundles.urlChooser.init();
        kunstmaanbundles.mediaChooser.init();
        kunstmaanbundles.iconChooser.init();
        kunstmaanbundles.bulkActions.init();
        kunstmaanbundles.appLoading.init();
        kunstmaanbundles.colorpicker.init();
        kunstmaanbundles.charactersLeft.init();
        kunstmaanbundles.rangeslider.init();
        kunstmaanbundles.googleOAuth.init();
        kunstmaanbundles.appNodeVersionLock.init();
        kunstmaanbundles.appEntityVersionLock.init();
    };

    initModules = function() {
        // Init new rich editors
        kunstmaanbundles.richEditor.init();
        // Init new nested forms
        kunstmaanbundles.nestedForm.init();
        // Init new selects.
        kunstmaanbundles.advancedSelect.init();
        // Init new tooltips
        kunstmaanbundles.tooltip.init();
        // Init new colorpickers
        kunstmaanbundles.colorpicker.init();
        // Init new datepickers
        kunstmaanbundles.datepicker.reInit();
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
        init: init,
        initModules: initModules
    };

})(jQuery, window);

$(function() {
    kunstmaanbundles.app.init();
});
