import Component from './Component';

import {
    select,
    dispatch,
    SELECT_TAB_SCOPE,
    SET_TAB_SCOPE_CONTACT,
    SET_TAB_SCOPE_PREFERENCES,
    SET_TAB_SCOPE_PRIVACY,
} from '../state';

import {
    TAB_SCOPE_CONTACT,
    TAB_SCOPE_PREFERENCES,
    TAB_SCOPE_PRIVACY,
} from '../state/state.config';

class Tab extends Component {
    constructor({ vdom }) {
        super({
            vdom,
            eventListeners: {
                click: 'handleTabClick',
            },
        });
    }

    handleComponentState(state) {
        super.handleComponentState(state);

        this.checkIfTabHasToBeActivated();
    }

    handleTabClick() {
        switch (true) {
            case this.vdom.id === TAB_SCOPE_CONTACT:
                dispatch(SET_TAB_SCOPE_CONTACT);
                break;
            case this.vdom.id === TAB_SCOPE_PREFERENCES:
                dispatch(SET_TAB_SCOPE_PREFERENCES);
                break;
            case this.vdom.id === TAB_SCOPE_PRIVACY:
                dispatch(SET_TAB_SCOPE_PRIVACY);
                break;
            default:
                dispatch(SET_TAB_SCOPE_PREFERENCES);
        }
    }

    checkIfTabHasToBeActivated() {
        const { tabScope } = select(SELECT_TAB_SCOPE);

        if (this.vdom.id === tabScope) {
            this.vdom.checked = true;
        } else {
            this.vdom.checked = false;
        }
    }
}

export default Tab;
