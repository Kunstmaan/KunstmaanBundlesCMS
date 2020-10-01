import PagePartChooser from './PagePartChooser';
import 'svgxuse/svgxuse';

function init() {
    PagePartChooser.init();
}

// This script is loaded dynamically, so it could be that DOMContentLoaded was already fired when this script is executed
if (document.readyState !== 'loading') {
    init();
} else {
    document.addEventListener('DOMContentLoaded', () => {
        init();
    });
}
