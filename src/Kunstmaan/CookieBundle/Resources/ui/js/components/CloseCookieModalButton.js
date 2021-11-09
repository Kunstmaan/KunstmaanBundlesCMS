import Component from './Component';

import { MODAL_CLOSE_BUTTON_IDENTIFIER } from '../config/modalCloseButton.config';
import {
    dispatch,
} from '../state';
import { getBackToBasicModalVisibilityScope } from '../utils';

class CloseCookieModalButton extends Component {
    constructor({ configuration }) {
        super({
            identifier: MODAL_CLOSE_BUTTON_IDENTIFIER,
            configuration,
            eventListeners: {
                click: 'closeCookieModal',
            },
        });
    }

    closeCookieModal() {
        const { isOnCookiePage } = this.configuration;
        dispatch(getBackToBasicModalVisibilityScope({ isOnCookiePage }));
    }
}

export default CloseCookieModalButton;
