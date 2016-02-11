'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.LocaleWhitelistRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup;

        var _ready;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate LocaleWhitelistRule rule " + id);

            var currentPath = window.location.pathname;
            window.kunstmaan.leadGeneration.log(_popup.name + ": current path " + currentPath);

            var showPopup = true;

            if (properties.requestlocale != properties.locale) {
                showPopup = false;
                window.kunstmaan.leadGeneration.log(_popup.name + ": whitelisted locale did not match. Not showing.");
            }

            if (showPopup) {
                window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for LocaleWhitelistRule rule " + id);

                instance.isMet = true;
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
            }
        };

        return instance;
    };

})(window, document);
