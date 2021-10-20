/* global window */

import Component from './Component';

import { KEYCODE_ESCAPE } from '../config/keycodes.config';
import {
    BACKDROP_IDENTIFIER,
    CLASSES,
} from '../config/backdrop.config';
import {
    dispatch,
    select,
    SELECT_VISIBILITY_SCOPE,
} from '../state';
import {
    COOKIE_MODAL_VISIBILITY_SCOPE,
    COOKIE_MODAL_VISIBILITY_SCOPE_DETAIL,
} from '../state/state.config';
import { getBackToBasicModalVisibilityScope } from '../utils';

class BackDrop extends Component {
    constructor({ configuration }) {
        super({
            identifier: BACKDROP_IDENTIFIER,
            configuration: Object.assign(configuration, {
                visibilityScopes: {
                    [COOKIE_MODAL_VISIBILITY_SCOPE]: [CLASSES.VISIBLE],
                    [COOKIE_MODAL_VISIBILITY_SCOPE_DETAIL]: [CLASSES.VISIBLE],
                },
            }),
            eventListeners: {
                click: 'backToBasicModal',
            },
        });

        this.backToBasicModalBound = this.backToBasicModal.bind(this);
    }

    addAllEventListeners() {
        const { visibilityScope } = select(SELECT_VISIBILITY_SCOPE);
        if (
            !this.hasEventsConfigured &&
            Object.keys(this.configuration.visibilityScopes).indexOf(visibilityScope) >= 0
        ) {
            super.addAllEventListeners();

            window.addEventListener('keyup', this.backToBasicModalBound);
            this.hasEventsConfigured = true;
        }
    }

    removeAllEventListeners() {
        super.removeAllEventListeners();

        window.removeEventListener('keyup', this.backToBasicModalBound);
        this.hasEventsConfigured = false;
    }

    backToBasicModal(e) {
        if ((e.type === 'keyup' && e.keyCode === KEYCODE_ESCAPE) || e.type === 'click') {
            const { isOnCookiePage } = this.configuration;
            dispatch(getBackToBasicModalVisibilityScope({ isOnCookiePage }));
        }
    }
}

export default BackDrop;
