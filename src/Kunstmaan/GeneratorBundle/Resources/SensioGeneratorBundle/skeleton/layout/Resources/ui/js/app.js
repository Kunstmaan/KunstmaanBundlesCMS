var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();

        {{ bundle.getName() }}.forms.init();
        {{ bundle.getName() }}.search.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    {{ bundle.getName() }}.init();
});
