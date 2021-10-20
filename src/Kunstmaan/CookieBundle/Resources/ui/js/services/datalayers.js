/* global window, document */

import { VISITOR_TYPE_IDENTIFIER } from '../config/visitorType.config';

class datalayers {
    static push(eventObject) {
        if (typeof window.dataLayer !== 'undefined' && Array.isArray(window.dataLayer)) {
            window.dataLayer.push(eventObject);
        } else {
            throw new Error('Could not find dataLayer on window.');
        }
    }

    static sendEnableCookieEvent(cookieObject) {
        datalayers.push({
            event: 'enableCookie',
            attributes: cookieObject,
        });
    }

    static sendIpAddressEvent() {
        const visitorType = document.getElementById(VISITOR_TYPE_IDENTIFIER);
        if (visitorType === null) {
            return;
        }

        datalayers.push({
            event: 'ip-address',
            type: visitorType.value,
        });
    }
}

export default datalayers;
