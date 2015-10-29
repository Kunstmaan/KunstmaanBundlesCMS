'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.OnExitIntentRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup, _delayTimer, _disableKeydown, _attachListener, _fire, _handleMouseLeave, _handleMouseEnter, _handleKeyDown, _sensitivity, _timer, _delay;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate OnExitIntentRule rule " + id);

            _sensitivity = properties.sensitivity === null ? 20 : properties.sensitivity;
            _timer = properties.timer === null ? 1000 : properties.timer;
            _delay = properties.delay === null ? 0 : properties.delay;

            window.kunstmaan.leadGeneration.log(_popup.name + ": OnExitIntentRule sensitivity: " + _sensitivity);
            window.kunstmaan.leadGeneration.log(_popup.name + ": OnExitIntentRule timer: " + _timer);
            window.kunstmaan.leadGeneration.log(_popup.name + ": OnExitIntentRule delay: " + _delay);

            _disableKeydown = false;
            window.setTimeout(_attachListener, _timer);
        };

        _attachListener = function() {
            document.documentElement.addEventListener('mouseleave', _handleMouseLeave);
            document.documentElement.addEventListener('mouseenter', _handleMouseEnter);
            document.documentElement.addEventListener('keydown', _handleKeyDown);
        };

        _handleMouseLeave = function(e) {
            if (e.clientY > _sensitivity) {
                return;
            }

            _delayTimer = setTimeout(_fire, _delay);
        };

        _handleMouseEnter = function() {
            if (_delayTimer) {
                clearTimeout(_delayTimer);
                _delayTimer = null;
            }
        };

        _handleKeyDown = function(e) {
            if (_disableKeydown) {
                return;
            } else if (!e.metaKey || e.keyCode !== 76) {
                return;
            }

            _disableKeydown = true;
            _delayTimer = setTimeout(_fire, _delay);
        };

        _fire = function() {
            if (instance.isMet == false) {
                document.documentElement.removeEventListener('mouseleave', _handleMouseLeave);
                document.documentElement.removeEventListener('mouseenter', _handleMouseEnter);
                document.documentElement.removeEventListener('keydown', _handleKeyDown);

                window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for OnExitIntentRule rule " + id);
                instance.isMet = true;
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
            }
        };

        return instance;
    };

})(window, document);
