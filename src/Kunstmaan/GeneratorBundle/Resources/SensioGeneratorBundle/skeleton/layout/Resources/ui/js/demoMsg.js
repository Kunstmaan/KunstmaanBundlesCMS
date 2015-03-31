var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.demoMsg = (function($, window, undefined) {

    var init, initDemoMsg;

    init = function() {
        initDemoMsg();
    };

    initDemoMsg = function() {
        var $hook = $('.js-demo-msg'),
            $btn = $hook.find('.js-toggle-btn'),
            $target = $($btn.data('target'));

        if ($target.hasClass('toggle-item--active')) {
            setTimeout(function() {
                cargobay.toggle.hide($btn, $target);
            }, 10000);
        };
    };

    return {
        init: init
    };

}(jQuery, window));
