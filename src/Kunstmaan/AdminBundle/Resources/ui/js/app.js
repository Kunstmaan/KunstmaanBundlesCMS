var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.app = (function($, window, undefined) {

    var init;

    init = function() {
        kunstmaanbundles.sidebartoggle.init();
        kunstmaanbundles.sidebartree.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    kunstmaanbundles.app.init();
});
