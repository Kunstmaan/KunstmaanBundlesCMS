import Component from './Component';
import { dispatch, SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES } from '../state';

import { CLOSE_DETAIL_BUTTON_IDENTIFIER } from '../config/closeDetailButton.config';


class CloseDetailButton extends Component {
    constructor() {
        super({
            identifier: CLOSE_DETAIL_BUTTON_IDENTIFIER,
            eventListeners: {
                click: 'handleCloseDetail',
            },
        });
    }

    /* disable rule because it breaks component functionality */
    /* eslint class-methods-use-this: 0 */
    handleCloseDetail(e) {
        e.preventDefault();
        dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_PREFERENCES);
    }
}

export default CloseDetailButton;
