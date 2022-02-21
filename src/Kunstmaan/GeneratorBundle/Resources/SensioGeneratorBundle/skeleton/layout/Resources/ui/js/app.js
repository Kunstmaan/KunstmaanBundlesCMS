{% set pathPrefix = './' %}
{% if not groundcontrol -%}
{% set pathPrefix = './js/' %}
{% endif %}
import 'picturefill';
{% if demosite %}
import 'velocity-animate';

import cbScrollToTop from 'cargobay/src/scroll-to-top/js/jquery.scroll-to-top';
import cbSidebarToggle from 'cargobay/src/sidebar-toggle/js/jquery.sidebar-toggle';
import cbToggle from 'cargobay/src/toggle/js/jquery.toggle';
{% endif %}

{% if not groundcontrol %}
import './scss/style.scss';

{% endif %}
{% if demosite %}
import search from '{{ pathPrefix }}search';
import demoMsg from '{{ pathPrefix }}demoMsg';
{% endif %}
import cookieConsent from '{{ pathPrefix }}cookieConsent';
import videolink from '{{ pathPrefix }}videolink';

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
