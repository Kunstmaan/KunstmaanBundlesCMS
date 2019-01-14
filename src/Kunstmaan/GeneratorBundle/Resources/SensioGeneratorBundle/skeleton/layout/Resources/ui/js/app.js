{% if demosite %}
import velocity from 'velocity-animate'; // eslint-disable-line

import cbScrollToTop from 'cargobay/src/scroll-to-top/js/jquery.scroll-to-top';
import cbSidebarToggle from 'cargobay/src/sidebar-toggle/js/jquery.sidebar-toggle';
import cbToggle from 'cargobay/src/toggle/js/jquery.toggle';

import search from './search';
import demoMsg from './demoMsg';
{% endif %}
import cookieConsent from './cookieConsent';
import videolink from './videolink';

{% if demosite %}
$(() => {
    cbToggle.init();
    cbScrollToTop.init();
    cbSidebarToggle.init();

    search();
    demoMsg();
    cookieConsent();
    videolink();
});

{% else %}
document.addEventListener('DOMContentLoaded', () => {
    cookieConsent();
    videolink();
});
{% endif %}
