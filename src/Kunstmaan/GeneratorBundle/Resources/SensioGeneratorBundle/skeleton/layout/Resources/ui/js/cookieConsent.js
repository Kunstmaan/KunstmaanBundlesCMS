var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.cookieConsent = (function($, window, undefined) {

    var init;

    init = function() {

        var cookieBar = document.getElementById('cookie-bar'),
            cookieBarConsentBtn = document.getElementById('cookie-bar__consent-btn'),
            _hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)bundles\-cookie\-consent\s*\=\s*([^;]*).*$)|^.*$/)[1];

        if (typeof _hasCookie === 'undefined' || _hasCookie === 'false') {
            cookieBar.classList.add('cookie-bar--visible');
        }

        cookieBarConsentBtn.addEventListener('click', function(e) {
            e.preventDefault();
            document.cookie = 'bundles-cookie-consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            cookieBar.classList.remove('cookie-bar--visible');
        });

    };



    return {
        init: init
    };

}(jQuery, window));
