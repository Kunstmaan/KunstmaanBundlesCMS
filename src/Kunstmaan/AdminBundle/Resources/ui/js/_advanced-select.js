var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.advancedSelect = (function(window, undefined) {

    var init;

    init = function() {
        $('.js-advanced-select').select2();
    };

    return {
        init: init
    };

}(window));
