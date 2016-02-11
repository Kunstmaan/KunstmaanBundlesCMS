/* ==========================================================================
   Has Space

   Initialize:
   cargobay.hasSpace.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE10 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.hasSpace = (function(window, undefined) {

    var init, calcSpace, toggleState,
	containerClass = '.js-has-space',
	containerItemClass = '.js-has-space__item';

    init = function() {
	calcSpace();
    };

    calcSpace = function() {
	[].forEach.call( document.querySelectorAll(containerClass), function(container) {
	    var spaceHook = container.getAttribute('data-space-hook-target'),
		enoughSpaceWidth = 0;

	    [].forEach.call( container.querySelectorAll(containerItemClass), function(item) {
		if (!item.classList.contains('js-has-space__item--hidden')) {
		    enoughSpaceWidth += parseInt(item.offsetWidth, 10);
		}
	    });

	    toggleState(container, spaceHook, enoughSpaceWidth);

	    window.addEventListener('resize', function(e) {
		toggleState(container, spaceHook, enoughSpaceWidth);
	    });
	});
    };

    toggleState = function(currentContainer, currentSpaceHook, currentEnoughSpaceWidth) {
	var currentAvailableSpaceWidth = 0,
	    currentSpaceHookStyles = window.getComputedStyle(document.querySelectorAll(currentSpaceHook)[0], null);

	currentAvailableSpaceWidth = parseInt(currentSpaceHookStyles.getPropertyValue("width"), 10) - (parseInt(currentSpaceHookStyles.getPropertyValue("padding-left"), 10) + parseInt(currentSpaceHookStyles.getPropertyValue("padding-right"), 10));

	if (currentEnoughSpaceWidth > currentAvailableSpaceWidth) {
	    currentContainer.classList.add('has-space--no-space');
	    currentContainer.classList.remove('has-space--space');
	} else {
	    currentContainer.classList.add('has-space--space');
	    currentContainer.classList.remove('has-space--no-space');
	}
    };

    return {
	init: init
    };

}(window));
