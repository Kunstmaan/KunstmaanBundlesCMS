var projectname = projectname || {};

projectname = (function($, window, undefined) {

    var init;

    init = function() {
        cargobay.scrollToTop.init();
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    projectname.init();
});
