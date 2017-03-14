export default class CookieConsent {

    constructor() {
        const cookieBar = document.getElementById('cookie-bar');
        const cookieBarConsentBtn = document.getElementById('cookie-bar__consent-btn');
        const hasCookie = document.cookie.match(/(?:(?:^|.*;\s*)bundles-cookie-consent\s*=\s*([^;]*).*$)|^.*$/)[1];

        if ((typeof hasCookie === 'undefined' || hasCookie === 'false') && cookieBar) {
            cookieBar.classList.add('cookie-bar--visible');
        }

        if (cookieBarConsentBtn) {
            cookieBarConsentBtn.addEventListener('click', (event) => {
                event.preventDefault();
                document.cookie = 'bundles-cookie-consent=true; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/';
                cookieBar.classList.remove('cookie-bar--visible');
            });
        }
    }
}
