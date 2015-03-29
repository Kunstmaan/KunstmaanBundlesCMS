/* ==========================================================================
   Cookie Consent

   Initialize:
   cargobay.cookieConsent.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE10 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.cookieConsent = (function(window, undefined) {

    var init;

    init = function() {
	var cookieBar = document.getElementById('cookie-bar'),
	    cookieBarConsentBtn = document.getElementById('cookie-bar__consent-btn'),
	    _hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)cargobay\-cookie\-consent\s*\=\s*([^;]*).*$)|^.*$/)[1];

	if (typeof _hasCookie === 'undefined' || _hasCookie === 'false') {
	    cookieBar.classList.add('cookie-bar--visible');
	}

	cookieBarConsentBtn.addEventListener('click', function(e) {
	    e.preventDefault();
	    document.cookie = 'cargobay-cookie-consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
	    cookieBar.classList.remove('cookie-bar--visible');
	});
    };

    return {
	init: init
    };

}(window));
