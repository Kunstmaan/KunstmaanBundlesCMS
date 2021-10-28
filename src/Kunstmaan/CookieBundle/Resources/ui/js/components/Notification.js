import Component from './Component';

import { NOTIFICATION_IDENTIFIER, CLASSES } from '../config/notification.config';
import {
    dispatch,
    SET_VISIBILITY_SCOPE_TO_NONE,
} from '../state';
import { NOTIFICATION_VISIBILITY_SCOPE } from '../state/state.config';

class Notification extends Component {
    constructor() {
        super({
            identifier: NOTIFICATION_IDENTIFIER,
            configuration: {
                visibilityScopes: {
                    [NOTIFICATION_VISIBILITY_SCOPE]: [CLASSES.VISIBLE],
                },
            },
        });
    }

    show({ visibilityScope }) {
        super.show({ visibilityScope });
        // set timeout to revert notification.
        setTimeout(() => {
            dispatch(SET_VISIBILITY_SCOPE_TO_NONE);
        }, 2000);
    }
}

export default Notification;
