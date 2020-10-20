import { SELECTORS } from './config';
import { initSideNav } from './initSideNav';

export default function SideNav() {
    const navHolder = document.querySelector(SELECTORS.NAV_HOLDER);

    initSideNav(navHolder);
}
