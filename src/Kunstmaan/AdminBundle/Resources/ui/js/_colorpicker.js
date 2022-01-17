var kunstmaanbundles = kunstmaanbundles || {};

// NEXT_MAJOR: Remove JS and releted SCSS/CSS when deprecate ColorType is removed
kunstmaanbundles.colorpicker = (function(window, undefined) {

    var init, reInit, initColorpicker;

    init = reInit = function() {
        $('.js-colorpicker').each(function() {
            if(!$(this).hasClass('js-colorpicker--enabled')) {
                initColorpicker($(this));
            }
        });
    };


    // Initialize
    initColorpicker = function($el) {
        $el.addClass('js-colorpicker--enabled');
        $el.colorpicker();
    };


    return {
        init: init,
        reInit: reInit
    };

})(window);
