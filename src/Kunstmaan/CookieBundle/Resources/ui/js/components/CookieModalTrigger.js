import Component from './Component';
import {
    dispatch,
    SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES,
    SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PRIVACY,
    SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_CONTACT,
} from '../state';
import {
    TAB_SCOPE_CONTACT,
    TAB_SCOPE_PREFERENCES,
    TAB_SCOPE_PRIVACY,
} from '../state/state.config';

class CookieModalTrigger extends Component {
    constructor({ vdom }) {
        super({
            vdom,
            eventListeners: {
                click: 'openCookieModal',
            },
        });
    }

    openCookieModal(e) {
        e.preventDefault();

        if (this.vdom.hasAttribute('data-target')) {
            switch (this.vdom.getAttribute('data-target')) {
                case TAB_SCOPE_CONTACT:
                    dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_CONTACT);
                    break;
                case TAB_SCOPE_PREFERENCES:
                    dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES);
                    break;
                case TAB_SCOPE_PRIVACY:
                    dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PRIVACY);
                    break;
                default:
                    dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES);
            }
        } else {
            dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES);
        }
    }
}

export default CookieModalTrigger;
