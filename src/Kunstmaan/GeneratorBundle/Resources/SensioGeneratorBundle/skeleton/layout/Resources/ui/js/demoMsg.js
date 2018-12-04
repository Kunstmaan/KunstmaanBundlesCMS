export default function init() {
    const $hook = $('.js-demo-msg');
    const $btn = $hook.find('.js-toggle-btn');
    const $target = $($btn.data('target'));
    const hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)demosite-message\s*=\s*([^;]*).*$)|^.*$/)[1];

    if (typeof hasCookie === 'undefined' || hasCookie === 'false') {
        $target.addClass('toggle-item--active');

        setTimeout(() => {
            hideDemoMsg($btn, $target);
            document.cookie = 'demosite-message=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
        }, 10000);
    }
}

function hideDemoMsg($btn, $target) {
    $target.velocity({
        height: 0,
    }, {
        duration: 200,
        complete: () => {
            $btn.removeClass('toggle-btn--active');
        },
    });

    $target.removeClass('toggle-item--active');
}
