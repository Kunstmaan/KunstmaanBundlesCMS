/* global document */

import { listen } from '../state';

class Component {
    constructor({
        identifier,
        vdom,
        configuration,
        eventListeners,
    }) {
        this.hasEventsConfigured = false;
        this.previousVisibilityScope = null;
        this.identifier = identifier;
        this.vdom = vdom || document.getElementById(identifier);
        this.configuration = Object.assign({
            isOnCookiePage: false,
        }, configuration);
        this.eventListeners = eventListeners || {};

        // So we don't accidentally double init a component.
        // This could happen when async adding content and uncarefully running de AsyncDomInitiator.
        if (!this.vdom.kmccInitiated) {
            this.boundEventListeners = this.bindEventListeners();
            listen(this.handleComponentState.bind(this));
            this.vdom.kmccInitiated = true;
        }
    }

    // addEventLister(<type>, handler.bind(context)) is not removeable otherwise.
    bindEventListeners() {
        const boundFns = {};
        Object.keys(this.eventListeners).forEach((eventType) => {
            boundFns[eventType] = this[this.eventListeners[eventType]].bind(this);
        });
        return boundFns;
    }

    handleComponentState(state) {
        // handle visibility and events of component;
        if (Object.prototype.hasOwnProperty.call(this.configuration, 'visibilityScopes')) {
            if (Object.keys(this.configuration.visibilityScopes).indexOf(state.visibilityScope) >= 0) {
                if (this.previousVisibilityScope !== null) {
                    this.hide(this.previousVisibilityScope);
                }

                this.show(state);
            } else {
                this.hide(this.previousVisibilityScope);
                this.removeAllEventListeners();
                this.hasEventsConfigured = false;
            }
        }
        // Alwasy configure events. Some items don't have visibilityscopes defined.
        if (!this.hasEventsConfigured) {
            this.addAllEventListeners();
        }
        // Used to reset previous values on scope.
        this.previousVisibilityScope = state.visibilityScope;
    }

    addAllEventListeners() {
        Object.keys(this.boundEventListeners).forEach((eventType) => {
            this.vdom.addEventListener(eventType, this.boundEventListeners[eventType]);
        });

        this.hasEventsConfigured = true;
    }

    removeAllEventListeners() {
        Object.keys(this.boundEventListeners).forEach((eventType) => {
            this.vdom.removeEventListener(eventType, this.boundEventListeners[eventType]);
        });
    }

    // Regarding classList.add/classList.remove:
    // Can't do this form below because IE11 does not support multiple params for classList.add
    // this.vdom.classList.add.apply(this.vdom.classList, this.configuration.visibilityScopes[visibilityScope]);
    // see: https://developer.mozilla.org/en-US/docs/Web/API/Element/classList#compat-desktop

    show({ visibilityScope }) {
        const visibilityScopes = this.configuration.visibilityScopes[visibilityScope];
        if (Array.isArray(visibilityScopes)) {
            visibilityScopes.forEach((scopeClass) => {
                this.vdom.classList.add(scopeClass);
            });
        }
    }

    hide(previousVisibilityScope) {
        const visibilityScopes = this.configuration.visibilityScopes[previousVisibilityScope];
        if (Array.isArray(visibilityScopes) && visibilityScopes.length > 0) {
            visibilityScopes.forEach((scopeClass) => {
                this.vdom.classList.remove(scopeClass);
            });
        }
    }
}

export default Component;
