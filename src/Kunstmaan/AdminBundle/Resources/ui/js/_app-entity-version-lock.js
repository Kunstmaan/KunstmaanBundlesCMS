var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.appEntityVersionLock = (function(window, undefined) {

    var init, checkEntityVersionLock;

    init = function () {
        if ($('body').hasClass('js-entity-version-lock')) {
            kunstmaanbundles.appEntityVersionLock.checkEntityVersionLock();
        }
    };

    checkEntityVersionLock = function() {
        var $elem = $('#js-entity-version-lock-data');
        if ($elem) {
            var url = $elem.data('url');
            var interval = $elem.data('check-interval') * 1000;

            if (url) {
                $.getJSON(url, function (data) {
                    if (data.lock) {
                        $elem.find('.message').html(data.message);
                        $elem.removeClass('hidden');
                    } else {
                        $elem.addClass('hidden');
                    }

                    if (interval) {
                        setTimeout(function () {
                            kunstmaanbundles.appEntityVersionLock.checkEntityVersionLock();
                        }, interval);
                    }
                });
            }
        }
    };

    return {
        init: init,
        checkEntityVersionLock: checkEntityVersionLock
    };

})(window);
