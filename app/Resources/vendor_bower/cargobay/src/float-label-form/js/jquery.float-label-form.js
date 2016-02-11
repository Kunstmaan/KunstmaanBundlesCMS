/* ==========================================================================
   Float Label Form

   Initialize:
   cargobay.floatLabelForm.init();

   Support:
   Latest Chrome
   Latest FireFox
   Latest Safari
   IE9 and up
   ========================================================================== */

var cargobay = cargobay || {};

cargobay.floatLabelForm = (function($, window, undefined) {
    var activeLabelClass = 'form__label--active',
        focusLabelClass = 'form__label--focus',
        $hook = $('.js-form__field'),
        init;

    init = function() {
        $hook.bind('checkval', function() {
            var label = $(this).prev('.form__label');

            if ($(this).val().length !== 0) {
                label.addClass(activeLabelClass);
            } else {
                label.removeClass(activeLabelClass);
            }

        }).on('keydown', function() {
            $(this).trigger('checkval');
        }).on('focus', function() {
           $(this).prev('.form__label').addClass(focusLabelClass);
        }).on('blur', function() {
            $(this).prev('.form__label').removeClass(focusLabelClass);
        }).trigger('checkval');
    };


    return {
        init: init
    };

}(jQuery, window));
