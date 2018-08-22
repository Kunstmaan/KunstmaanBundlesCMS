var kunstmaanTranslatorBundle = kunstmaanTranslatorBundle || {};

kunstmaanTranslatorBundle.app = (function($, window, undefined) {

    var init;

    init = function() {
        kunstmaanTranslatorBundle.inlineEdit.init();
    };

    return {
        init: init
    };

}(jQuery, window));


$(function() {
    kunstmaanTranslatorBundle.app.init();
});
