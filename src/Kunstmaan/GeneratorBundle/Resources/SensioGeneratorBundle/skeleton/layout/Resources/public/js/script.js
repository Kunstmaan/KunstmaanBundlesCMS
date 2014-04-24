/*
    change myApplication to the projectname
*/

var myApplication = (function($, window, undefined) {

    /*
        declare all your methods here
    */
    var init, initSocial;

    init = function() {
        cupcake.navigation.init();
        cupcake.slider.init();
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
