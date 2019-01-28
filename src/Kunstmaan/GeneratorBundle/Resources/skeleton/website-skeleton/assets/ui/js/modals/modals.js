import { sliceArray } from '../helpers/sliceArray';
import { CLASSES } from './modals.config';


export function modal() {
    const HOOKS = sliceArray(document.querySelectorAll(CLASSES.COMPONENT));

    HOOKS.forEach((HOOK) => {
        const target = document.querySelector(HOOK.dataset.target);

        HOOK.addEventListener('click', (e) => {
            clickHandler(e, target);
        });
    });
}

function clickHandler(e, target) {
    e.preventDefault();

    if (target.classList.contains(CLASSES.ACTIVE)) {
        target.classList.remove(CLASSES.ACTIVE);
    } else {
        target.classList.add(CLASSES.ACTIVE);
    }
}
