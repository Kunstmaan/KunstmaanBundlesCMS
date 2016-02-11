/* ==========================================================================
   Scroll to top

   Initialize:
   cargobay.scrollToTop.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.scrollToTop = (function($, window, undefined) {

    var init,
	duration,
	defaultDuration = 300,
	$hook = $('.js-scroll-to-top');

    init = function() {
	$hook.on('click', function(e) {
	    e.preventDefault();

	    var $this = $(this),
		dataDuration = $this.data('animation-duration');

	    duration = (typeof dataDuration !== undefined && !isNaN(dataDuration)) ? dataDuration : defaultDuration;

	    $('html, body').animate({scrollTop: 0}, duration);
	});
    };

    return {
	init: init
    };

}(jQuery, window));
