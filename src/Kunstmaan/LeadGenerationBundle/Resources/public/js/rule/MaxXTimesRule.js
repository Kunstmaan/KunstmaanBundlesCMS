'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.MaxXTimesRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup;

        var _markViewed, _hasNotBeenLaunchedMaxTimes, _ready;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate MaxXTimesRule rule " + id);

            // listen to the BEFORE CLOSING event to set the date viewed
            document.addEventListener(window.kunstmaan.leadGeneration.events.BEFORE_CLOSING, _markViewed);

            if (_hasNotBeenLaunchedMaxTimes()) {
                _ready();
            }
        };

        _markViewed = function(event) {
            if (event.detail.popup == _popup.name) {
                var timesViewed = _popup.getRuleProperty(id, 'times');
                if (!timesViewed) {
                    timesViewed = 0;
                }
                timesViewed = timesViewed + 1;

                window.kunstmaan.leadGeneration.log(_popup.name + ': set times viewed ' + timesViewed);
                _popup.setRuleProperty(id, 'times', timesViewed);
            }
        };

        _hasNotBeenLaunchedMaxTimes = function() {
            var maxTimes = properties.times;
            var timesViewed = _popup.getRuleProperty(id, 'times');

            return (!timesViewed || maxTimes > timesViewed);
        };

        _ready = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for MaxXTimesRule rule " + id);
            instance.isMet = true;
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
        };

        return instance;
    };

})(window, document);
