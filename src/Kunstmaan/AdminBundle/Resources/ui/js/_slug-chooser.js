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
            $preview = $widget.find('.js-slug-chooser__preview'),
            $chooser = $widget.find('.js-slug-chooser'),
            $resetBtn = $widget.find('.js-slug-chooser__reset-btn');

        // Update
        $input.on('change', function() {
            updateSlugPreview($input, $preview, $chooser);
        });
        $input.on('keyup', function() {
            updateSlugPreview($input, $preview, $chooser);
        });

        // Reset Btn
        $resetBtn.on('click', function() {
            resetSlug($input, resetValue);
        });
    };

    updateSlugPreview = function($input, $preview, $chooser) {
        var updatedUrl = $chooser.attr('data-url-prefix') + $input.val();

        if($input.attr('data-slug') === $input.val()) {
            $preview.hide();

            return;
        }

        $preview.find('a').attr('href', updatedUrl);
        $preview.find('a').html(updatedUrl);

        $preview.show();
    };

    resetSlug = function($input, resetValue) {
        $input.val(resetValue);
        $input.trigger('change')
    };

    return {
        init: init
    };

})(window);
