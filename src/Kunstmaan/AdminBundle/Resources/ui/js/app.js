var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.app = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.toggle.init();

        kunstmaanbundles.sidebartoggle.init();
        kunstmaanbundles.sidebartree.init();
        kunstmaanbundles.filter.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    kunstmaanbundles.app.init();
});
