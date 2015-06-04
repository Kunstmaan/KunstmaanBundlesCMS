'use strict';

(function(window, document, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.UrlBlacklistRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _popup;

        var _ready;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate UrlBlacklistRule rule " + id);

            var currentPath = window.location.pathname;
            window.kunstmaan.leadGeneration.log(_popup.name + ": current path " + currentPath);

            var showPopup = true;

            var i = 0;
            for (; i < properties.urls.length; i++) {
                var path = properties.urls[i];
                var exp = new RegExp(path, "gi");
                if (exp.test(currentPath)) {
                    showPopup = false;
                    window.kunstmaan.leadGeneration.log(_popup.name + ": blacklisted path matched " + currentPath);

                    break;
                }
            }

            if (showPopup) {
                window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for UrlBlacklistRule rule " + id);

                instance.isMet = true;
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
            }
        };

        return instance;
    };

})(window, document);
