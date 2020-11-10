import 'svgxuse/svgxuse';
import { initPagePartChoosers } from './PagePartChooser';
import { initWysiwygEditors } from './WysiwygEditor';

function init() {
    initPagePartChoosers();
    initWysiwygEditors();
}

// This script is loaded dynamically, so it could be that
// DOMContentLoaded was already fired when this script is executed
if (document.readyState !== 'loading') {
    init();
} else {
    document.addEventListener('DOMContentLoaded', () => {
        init();
    });
}
