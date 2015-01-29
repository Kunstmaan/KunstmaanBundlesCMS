var meetingKempen = meetingKempen || {};

meetingKempen = (function($, window, undefined) {

    var init, initFitText;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();
        initFitText();
    };

    initFitText = function() {
        $('#responsive-headline').fitText(1.2, { minFontSize: '24px', maxFontSize: '74px' });
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    meetingKempen.init();
});
