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

    var init, calcSpace, toggleState, debounce,
        containerClass = '.js-has-space',
        containerItemClass = '.js-has-space__item';

    init = function() {
        calcSpace();
    };

    calcSpace = function() {
        var _toggleStateDebounced = debounce(toggleState, 250);

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
                _toggleStateDebounced($this, spaceHook, enoughSpaceWidth);
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

    debounce = function(func, wait, immediate){
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) {
                    func.apply(context, args);
                }
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) {
                func.apply(context, args);
            }
        };
    };

    return {
        init: init
    };

}(jQuery, window));
