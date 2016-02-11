'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.RecurringEveryXTimeRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup,
            _minute = 1000 * 60,
            _hour = _minute * 60,
            _day = _hour * 24;

        var _ready, _hasNotBeenLaunchedMaxTimes, _markViewed, _getDelay;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate RecurringEveryXTimeRule rule " + id);

            // listen to the BEFORE CLOSING event to set the date viewed
            document.addEventListener(window.kunstmaan.leadGeneration.events.BEFORE_CLOSING, _markViewed);

            // if never launched before => mark as condition met
            if (!_popup.getRuleProperty(id, 'date')) {
                window.kunstmaan.leadGeneration.log(_popup.name + ': never launched before');
                _ready();

                return;
            }

            var now = new window.Date().getTime();
            var launchTime = (_popup.getRuleProperty(id, 'date') + _getDelay());

            // if has launched before and can be launched again => mark as condition met
            if (launchTime <= now && _hasNotBeenLaunchedMaxTimes()) {
                _ready();
            } else if (_hasNotBeenLaunchedMaxTimes()) {
                // if has launched before but shouldn't be launched yet => set timer and mark as condition met when timer finishes
                window.setTimeout(_ready, launchTime - now);
            }
        };

        _hasNotBeenLaunchedMaxTimes = function() {
            var maxTimes = properties.times;
            var timesViewed = _popup.getRuleProperty(id, 'times');

            return (!maxTimes || !timesViewed || maxTimes > timesViewed);
        };

        _markViewed = function(event) {
            if (event.detail.popup == _popup.name) {
                window.kunstmaan.leadGeneration.log(_popup.name + ': set date last viewed');
                _popup.setRuleProperty(id, 'date', new window.Date().getTime());

                if (properties.times) {
                    var timesViewed = _popup.getRuleProperty(id, 'times');
                    if (!timesViewed) {
                        timesViewed = 0;
                    }
                    timesViewed = timesViewed + 1;

                    window.kunstmaan.leadGeneration.log(_popup.name + ': set times viewed ' + timesViewed);
                    _popup.setRuleProperty(id, 'times', timesViewed);
                }

                if (_hasNotBeenLaunchedMaxTimes()) {
                    instance.isMet = false;

                    window.kunstmaan.leadGeneration.log(_popup.name + ': reschedule new ready');
                    // don't need an interval if we reschedule each time the popup closes
                    window.setTimeout(_ready, _getDelay());
                }
            }
        };

        _getDelay = function() {
            var activateAgainIn = 0;
            if (properties.minutes) {
                activateAgainIn = properties.minutes * _minute;
            } else if(properties.hours) {
                activateAgainIn = properties.hours * _hour;
            } else if(properties.days) {
                activateAgainIn = properties.days * _day;
            }

            return activateAgainIn;
        };

        _ready = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for RecurringEveryXTimeRule rule " + id);

            instance.isMet = true;
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
        };

        return instance;
    };

})(window, document);
