var {{ websitetitle|lower|replace({' ':''}) }} = {{ websitetitle|lower|replace({' ':''}) }} || {};

{{ websitetitle|lower|replace({' ':''}) }} = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    {{ websitetitle|lower|replace({' ':''}) }}.init();
});
