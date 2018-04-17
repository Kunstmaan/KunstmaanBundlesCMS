const COOKIEBAR_ID = 'cookie-bar';
const COOKIEBAR_BUTTON_ID = 'cookie-bar__consent-btn';
const COOKIEBAR_VISIBLE_CLASS = 'cookie-bar--visible';

let cookieBar;

function cookieConsent() {
    cookieBar = document.getElementById(COOKIEBAR_ID);

    if (cookieBar) {
        init();
    }
}

function init() {
    const cookieBarConsentBtn = document.getElementById(COOKIEBAR_BUTTON_ID);
    const hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)bundles-cookie-consent\s*=\s*([^;]*).*$)|^.*$/)[1];

    if ((typeof hasCookie === 'undefined' || hasCookie === 'false') && cookieBar) {
        cookieBar.classList.add(COOKIEBAR_VISIBLE_CLASS);
    }

    if (cookieBarConsentBtn) {
        cookieBarConsentBtn.addEventListener('click', (event) => {
            event.preventDefault();
            document.cookie = 'bundles-cookie-consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
            cookieBar.classList.remove(COOKIEBAR_VISIBLE_CLASS);
        });
    }
}

export default cookieConsent;
