var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.rangeslider = (function(window, undefined) {

    var init, reInit, initRangePercentage;

    init = reInit = function() {
        $(".range").find('input').change(function() {

            var $el = $(this);

            initRangePercentage($el);

        }).trigger('change');
    }
    //Initialize
    initRangePercentage = function($el) {

        $el.find('.range--value')
            .text($el.val());
    };

    return {
        init: init,
        reInit: reInit
    };

})(window);
