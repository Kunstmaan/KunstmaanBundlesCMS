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
        var urlPrefix = $widget.data('url-prefix');

        // Elements
        var $input = $widget.find('.js-slug-chooser__input'),
            $preview = $widget.find('.js-slug-chooser__preview'),
            $resetBtn = $widget.find('.js-slug-chooser__reset-btn');

        // Update
        $input.on('change', function() {
            updateSlugPreview($input, $preview, urlPrefix, resetValue);
        });
        $input.on('keyup', function() {
            updateSlugPreview($input, $preview, urlPrefix, resetValue);
        });

        // Reset Btn
        $resetBtn.on('click', function() {
            resetSlug($input, resetValue);
        });
    };

    updateSlugPreview = function($input, $preview, urlPrefix, resetValue) {
        var updatedUrl = urlPrefix + $input.val();

        if(resetValue === $input.val()) {
            $preview.hide();

            return;
        }

        $preview.find('span').html(updatedUrl);
        $preview.show();
    };

    resetSlug = function($input, resetValue) {
        $input.val(resetValue);
        $input.trigger('change');
    };

    return {
        init: init
    };

})(window);
