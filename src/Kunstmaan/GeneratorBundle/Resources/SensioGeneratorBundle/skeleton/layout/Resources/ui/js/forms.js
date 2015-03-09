var {{ bundle.getName() }} = {{ bundle.getName() }} || {};

{{ bundle.getName() }}.forms = (function($, window, undefined) {

    var init, initForms;

    init = function() {
        initForms();
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

    initSearch = function() {
        $('.js-searchbox').on('click', function() {
            $(this).addClass('searchbox--active');
        });
    };

    return {
        init: init
    };

}(jQuery, window));
