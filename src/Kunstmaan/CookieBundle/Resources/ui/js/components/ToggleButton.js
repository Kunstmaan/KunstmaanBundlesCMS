import Component from './Component';

import { dispatch, UPDATE_COOKIE_SETTING_VALUE } from '../state';

class ToggleButton extends Component {
    constructor({ vdom, configuration }) { // {configuration: {stateIdentifier: String}}
        super({
            vdom,
            configuration,
            eventListeners: {
                click: 'updateStateValueForToggle',
            },
        });
    }

    updateStateValueForToggle() {
        const value = this.vdom.checked;
        dispatch(UPDATE_COOKIE_SETTING_VALUE, { type: this.configuration.stateIdentifier, value });
    }
}

export default ToggleButton;
