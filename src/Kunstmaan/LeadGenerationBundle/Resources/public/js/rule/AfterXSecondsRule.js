'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.AfterXSecondsRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup;

        var _ready;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate AfterXSecondsRule rule " + id);
            window.setTimeout(_ready, properties.seconds * 1000);
        };

        _ready = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for AfterXSecondsRule rule " + id);
            instance.isMet = true;
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
        };

        return instance;
    };

})(window, document);
