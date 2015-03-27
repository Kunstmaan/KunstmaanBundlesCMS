var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init;

    init = function() {
{% if demosite %}
        cargobay.scrollToTop.init();
        cargobay.toggle.init();
        cargobay.sidebarToggle.init();
        cargobay.videolink.init();
        cargobay.cookieconsent.init();
        {{ bundle.getName() }}.search.init();
{% endif %}
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    {{ bundle.getName() }}.init();
});
