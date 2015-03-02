var projectname = projectname || {};

projectname = (function($, window, undefined) {

    var init, initForms;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();

        initForms();
    };

    initForms = function() {
        if( $('.form--default').length ) {
            $('.form-control').bind('blur', function () {
                if( $(this).val() ) {
                    $(this).addClass('form-control--filled');
                } else {
                    $(this).removeClass('form-control--filled');
                }
            });
        }
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    projectname.init();
});
