var projectname = projectname || {};

projectname = (function($, window, undefined) {

    var init, initForms, initToggleClass;

    init = function() {
        cargobay.scrollToTop.init();
        cargobay.toggle.init();

        initForms();
        initToggleClass();
    };

    initForms = function() {
        if( $('.form--default').length ) {

            $('.js-form-control').bind('focus', function() {
                $(this).addClass('form-control--filled');
            });
            $('.form-control').bind('blur', function() {
                if( $(this).val() ) {
                    $(this).addClass('form-control--filled');
                } else {
                    $(this).removeClass('form-control--filled');
                }
            });

            $('.js-form-control-choice').bind('focus', function() {
                $(this).closest('.form-widget--choices').addClass('form-widget--choices--filled');
            });
            $('.js-form-control-choice').bind('blur', function() {
                if( $(this).is(':checked') ) {
                    $(this).closest('.form-widget--choices').addClass('form-widget--choices--filled');
                } else if( $(this).closest('.form-widget--choices').find('.form-control-choice').is(':checked') ) {
                    $(this).closest('.form-widget--choices').addClass('form-widget--choices--filled');
                } else {
                    $(this).closest('.form-widget--choices').removeClass('form-widget--choices--filled');
                }
            });
        }
    };

    initToggleClass = function() {
        $('.js-searchbox__submit').on('click', function() {
            $(this).closest('.searchbox__input-wrapper').toggleClass('searchbox__input-wrapper--active');
        });
    };

    return {
        init: init
    };

}(jQuery, window));

$(function() {
    projectname.init();
});
