/*
    change myApplication to the projectname
*/

var myApplication = (function($, window, undefined) {

    /*
        declare all your methods here
    */
    var init, initSliders;

    init = function() {
        cupcakeNavigation.init();
        initSliders();
    };

    initSliders = function() {
        $('.flexslider').flexslider({
            animation: "slide",
            controlNav: "thumbnails"
        });
    };

    /*
        Put the methods you want to be public in this object
    */
    return {
        init: init
    };

}(jQuery, window));

$(function() {
    myApplication.init();
});
