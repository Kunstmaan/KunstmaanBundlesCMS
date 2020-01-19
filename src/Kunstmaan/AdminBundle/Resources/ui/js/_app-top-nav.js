var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.topNav = (function($, window, undefined) {

    var $navBar, collapseClass,
        init, initAutoCollapse, doCheck;

    collapseClass = 'collapsed';

    init = function() {
        initAutoCollapse();
    };

    initAutoCollapse = function() {
        $navBar = $('.js-app-top-nav');

        doCheck();

        $(window).on('resize', function() {
            doCheck();
        });
    };

    doCheck = function() {
        $navBar.removeClass(collapseClass);

        var navBarHeight = $navBar.height();
        var singleNavBarItemHeight = $navBar.find('li:first-child').innerHeight();


        if (navBarHeight > singleNavBarItemHeight + 5) { // allow error margin of 5px
            $navBar.addClass(collapseClass);
        } else {
            $navBar.removeClass(collapseClass);
        }
    };

    return {
        init: init
    };

})(jQuery, window);
