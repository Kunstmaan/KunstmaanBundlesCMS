/*
    change myApplication to the projectname
*/

var myApplication = (function($, window, undefined) {

    /*
        declare all your methods here
    */
    var init, initSliders, initSocial;

    init = function() {
        cupcake.navigation.init();
        initSliders();
        initSocial();
    };

    initSliders = function() {
        $('.flexslider').flexslider({
            animation: "slide",
            controlNav: "thumbnails"
        });
    };

    initSocial = function () {
        Socialite.load();
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
