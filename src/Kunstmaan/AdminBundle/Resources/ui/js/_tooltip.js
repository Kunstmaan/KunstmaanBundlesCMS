var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.tooltip = (function(window, undefined) {

    var init, initTooltip;

    init = function() {
        $('[data-toggle="tooltip"]').each(function() {
            if(!$(this).hasClass('js-tooltip--enabled')) {
                initTooltip($(this));
            }
        });
    };


    // Initialize
    initTooltip = function($el) {
        $el.addClass('js-tooltip--enabled');
        $el.tooltip();
    };


    return {
        init: init
    };

}(window));
