import Component from './Component';

import { COOKIE_BAR_VISIBILITY_SCOPE } from '../state/state.config';
import {
    COOKIEBAR_IDENTIFIER,
    CLASSES,
} from '../config/cookiebar.config';

class CookieBar extends Component {
    constructor() {
        super({
            identifier: COOKIEBAR_IDENTIFIER,
            configuration: {
                visibilityScopes: {
                    [COOKIE_BAR_VISIBILITY_SCOPE]: [CLASSES.VISIBLE],
                },
            },
        });
    }
}

export default CookieBar;
