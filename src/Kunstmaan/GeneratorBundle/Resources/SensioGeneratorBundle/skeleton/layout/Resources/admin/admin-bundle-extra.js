{% if groundcontrol %}
/**
 *
 * Extra Javascript that needs to be implemented for the KunstmaanBundlesCMS can be written in this file/folder.
 *
 * The Javascript will be bundled & compiled when the `npm run build` command has run. The compiled JS will be at:
 * public/build/js/admin-bundle-extra.js and will be included automatically in all of the KunstmaanBundlesCMS layouts.
 *
 */
{% else %}
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
{% endif %}
