'use strict';

(function(window, document, $, undefined) {

    var _$window = $(window),
        _$document = $(document);

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.rules = window.kunstmaan.leadGeneration.rules || {};

    window.kunstmaan.leadGeneration.rules.AfterXScrollPercentRule = function(id, properties) {
        var instance = {
            'isMet': false
        };

        var _locked = false,
            _popup;

        var _doCheck;

        instance.setPopup = function(popup) {
            _popup = popup;
        };

        instance.activate = function() {
            window.kunstmaan.leadGeneration.log(_popup.name + ": activate AfterXScrollPercentRule rule " + id);
            window.addEventListener('scroll', _doCheck);
        };

        _doCheck = function() {
            if (_locked) {
                return;
            }
            _locked = true;

            var wintop = _$window.scrollTop(),
                docheight = _$document.height(),
                winheight = _$window.height(),
                percentage = (wintop / (docheight - winheight)) * 100;

            if (percentage > properties.percentage) {
                window.kunstmaan.leadGeneration.log(_popup.name + ": condition met for AfterXScrollPercentRule rule " + id);
                window.removeEventListener('scroll', _doCheck);

                instance.isMet = true;
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, { detail: {popup: _popup.name, rule: id} }));
            }

            _locked = false;
        };

        return instance;
    };

})(window, document, $);
