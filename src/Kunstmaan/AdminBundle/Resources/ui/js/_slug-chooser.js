var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.slugChooser = (function(window, undefined) {

    var init, slugChooser,
        updateSlugPreview, resetSlug;


    init = function() {
        // Init
        $('.js-slug-chooser').each(function() {
            slugChooser($(this));
        });
    };


    // Slug-Chooser
    slugChooser = function($widget) {
        var resetValue = $widget.data('reset');

        // Elements
        var $input = $widget.find('.js-slug-chooser__input'),
            $resetBtn = $widget.find('.js-slug-chooser__reset-btn');

        // Reset Btn
        $resetBtn.on('click', function() {
            resetSlug($input, resetValue);
        });
    };

    resetSlug = function($input, resetValue) {
        $input.val(resetValue);
    };

    return {
        init: init
    };

})(window);
