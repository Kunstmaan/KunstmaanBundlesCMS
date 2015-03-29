/* ==========================================================================
   Cookie Consent

   Initialize:
   cargobay.cookieConsent.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.cookieConsent = (function($, window, undefined) {

    var init;

    init = function() {
	var $cookieBar = $('#cookie-bar'),
	    $cookieBarConsentBtn = $('#cookie-bar__consent-btn'),
	    _hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)cargobay\-cookie\-consent\s*\=\s*([^;]*).*$)|^.*$/)[1];

	if (typeof _hasCookie === 'undefined' || _hasCookie === 'false') {
	    $cookieBar.addClass('cookie-bar--visible');
	}

	$cookieBarConsentBtn.on('click', function(e){
	    e.preventDefault();
	    document.cookie = 'cargobay-cookie-consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
	    $cookieBar.removeClass('cookie-bar--visible');
	});
    };

    return {
	init: init
    };

}(jQuery, window));
