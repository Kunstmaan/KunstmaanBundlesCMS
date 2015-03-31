var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.demoMsg = (function($, window, undefined) {

    var init, initDemoMsg, hideDemoMsg;

    init = function() {
        initDemoMsg();
    };

    initDemoMsg = function() {
        var $hook = $('.js-demo-msg'),
            $btn = $hook.find('.js-toggle-btn'),
            $target = $($btn.data('target'));

        if ($target.hasClass('toggle-item--active')) {
            setTimeout(function() {
                hideDemoMsg($btn, $target);
            }, 10000);
        }
    };

    hideDemoMsg = function($btn, $target) {
        $target.velocity({
            height: 0
        }, {
            duration: 200,
            complete: function() {
                $btn.removeClass('toggle-btn--active');
            }
        });

        $target.removeClass('toggle-item--active');
    };

    return {
        init: init
    };

}(jQuery, window));
