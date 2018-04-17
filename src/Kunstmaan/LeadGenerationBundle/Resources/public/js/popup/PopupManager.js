'use strict';

(function(window, document, undefined) {
    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};

    window.kunstmaan.leadGeneration.PopupManager = function() {
        var instance = {};

        var _popups = [],
            _queue = [],
            _onStage = false;

        var _forEachPopup, _queueContains, _removeFromStage, _queuePopup, _start;

        instance.init = function() {};

        instance.registerPopup = function(popup) {
            _popups.push(popup);
        };

        instance.activate = function() {
            // listening just once for all popups
            document.addEventListener(window.kunstmaan.leadGeneration.events.READY_TO_SHOW, _queuePopup, true);
            document.addEventListener(window.kunstmaan.leadGeneration.events.IS_CLOSING, _removeFromStage);
            document.addEventListener(window.kunstmaan.leadGeneration.events.NO_THANKS, _removeFromStage);

            _forEachPopup(function(popup) {
                popup.activate();
            });

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.READY));
        };

        _start = function() {
            if (!_onStage && _queue.length > 0) {
                _onStage = true;
                var popupName = _queue.shift();
                _forEachPopup(function(popup) {
                    if (popup.name === popupName) {
                        window.kunstmaan.leadGeneration.log(popupName + ": staged");
                        popup.show();
                    }
                });
            }
        };

        _queuePopup = function(event) {
            if (!_queueContains(event.detail.popup)) {
                window.kunstmaan.leadGeneration.log(event.detail.popup + ': queueing');

                _queue.push(event.detail.popup);
                _start();
            }
        };

        _removeFromStage = function(event) {
            window.kunstmaan.leadGeneration.log(event.detail.popup + ': remove from stage');
            _onStage = false;
            _start();
        };

        _queueContains = function(popupName) {
            var i = 0;
            for (; i < _queue.length; i++) {
                if (popupName === _queue[i]) {
                    return true;
                }
            }

            return false;
        };

        _forEachPopup = function(cb) {
            var i = 0;
            for (; i < _popups.length; i++) {
                cb(_popups[i]);
            }
        };

        return instance;
    };

})(window, document);
