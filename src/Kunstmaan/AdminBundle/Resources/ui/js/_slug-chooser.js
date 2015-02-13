var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.slugChooser = (function(window, undefined) {

    var init, slugChooser;


    init = function() {
        if($('#slug-chooser').length) {
            slugChooser();
        }
    };


    // Slug-Chooser
    slugChooser = function() {
        var _updateSlugPreview, _resetSlug;

        var $widget = $('#slug-chooser'),
            $input = $('#slug-chooser__input'),
            $preview = $('#slug-chooser__preview'),
            $resetBtn = $('#slug-chooser__resetbtn'),
            resetValue = $widget.data('reset')
            urlprefix = $widget.data('url-prefix');

        // Setup url prefix
        if(urlprefix.length == 0 || urlprefix.indexOf('/', urlprefix.length - 1) == -1) { //endwidth
            urlprefix += '/';
        }

        // Update function
        _updateSlugPreview = function() {
            var inputValue = $input.val();

            $preview.html('url: ' + urlprefix + inputValue);
        };
        $input.on('change', _updateSlugPreview);
        $input.on('keyup', _updateSlugPreview);

        // Reset
        _resetSlug = function() {
            $input.val(resetValue);
            _updateSlugPreview();
        };
        $resetBtn.on('click', _resetSlug);

        // Set initial value
        _updateSlugPreview();
    };


    return {
        init: init
    };

}(window));
