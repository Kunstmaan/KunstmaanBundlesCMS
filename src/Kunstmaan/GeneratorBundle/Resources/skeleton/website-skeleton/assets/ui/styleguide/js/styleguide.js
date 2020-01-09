import 'prismjs';
import 'prismjs/components/prism-markup';
import Clipboard from 'clipboard';

import Scrollspy from './Scrollspy';
import MobileNav from './MobileNav';
import Toggle from './Toggle';

document.addEventListener('DOMContentLoaded', () => {
    new Clipboard('.js-clipboard-code');

    new Scrollspy();
    new MobileNav();
    new Toggle();
});
