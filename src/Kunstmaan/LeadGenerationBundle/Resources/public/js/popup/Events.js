'use strict';

(function(window, console, undefined) {

    window.kunstmaan = window.kunstmaan || {};
    window.kunstmaan.leadGeneration = window.kunstmaan.leadGeneration || {};
    window.kunstmaan.leadGeneration.events = window.kunstmaan.leadGeneration.events || {};

    /**
     * READY: the popup manager is ready initializing all the popups.
     */
    window.kunstmaan.leadGeneration.events.READY = 'window.kunstmaan.leadGeneration.events.ready';
    /**
     * CONDITIONS_MET: event dispatched by the Rules to indicate that their requirements are fulfilled.
     * The Popup listens to this event to then checks whether all it's rules have been fulfilled.
     */
    window.kunstmaan.leadGeneration.events.CONDITIONS_MET = 'window.kunstmaan.leadGeneration.events.conditions_met';
    /**
     * READY_TO_SHOW: event dispatched by the Popup when all it's conditions are met. Listened to by the
     * PopupManager to then queue the popup and start displaying.
     */
    window.kunstmaan.leadGeneration.events.READY_TO_SHOW = 'window.kunstmaan.leadGeneration.events.ready_to_show';
    /**
     * BEFORE_SHOWING: dispatched right before showing the popup and adding it to "the stage".
     */
    window.kunstmaan.leadGeneration.events.BEFORE_SHOWING = 'window.kunstmaan.leadGeneration.events.before_showing';
    /**
     * IS_SHOWING: event dispatched by the Popup when it's on stage and shown.
     */
    window.kunstmaan.leadGeneration.events.IS_SHOWING = 'window.kunstmaan.leadGeneration.events.is_showing';
    /**
     * DO_CLOSE: can be fired by the application to remove the popup from the stage. Also fired when clicked on a close
     * button/link with the correct close class.
     */
    window.kunstmaan.leadGeneration.events.DO_CLOSE = 'window.kunstmaan.leadGeneration.events.do_close';
    /**
     * BEFORE_CLOSING: dispatched right before closing (after checking for a successfully filled in form and before
     * actually closing the popup and removing it from "the stage"). Listened to by the Rules to store their own data in the
     * storage.
     */
    window.kunstmaan.leadGeneration.events.BEFORE_CLOSING = 'window.kunstmaan.leadGeneration.events.before_closing';
    /**
     * IS_CLOSING: dispatched by the Popup and listened to by the PopupManager to remove the Popup from the "stage"
     * and fetch a new popup from the queue.
     */
    window.kunstmaan.leadGeneration.events.IS_CLOSING = 'window.kunstmaan.leadGeneration.events.is_closing';
    /**
     * DO_NO_THANKS: can be fired by the application to remove and hide the popup from the stage. Also fired when
     * clicked on a no thanks button/link with the correct class.
     */
    window.kunstmaan.leadGeneration.events.DO_NO_THANKS = 'window.kunstmaan.leadGeneration.events.do_no_thanks';
    /**
     * NO_THANKS: dispatched by the Popup, right after setting it's no_thanks value in the storage and before
     * launching the closing procedure.
     */
    window.kunstmaan.leadGeneration.events.NO_THANKS = 'window.kunstmaan.leadGeneration.events.no_thanks';
    /**
     * DO_SUBMIT_FORM: can be fired by the application to submit the form. Also fired when clicked on a submit button
     * with the correct class.
     */
    window.kunstmaan.leadGeneration.events.DO_SUBMIT_FORM = 'window.kunstmaan.leadGeneration.events.submit_form';
    /**
     * DO_CONVERSION: can be fired by the application to register a conversion.
     */
    window.kunstmaan.leadGeneration.events.DO_CONVERSION = 'window.kunstmaan.leadGeneration.events.do_conversion';

    /**
     * If set to true the popup javascript classes will print debugging info in the console if the console is available.
     */
    window.kunstmaan.leadGeneration.DEBUG = false;

    window.kunstmaan.leadGeneration.log = function(message) {
        if (console && window.kunstmaan.leadGeneration.DEBUG) {
            console.log(new window.Date().toLocaleTimeString() + " - " + message);
        }
    };

})(window, console);

// Polyfill for the CustomEvent() constructor (IE9)
(function () {
    function CustomEvent(event, params) {
        params = params || { bubbles: false, cancelable: false, detail: undefined };
        var evt = document.createEvent('CustomEvent');
        evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
        return evt;
    }
    CustomEvent.prototype = window.Event.prototype;
    window.CustomEvent = CustomEvent;
})();
