import Component from './Component';

import {
    TO_TOP_BUTTON_IDENTIFIER,
    CLASSES,
} from '../config/toTopButton.config';

class ToTopButton extends Component {
    constructor({ controlledElement }) {
        super({
            identifier: TO_TOP_BUTTON_IDENTIFIER,
            eventListeners: {
                click: 'handleScrollToTop',
            },
        });

        if (typeof controlledElement === 'undefined') {
            throw new Error('You should specify an element that the button should scroll to top.');
        }

        this.controlledElement = controlledElement;
        this.controlledElement.addEventListener('scroll', this.handleScrollEvent.bind(this));
    }

    handleScrollEvent() {
        if (this.controlledElement.scrollTop >= (this.controlledElement.clientHeight / 2)) {
            this.vdom.classList.add(CLASSES.VISIBLE);
        } else {
            this.vdom.classList.remove(CLASSES.VISIBLE);
        }
    }

    handleScrollToTop(e) {
        e.preventDefault();

        this.controlledElement.scroll({
            top: 0,
            left: 0,
            behavior: 'smooth',
        });
    }
}

export default ToTopButton;
