var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }} = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.videolink.init();        
        cargobay.scrollToTop.init();
{% if demosite %}
        cargobay.toggle.init();
        cargobay.sidebarToggle.init();
        cargobay.cookieConsent.init();
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
