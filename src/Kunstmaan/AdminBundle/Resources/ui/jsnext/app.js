import 'svgxuse/svgxuse';
import PagePartChooser from './PagePartChooser';

function init() {
    PagePartChooser.init();
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
