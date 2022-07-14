import { sliceArray } from '../helpers/sliceArray';
import { CLASSES } from './tabs.config';

export function tabs() {
    const TABS = sliceArray(document.querySelectorAll(CLASSES.COMPONENT));

    TABS.forEach((tab) => {
        init(tab);
    });
}

function init(tab) {
    const hooks = sliceArray(tab.querySelectorAll(CLASSES.SWITCH));
    const currentActive = tab.querySelector(`.${CLASSES.ACTIVE}`);

    loadContent(tab, currentActive);

    hooks.forEach((hook) => {
        hook.addEventListener('click', (e) => {
            clickHandler(e, tab);
        });
    });
}

function loadContent(tab, currentActive) {
    const url = currentActive.getAttribute('data-content-url');
    const contentHolder = tab.querySelector(CLASSES.CONTENT);

    getContent(url).then((newContent) => {
        contentHolder.innerHTML = newContent;
    });
}

function getContent(url) {
    const promiseObj = new Promise((resolve) => {
        const xhr = new XMLHttpRequest();

        xhr.open('GET', url, true);
        xhr.send();

        xhr.onreadystatechange = () => {
            const finished = xhr.readyState === 4;
            const ok = xhr.status === 200;

            if (finished && ok) {
                resolve(xhr.responseText);
            }
        };
    });

    return promiseObj;
}

function clickHandler(e, tab) {
    const newActive = e.currentTarget;
    const currentActive = tab.querySelector(`.${CLASSES.ACTIVE}`);

    currentActive.classList.remove(CLASSES.ACTIVE);
    newActive.classList.add(CLASSES.ACTIVE);

    loadContent(tab, newActive);
}
