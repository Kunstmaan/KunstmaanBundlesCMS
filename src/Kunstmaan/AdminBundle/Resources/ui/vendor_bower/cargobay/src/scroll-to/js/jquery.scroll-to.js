/* ==========================================================================
   Scroll To

   Initialize:
   cargobay.scrollTo.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.scrollTo = (function($, window, undefined) {

    var init,
	targetOffset,
	defaultOffset = 0,
	duration,
	defaultDuration = 300,
	$hook = $('.js-scroll-to');

    init = function() {
	$hook.on('click', function(e) {
	    e.preventDefault();

	    var $this = $(this),
		target = $this.attr('href'),
		dataOffset = $this.data('offset'),
		dataDuration = $this.data('animation-duration');

	    targetOffset = (typeof dataOffset !== undefined && !isNaN(dataOffset)) ? dataOffset : defaultOffset;
	    duration = (typeof dataDuration !== undefined && !isNaN(dataDuration)) ? dataDuration : defaultDuration;

	    $(target).velocity('scroll', {
		duration: duration,
		offset: targetOffset
	    });
	});
    };

    return {
	init: init
    };

}(jQuery, window));
