var kunstmaanbundles = kunstmaanbundles || {};

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

}(window));
