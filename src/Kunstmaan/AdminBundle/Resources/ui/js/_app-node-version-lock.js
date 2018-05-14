var kunstmaanbundles = kunstmaanbundles || {};

kunstmaanbundles.appNodeVersionLock = (function(window, undefined) {

    var init, checkNodeVersionLock;

    init = function () {
        if ($('body').hasClass('js-node-version-lock')) {
            kunstmaanbundles.appNodeVersionLock.checkNodeVersionLock();
        }
    };

    checkNodeVersionLock = function() {
        var $elem = $('#js-node-version-lock-data');
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
                            kunstmaanbundles.appNodeVersionLock.checkNodeVersionLock();
                        }, interval);
                    }
                });
            }
        }
    };

    return {
        init: init,
        checkNodeVersionLock: checkNodeVersionLock
    };

})(window);
