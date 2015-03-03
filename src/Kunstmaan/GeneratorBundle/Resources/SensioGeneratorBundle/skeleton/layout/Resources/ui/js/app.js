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

            $('.form-control').bind('focus', function () {
                $(this).addClass('form-control--filled');
            });
            $('.form-control').bind('blur', function () {
                if( $(this).val() ) {
                    $(this).addClass('form-control--filled');
                } else {
                    $(this).removeClass('form-control--filled');
                }
            });

            $('.js-form-control-choice').bind('focus', function () {
                $(this).parent().parent().find('.form-widget--choices').addClass('form-widget--choices--filled');
            });
            $('.js-form-control-choice').bind('blur', function () {
                if( $(this).val() ) {
                    $(this).parent().parent().find('.form-widget--choices').addClass('form-widget--choices--filled');
                } else {
                    $(this).parent().parent().find('.form-widget--choices').removeClass('form-widget--choices--filled');
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
