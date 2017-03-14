{% if demosite %}
import velocity from 'velocity-animate'; // eslint-disable-line

import cbScrollToTop from 'cargobay/src/scroll-to-top/js/jquery.scroll-to-top';
import cbSidebarToggle from 'cargobay/src/sidebar-toggle/js/jquery.sidebar-toggle';
import cbToggle from 'cargobay/src/toggle/js/jquery.toggle';

import search from './search';
import demoMsg from './demoMsg';
{% endif %}
import CookieConsent from './CookieConsent';
import Videolink from './Videolink';

{% if demosite %}
$(function() {
    cbToggle.init();
    cbScrollToTop.init();
    cbSidebarToggle.init();

    search.init();
    demoMsg.init();
    new CookieConsent();
    new Videolink();
});

{% else %}
document.addEventListener('DOMContentLoaded', () => {
    new CookieConsent();
    new Videolink();
});
{% endif %}
