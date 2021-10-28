/* global document */

import { post } from './xhr';
import datalayers from './datalayers';
import { select, SELECT_COOKIE_SETTINGS } from '../state';

const COOKIE_NAME = 'legal_cookie';

class cookies {
    static get() {
        const collectedCookies = {};
        document.cookie.split(';')
            .forEach((line) => {
                const [key, value] = line.split('=');
                collectedCookies[key.trim()] = decodeURIComponent(value);
            });

        return collectedCookies;
    }

    static getKmccCookies() {
        const cookieString = cookies.get()[COOKIE_NAME];
        return typeof cookieString !== 'undefined' ? JSON.parse(cookieString).cookies : undefined;
    }

    static hasAllowedDataLayers() {
        const kmccCookies = cookies.getKmccCookies();
        return typeof kmccCookies !== 'undefined' ? kmccCookies.analyzing_cookie : false;
    }

    static toggleAll(url) {
        return post(url).then(() => {
            cookies.sendActivateCookiesEventToGTM();
        });
    }

    static toggleSome(url, data) {
        let dataString = '';
        Object.keys(data).forEach((key, i) => {
            if (i > 0) {
                dataString += '&';
            }
            dataString += `${key}=${data[key]}`;
        });

        return post(url, dataString).then(() => {
            cookies.sendActivateCookiesEventToGTM();
        });
    }

    static sendActivateCookiesEventToGTM() {
        // This is an object of the form:
        // {"cookies":{"functional_cookie":"true","analyzing_cookie":"true","marketing_cookie":"true"}}
        // all of these subcookies need ot be sent to GA to activate the responding cookies.
        const legalCookieContent = getLegalCookieContent();
        datalayers.sendEnableCookieEvent(legalCookieContent);
    }
}

function getLegalCookieContent() {
    if (Object.prototype.hasOwnProperty.call(cookies.get(), COOKIE_NAME)) {
        return JSON.parse(cookies.get()[COOKIE_NAME]).cookies;
    }

    return select(SELECT_COOKIE_SETTINGS);
}

export default cookies;
