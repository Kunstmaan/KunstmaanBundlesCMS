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

cargobay.scrollTo = (function(window, undefined) {

    var init, animateScroll,
	start, currentTime, change,
	to = 0,
	targetOffset,
	defaultOffset = 0,
	duration,
	defaultDuration = 300,
	increment = 20;

    init = function() {
	[].forEach.call( document.querySelectorAll('.js-scroll-to'), function(el) {
	    el.addEventListener('click', function(e) {
		e.preventDefault();

		var target = this.getAttribute('href').slice(+1),
		    dataOffset = this.getAttribute('data-offset'),
		    dataDuration = this.getAttribute('data-animation-duration'),
		    targetTop;

		targetOffset = (typeof dataOffset !== undefined && !isNaN(dataOffset)) ? dataOffset : defaultOffset;

		duration = (typeof dataDuration !== undefined && !isNaN(dataDuration)) ? dataDuration : defaultDuration;

		targetEl = document.getElementById(target);
		targetTop = targetEl.offsetTop - targetOffset;

		start = document.documentElement.scrollTop || document.body.scrollTop;
		change = targetTop - start;
		currentTime = 0;

		animateScroll();
	    }, false);
	});
    };

    animateScroll = function() {
	currentTime += increment;

	var val = Math.easeInOutQuad(currentTime, start, change, duration);

	document.body.scrollTop = document.documentElement.scrollTop = val;

	if (currentTime < duration) {
	    setTimeout(animateScroll, increment);
	}
    };

    Math.easeInOutQuad = function(t, b, c, d) {
	t /= d/2;
	if (t < 1) {
	    return c/2*t*t + b;
	}
	t--;
	return -c/2 * (t*(t-2) - 1) + b;
    };

    return {
	init: init
    };

}(window));
