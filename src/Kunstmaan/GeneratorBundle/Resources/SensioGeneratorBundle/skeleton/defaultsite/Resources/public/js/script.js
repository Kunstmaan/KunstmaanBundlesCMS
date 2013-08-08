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
        $('.js-flexslider').flexslider({
            animation: "slide",
            controlNav: true,
            slideshow: false,
            startAt: 0,
            manualControls: ".js-thumbs .js-thumbs--item",
            start: function(slider){
                $(slider.slides.eq(0)).addClass('flex-animateIn');
            },
            before: function(slider){
                var thisSlide = slider.slides.eq(slider.currentSlide),
                    animateSlide = slider.slides.eq(slider.animatingTo);
                $(thisSlide).removeClass('flex-animateIn');
                $(animateSlide).addClass('flex-animateIn');
            }
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
