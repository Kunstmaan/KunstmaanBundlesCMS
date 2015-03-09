var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init, initForms, initDesktopSearch;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();

        forms.init();
        search.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    {{ bundle.getName() }}.init();
});
