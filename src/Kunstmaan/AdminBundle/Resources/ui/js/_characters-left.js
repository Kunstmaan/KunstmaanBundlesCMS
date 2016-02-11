var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.charactersLeft = (function(window, undefined) {

    var init, reInit, _updateCounter;

    // Initialize
    init = function() {
        $('.js-max-length-input').each(function() {
            $(this).addClass('js-counter-initialized');

            _updateCounter(this);
            $(this).on('input', _updateCounter);
        });
    };

    // Re initialize i.e when adding a new pp
    reInit = function(el) {
        if (el) {
            _updateCounter(el);
            $(el).on('input', _updateCounter);
        } else {
            $('.js-max-length-input').each(function() {
                // Check if element isn't initialized already
                if ($(this).hasClass('js-counter-initialized')  === false) {
                    _updateCounter(this);
                    $(this).on('input', _updateCounter);
                }
            });
        }
    };

    // Update value
    _updateCounter = function(e) {
        var $currentElement = typeof e.target !== 'undefined' ? $(e.target) : $(e);

        var maxLength = parseInt($currentElement.attr('maxlength'), 10);
        var remaining;

        // Check if maxLength is an intiger
        if (isNaN(maxLength)) {
            throw new Error('Please enter a valid limit count');
        } else {
             remaining = maxLength - $currentElement.val().length;
        }

        var $counter = $($currentElement.attr('data-target'));

        // update counter
        $counter.text(remaining);

        // running low indicator
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

})(window);
