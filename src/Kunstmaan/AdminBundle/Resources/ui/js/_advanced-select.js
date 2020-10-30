var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.advancedSelect = (function(window, undefined) {

    var init;

    init = function() {
        $('.js-advanced-select').select2({
            closeOnSelect: false
        });
    };

    return {
        init: init
    };

})(window);
