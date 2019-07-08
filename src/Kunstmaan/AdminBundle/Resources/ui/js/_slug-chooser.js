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
        var resetValue = $widget.data('reset'),
            urlprefix = $widget.data('url-prefix');

        // Setup url prefix
        if(urlprefix.length == 0 || urlprefix.indexOf('/', urlprefix.length - 1) == -1) { //endwidth
            urlprefix += '/';
        }

        // Elements
        var $input = $widget.find('.js-slug-chooser__input'),
            $preview = $widget.find('.js-slug-chooser__preview'),
            $resetBtn = $widget.find('.js-slug-chooser__reset-btn');

        // Update
        $input.on('change', function() {
            updateSlugPreview($input, $preview, urlprefix, resetValue);
        });
        $input.on('keyup', function() {
            updateSlugPreview($input, $preview, urlprefix, resetValue);
        });

        // Reset Btn
        $resetBtn.on('click', function() {
            resetSlug($input, resetValue);
            updateSlugPreview($input, $preview, urlprefix, resetValue);
        });

        // Set initial value
        updateSlugPreview($input, $preview, urlprefix, resetValue);
    };


    resetSlug = function($input, resetValue) {
        $input.val(resetValue);
    };


    updateSlugPreview = function($input, $preview, urlprefix, resetValue) {
        var inputValue = $input.val();

        if(inputValue === resetValue) {
            $preview.hide();

            return;
        }

        $preview.html('url: ' + urlprefix + inputValue).show();
    };


    return {
        init: init
    };

})(window);
