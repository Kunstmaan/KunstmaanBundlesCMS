'use strict';

(function(window, document, $, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};

    window.kunstmaan.leadGeneration.Popup = function(name, htmlId) {
        var instance = {
            'name': name
        };

        var _rules = [],
            _$popup = $('#' + htmlId),
            _$close = $('.' + htmlId + '--close'),
            _$noThanks = $('.' + htmlId + '--no-thanks'),
            _$submit = $('.' + htmlId + '--submit');

        var _listenToHtmlClicks, _listenToEvents, _conditionsMet, _forEachRule, _doConditionsMetLogic,
            _htmlNoThanks, _noThanks, _htmlClose, _close, _conversion, _htmlSubmit, _submit,
            _onSubmitSuccess, _getData, _setData;

        instance.addRule = function(rule) {
            rule.setPopup(instance);
            _rules.push(rule);
        };

        instance.activate = function() {
            _listenToHtmlClicks();
            _listenToEvents();

            window.kunstmaan.leadGeneration.log(instance.name + ": activating all rules");

            // listening to all rules
            document.addEventListener(window.kunstmaan.leadGeneration.events.CONDITIONS_MET, _conditionsMet, true);

            var data = _getData();
            // if not converted && not clicked "no thanks", activate & listen to rules
            if (data === null || (!data.already_converted && !data.no_thanks)) {
                if (_rules.length === 0) {
                    // when there are nu rules, directly show the popup
                    _doConditionsMetLogic();
                } else {
                    _forEachRule(function(rule) {
                        rule.activate();
                    });
                }
            }
        };

        instance.show = function() {
            window.kunstmaan.leadGeneration.log(instance.name + ": show popup");
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.BEFORE_SHOWING, { detail: {popup: instance.name} }));

            $('#' + htmlId).removeClass('popup--hide').addClass('popup--show');

            var data = _getData();
            data.last_shown = new window.Date().getTime();
            _setData(data);

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.IS_SHOWING,  { detail: {popup: instance.name} }));
        };

        instance.setRuleProperty = function(ruleId, id, value) {
            var data = _getData();

            if (!data.rule) {
                data.rule = {};
            }

            if (!data.rule[ruleId]) {
                data.rule[ruleId] = {};
            }

            // adjust timestamp
            data.rule[ruleId][id] = value;

            // store in browser storage
            _setData(data);
        };

        instance.getRuleProperty = function(ruleId, id) {
            var data = _getData();

            if (!data.rule || !data.rule[ruleId] || !data.rule[ruleId][id]) {
                return false;
            }

            return data.rule[ruleId][id];
        };

        _listenToHtmlClicks = function() {
            _$close.click(_htmlClose);
            _$noThanks.click(_htmlNoThanks);
            _$submit.click(_htmlSubmit);
        };

        _listenToEvents = function() {
            document.addEventListener(window.kunstmaan.leadGeneration.events.DO_CLOSE, _close, true);
            document.addEventListener(window.kunstmaan.leadGeneration.events.DO_NO_THANKS, _noThanks, true);
            document.addEventListener(window.kunstmaan.leadGeneration.events.DO_SUBMIT_FORM, _submit, true);
            document.addEventListener(window.kunstmaan.leadGeneration.events.DO_CONVERSION, _conversion, true);
        };

        _conditionsMet = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ": checking all conditions");

                _doConditionsMetLogic();
            }
        };

        _forEachRule = function(cb) {
            var i = 0;
            for (; i < _rules.length; i++) {
                cb(_rules[i]);
            }
        };

        _doConditionsMetLogic = function() {
            var areMet = true;
            _forEachRule(function(rule) {
                if (!rule.isMet) {
                    areMet = false;
                }
            });

            var data = _getData();
            // if all conditions are met notify that the popup is ready to be shown
            if (areMet && (data === null || (!data.already_converted && !data.no_thanks))) {
                window.kunstmaan.leadGeneration.log(instance.name + ": firing ready event");
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.READY_TO_SHOW, { detail: {popup: instance.name} }));
            }
        };

        _htmlNoThanks = function(event) {
            window.kunstmaan.leadGeneration.log(instance.name + ": no thanks");

            event.preventDefault();

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_NO_THANKS, { detail: {popup: instance.name} }));
        };

        _noThanks = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ": no thanks event catched");

                var data = _getData();
                data.no_thanks = true;
                _setData(data);

                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.NO_THANKS, { detail: {popup: instance.name} }));

                _close(event);
            }
        };

        _htmlClose = function(event) {
            event.preventDefault();

            window.kunstmaan.leadGeneration.log(instance.name + ": html close click");
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_CLOSE, { detail: {popup: instance.name} }));
        };

        _close = function(event) {
            if (event.detail.popup === instance.name) {
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.BEFORE_CLOSING, {detail: {popup: instance.name}}));
                window.kunstmaan.leadGeneration.log(instance.name + ": close event catched");

                _$popup.removeClass('popup--show').addClass('popup--hide');
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.IS_CLOSING, {detail: {popup: instance.name}}));
            }
        };

        _conversion = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ': mark as converted');
                var data = _getData();
                data.already_converted = true;
                _setData(data);
            }
        };

        _htmlSubmit = function(event) {
            event.preventDefault();
            window.kunstmaan.leadGeneration.log(instance.name + ': html submit form');

            var $form = _$submit.parents('form');
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_SUBMIT_FORM, {detail: {popup: instance.name, form: $form}}));
        };

        _submit = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ': submit form');

                var url = $(event.detail.form).attr('action');
                var data = $(event.detail.form).serialize();

                $.post(url, data, _onSubmitSuccess);
            }
        };

        _onSubmitSuccess = function(data) {
            window.kunstmaan.leadGeneration.log(instance.name + ': onSubmitSuccess');

            $('#' + htmlId + '--content').html(data);
            _listenToHtmlClicks();
        };

        _getData = function() {
            if (window.localStorage) {
                var data = window.localStorage.getItem('popup_' + instance.name);
                if (data != null) {
                    return window.JSON.parse(data);
                } else {
                    data = {'last_shown': null, 'already_converted': false, 'no_thanks': false};
                    _setData(data);
                }

                return data;
            }
        };

        _setData = function(data) {
            if (window.localStorage) {
                window.localStorage.setItem('popup_' + instance.name, window.JSON.stringify(data));
            }
        };

        return instance;
    };

})(window, document, $);
