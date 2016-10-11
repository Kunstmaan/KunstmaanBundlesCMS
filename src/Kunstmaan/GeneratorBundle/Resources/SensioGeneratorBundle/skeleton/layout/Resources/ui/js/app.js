var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.videolink.init();
        cargobay.scrollToTop.init();
        {{ bundle.getName() }}.cookieConsent.init();
{% if demosite %}
        cargobay.toggle.init();
        cargobay.sidebarToggle.init();
        {{ bundle.getName() }}.search.init();
        {{ bundle.getName() }}.demoMsg.init();
{% endif %}
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    {{ bundle.getName() }}.init();
});
