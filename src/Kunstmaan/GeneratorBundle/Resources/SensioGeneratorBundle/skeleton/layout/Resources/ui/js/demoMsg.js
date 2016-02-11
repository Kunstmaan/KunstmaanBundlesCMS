var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.demoMsg = (function($, window, undefined) {

    var init, initDemoMsg, hideDemoMsg;

    init = function() {
        initDemoMsg();
    };

    initDemoMsg = function() {
    var $hook = $('.js-demo-msg'),
        $btn = $hook.find('.js-toggle-btn'),
        $target = $($btn.data('target')),
        _hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)demosite\-message\s*\=\s*([^;]*).*$)|^.*$/)[1];

        if (typeof _hasCookie === 'undefined' || _hasCookie === 'false') {
            $target.addClass('toggle-item--active');

            setTimeout(function() {
                hideDemoMsg($btn, $target);
                document.cookie = 'demosite-message=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
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
