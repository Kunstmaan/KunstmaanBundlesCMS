import Component from './Component';

import { select, dispatch, SELECT_COOKIE_SETTINGS, SET_VISIBILITY_SCOPE_TO_NOTIFICATION } from '../state';
import { BUTTON_IDENTIFIER } from '../config/acceptSomeCookiesButton.config';
import cookies from '../services/cookies';

class AcceptSomeCookiesButton extends Component {
    constructor() {
        super({
            identifier: BUTTON_IDENTIFIER,
            eventListeners: {
                click: 'handleAcceptSomeCookies',
            },
        });
    }

    handleAcceptSomeCookies(e) {
        e.preventDefault();

        if (this.vdom.hasAttribute('data-href')) {
            const toggleSomeCookiesUrl = this.vdom.getAttribute('data-href');
            const cookieSettings = select(SELECT_COOKIE_SETTINGS);
            cookies.toggleSome(toggleSomeCookiesUrl, cookieSettings);
            dispatch(SET_VISIBILITY_SCOPE_TO_NOTIFICATION);
        } else {
            throw new Error('Expected data-href attribute to be present on button.');
        }
    }
}

export default AcceptSomeCookiesButton;
