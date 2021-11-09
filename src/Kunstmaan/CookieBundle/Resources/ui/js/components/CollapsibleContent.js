/* global window */

import Component from './Component';

import {
    CONTENT_IDENTIFIER,
    TITLE_IDENTIFIER,
    CLASSES,
    STATES,
    BREAKPOINT,
} from '../config/collapsibleContent.config';

class CollapsibleContent extends Component {
    constructor({ vdom }) {
        super({
            vdom,
            eventListeners: {
                click: 'toggleCollapse',
            },
        });

        this.content = this.vdom.querySelector(CONTENT_IDENTIFIER);
        this.title = this.vdom.querySelector(TITLE_IDENTIFIER);
        this.collapsedState = STATES.STATE_OPEN;
        this.resizeTimeout = null;

        window.addEventListener('resize', this.resizeHandler.bind(this));
        this.resizeHandler(); // auto collapse on load on mobile.
        this.toggleCollapse();
    }

    shouldCollapse() {
        const breakpoint = this.vdom.dataset.breakpoint || BREAKPOINT;

        return !window.matchMedia(`(min-width: ${breakpoint}px)`).matches;
    }

    resizeHandler() {
        if (this.resizeTimeout !== null) {
            clearTimeout(this.resizeTimeout);
        }
        this.resizeTimeout = setTimeout(() => {
            if (this.shouldCollapse()) {
                this.close();
            } else {
                this.open();
            }
        }, 50);
    }

    toggleCollapse() {
        if (this.shouldCollapse()) {
            if (this.collapsedState === STATES.STATE_CLOSED) {
                this.open();
            } else {
                this.close();
            }
        }
    }

    open() {
        this.title.classList.add(CLASSES.OPEN);
        this.content.style.marginTop = '0px';

        this.collapsedState = STATES.STATE_OPEN;
    }

    close() {
        this.title.classList.remove(CLASSES.OPEN);
        this.content.style.marginTop = `-${this.content.clientHeight}px`;

        this.collapsedState = STATES.STATE_CLOSED;
    }
}

export default CollapsibleContent;
