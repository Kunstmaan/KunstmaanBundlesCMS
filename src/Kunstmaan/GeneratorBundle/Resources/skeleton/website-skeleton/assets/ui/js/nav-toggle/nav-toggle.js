import { sliceArray } from '../helpers/sliceArray';
import { CLASSES } from './nav-toggle.config';

export function navToggle() {
    const HOOKS = sliceArray(document.querySelectorAll(CLASSES.COMPONENT));

    HOOKS.forEach((HOOK) => {
        HOOK.addEventListener('click', clickHandler);
    });
}

function clickHandler(e) {
    const current = e.currentTarget;

    if (current.classList.contains(CLASSES.ACTIVE)) {
        current.classList.remove(CLASSES.ACTIVE);
    } else {
        current.classList.add(CLASSES.ACTIVE);
    }
}
