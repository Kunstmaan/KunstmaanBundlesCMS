import cookies from '../services/cookies';
import { SET_VISIBILITY_SCOPE_TO_COOKIE_BAR, SET_VISIBILITY_SCOPE_TO_NONE } from '../state';

export function getBackToBasicModalVisibilityScope({ isOnCookiePage }) {
    if (isOnCookiePage || typeof cookies.getKmccCookies() !== 'undefined') {
        return SET_VISIBILITY_SCOPE_TO_NONE;
    }

    return SET_VISIBILITY_SCOPE_TO_COOKIE_BAR;
}
