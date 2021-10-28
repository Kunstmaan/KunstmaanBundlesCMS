import Component from './Component';
import { get } from '../services/xhr';

import { dispatch, SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_DETAIL } from '../state';

class ToggleLink extends Component {
    constructor({ vdom }) {
        super({
            vdom,
            eventListeners: {
                click: 'handleShowDetailView',
            },
        });

        this.detailContent = this.loadRespondingContent();
    }

    loadRespondingContent() {
        return new Promise((resolve, reject) => {
            if (this.vdom.hasAttribute('href')) {
                const detailContentUrl = this.vdom.href;
                get(detailContentUrl).then((res) => {
                    resolve(res.response);
                });
            } else {
                reject(new Error('no href attribute specified'));
            }
        });
    }

    handleShowDetailView(e) {
        e.preventDefault();

        this.detailContent.then((content) => {
            dispatch(SET_VISIBILITY_SCOPE_TO_COOKIE_MODAL_DETAIL, { content });
        }).catch((error) => {
            throw new Error(error);
        });
    }
}

export default ToggleLink;
