var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.charactersLeft = (function(window, undefined) {

    var init, reInit, _updateCounter;

    // Initialize
    init = function() {
        $('.js-max-length-input').each(function() {
            _updateCounter(this);

            $(this).on('input', _updateCounter);
        });
    };

    reInit = function(el) {
        if (el) {
            _updateCounter(el);
            $(el).on('input', _updateCounter);
        } else {
            $('.js-max-length-input').each(function() {
                _updateCounter(this);

                $(this).on('input', _updateCounter);
            });
        }
    };

    // Update value
    _updateCounter = function(e) {
        var $currentElement = typeof e.target !== 'undefined' ? $(e.target) : $(e);

        var maxLength = parseInt($currentElement.attr('maxlength'), 10);
        var remaining = maxLength - $currentElement.val().length;
        var $counter = $($currentElement.attr('data-target'));

        $counter.text(remaining);

        if (remaining <= 5) {
            $counter.addClass('form-control__character-counter--warning');
        } else {
            $counter.removeClass('form-control__character-counter--warning');
        }
    };


    return {
        init: init,
        reInit: reInit
    };

}(window));
