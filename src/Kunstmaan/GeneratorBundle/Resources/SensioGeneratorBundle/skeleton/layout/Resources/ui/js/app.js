var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init;

    init = function() {
        {{ bundle.getName() }}.cookieConsent.init();
{% if demosite %}
        cargobay.scrollToTop.init();
        cargobay.sidebarToggle.init();
        cargobay.toggle.init();
        cargobay.videolink.init();
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
