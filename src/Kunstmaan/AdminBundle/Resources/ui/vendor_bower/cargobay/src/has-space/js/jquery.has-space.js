/* ==========================================================================
   Has Space

   Initialize:
   cargobay.hasSpace.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.hasSpace = (function($, window, undefined) {

    var init, calcSpace, toggleState,
	containerClass = '.js-has-space',
	containerItemClass = '.js-has-space__item';

    init = function() {
	calcSpace();
    };

    calcSpace = function() {
	$(containerClass).each(function() {
	    var $this = $(this),
		spaceHook = $this.data('space-hook-target'),
		enoughSpaceWidth = 0;

	    $this.find(containerItemClass).each(function() {
		if (!$(this).hasClass('js-has-space__item--hidden')) {
		    enoughSpaceWidth += parseInt($(this).outerWidth(), 10);
		}
	    });

	    toggleState($this, spaceHook, enoughSpaceWidth);

	    $(window).on('resize', function() {
		toggleState($this, spaceHook, enoughSpaceWidth);
	    });
	});
    };

    toggleState = function(currentContainer, currentSpaceHook, currentEnoughSpaceWidth) {
	var currentAvailableSpaceWidth = $(currentSpaceHook).width() - ($(currentSpaceHook).outerWidth() - $(currentSpaceHook).width());

	if (currentEnoughSpaceWidth > currentAvailableSpaceWidth) {
	    currentContainer.addClass('has-space--no-space')
			    .removeClass('has-space--space');
	} else {
	    currentContainer.addClass('has-space--space')
			    .removeClass('has-space--no-space');
	}
    };

    return {
	init: init
    };

}(jQuery, window));
