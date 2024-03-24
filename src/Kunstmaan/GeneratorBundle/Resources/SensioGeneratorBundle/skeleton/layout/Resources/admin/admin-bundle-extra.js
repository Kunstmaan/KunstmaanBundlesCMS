{% if not demosite %}
/* global $:readonly */
{% endif %}
import './admin-style.scss';

document.onreadystatechange = () => {
    // if you want to use jQuery
    if (document.readyState === 'complete') {
        initExtraAdminJs();
    }
};

function initExtraAdminJs() {
    console.log($);
}
