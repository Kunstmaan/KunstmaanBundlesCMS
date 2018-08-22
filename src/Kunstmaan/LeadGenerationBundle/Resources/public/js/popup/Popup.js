'use strict';

(function(window, document, undefined) {
    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};

    window.kunstmaan.leadGeneration.Popup = function(name, htmlId) {
        var instance = {
            'name': name,
            'id': htmlId
        };

        var RULES = [],
            POPUP,
            CLOSE,
            CANCEL,
            SUBMIT;

        var _listenToHtmlClicks, _listenToEvents, _conditionsMet, _forEachRule, _doConditionsMetLogic,
            _htmlNoThanks, _noThanks, _htmlClose, _close, _conversion, _htmlSubmit, _submit,
            _onSubmitSuccess, _getData, _setData;

        function setElements() {
            POPUP = document.querySelector('#' + htmlId);
            CLOSE = document.querySelector('.' + htmlId + '--close');
            CANCEL = document.querySelector('.' + htmlId + '--no-thanks');
            SUBMIT = document.querySelector('.' + htmlId + '--submit');
        }

        setElements();

        instance.addRule = function(rule) {
            rule.setPopup(instance);
            RULES.push(rule);
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
                if (RULES.length === 0) {
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
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.BEFORE_SHOWING, { detail: {popup: instance.name, id: instance.id} }));

            POPUP.classList.remove('popup--hide');
            POPUP.classList.add('popup--show');

            var data = _getData();
            data.last_shown = new window.Date().getTime();
            _setData(data);

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.IS_SHOWING, { detail: {popup: instance.name, id: instance.id} }));
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
            if (CLOSE) {
                CLOSE.addEventListener('click', _htmlClose);
            }
            if (CANCEL) {
                CANCEL.addEventListener('click', _htmlNoThanks);
            }
            if (SUBMIT) {
                SUBMIT.addEventListener('click', _htmlSubmit);
            }
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
            for (; i < RULES.length; i++) {
                cb(RULES[i]);
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
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.READY_TO_SHOW, { detail: {popup: instance.name, id: instance.id} }));
            }
        };

        _htmlNoThanks = function(event) {
            window.kunstmaan.leadGeneration.log(instance.name + ": no thanks");

            event.preventDefault();

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_NO_THANKS, { detail: {popup: instance.name, id: instance.id} }));
        };

        _noThanks = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ": no thanks event catched");

                var data = _getData();
                data.no_thanks = true;
                _setData(data);

                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.NO_THANKS, { detail: {popup: instance.name, id: instance.id} }));

                _close(event);
            }
        };

        _htmlClose = function(event) {
            event.preventDefault();

            window.kunstmaan.leadGeneration.log(instance.name + ": html close click");
            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_CLOSE, { detail: {popup: instance.name, id: instance.id} }));
        };

        _close = function(event) {
            if (event.detail.popup === instance.name) {
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.BEFORE_CLOSING, {detail: {popup: instance.name, id: instance.id}}));
                window.kunstmaan.leadGeneration.log(instance.name + ": close event catched");

                POPUP.classList.remove('popup--show')
                POPUP.classList.add('popup--hide');
                document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.IS_CLOSING, {detail: {popup: instance.name, id: instance.id}}));
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

            document.dispatchEvent(new window.CustomEvent(window.kunstmaan.leadGeneration.events.DO_SUBMIT_FORM, {detail: {popup: instance.name, form: findAncestor(SUBMIT, 'form')}}));
        };

        _submit = function(event) {
            if (event.detail.popup === instance.name) {
                window.kunstmaan.leadGeneration.log(instance.name + ': submit form');

                var url = event.detail.form.action;
                var data = serialize(event.detail.form);

                var request = new XMLHttpRequest();
                request.onreadystatechange = function() {
                    if (request.readyState === 4) {
                        _onSubmitSuccess(request.responseText);
                    }
                };
                request.open('POST', url);
                request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                request.send(data);
            }
        };

        _onSubmitSuccess = function(data) {
            window.kunstmaan.leadGeneration.log(instance.name + ': onSubmitSuccess');

            document.querySelector('#' + htmlId + '--content').innerHTML = data;

            var scripts = document.querySelectorAll('#' + htmlId + '--content script'), i;

            for (i = 0; i < scripts.length; ++i) {
                eval(scripts[i].innerText);
            }

            setElements();
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

    function findAncestor(el, sel) {
        while ((el = el.parentElement) && !((el.matches || el.matchesSelector).call(el,sel)));
        return el;
    }

    function serialize(form) {
        var data = [];

        for (var i = form.elements.length - 1; i >= 0; i -= 1) {
            if (form.elements[i].name === '') {
                continue;
            }
            var elementName = form.elements[i].name;
            var elementValue = form.elements[i].value;
            var serialized = encodeURIComponent(elementName) + '=' + encodeURIComponent(elementValue);
            var nodeName = form.elements[i].nodeName.toUpperCase();
            switch (nodeName) {
                case 'INPUT':
                    switch (form.elements[i].type) {
                        case 'file':
                        case 'submit':
                        case 'button':
                            break;
                        case 'checkbox':
                        case 'radio':
                            if (form.elements[i].checked) {
                                data.push(serialized);
                            }
                            break;
                        default:
                            data.push(serialized);
                            break;
                    }
                    break;
                case 'TEXTAREA':
                    data.push(serialized);
                    break;
                case 'SELECT':
                    switch (form.elements[i].type) {
                        case 'select-one':
                            data.push(serialized);
                            break;
                        case 'select-multiple':
                            for (var j = form.elements[i].options.length - 1; j >= 0; j -= 1) {
                                if (form.elements[i].options[j].selected) {
                                    data.push(encodeURIComponent(elementName) + '=' + encodeURIComponent(form.elements[i].options[j].value));
                                }
                            }
                            break;
                    }
                    break;
            }
        }

        return data.join('&');
    }
})(window, document);
