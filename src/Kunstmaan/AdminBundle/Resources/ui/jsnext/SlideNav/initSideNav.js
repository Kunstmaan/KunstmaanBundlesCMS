import { MODIFIERS, SELECTORS, SESSION_ITEM_NAME } from "./config";

export function initSideNav(holder) {
    const keepOpenControl = holder.querySelector(SELECTORS.KEEP_OPEN_CONTROL);
    const focusableItems = [...holder.querySelectorAll(SELECTORS.FOCUSABLE_NAV_ITEM)];
    let keepOpen = checkSessionStorage();

    setKeepOpenControl({ keepOpenControl, keepOpen });
    handleKeepOpen({ holder, keepOpen });

    keepOpenControl.addEventListener('change', () => {
        keepOpen = keepOpenControl.checked;
        handleKeepOpen({ holder, keepOpen });
    });

    focusableItems.forEach((item) => {
        item.addEventListener('focus', () => {
            const isOpen = holder.classList.contains(...[MODIFIERS.OPEN, MODIFIERS.KEEP_OPEN]);

            if (!isOpen) {
                open(holder);
            }
        });

        item.addEventListener('blur', () => {
            const isOpen = holder.classList.contains(...[MODIFIERS.OPEN, MODIFIERS.KEEP_OPEN]);
            const { activeElement } = document;
            const focusClass = SELECTORS.FOCUSABLE_NAV_ITEM.split('.')[1];

            if (!activeElement.classList.contains(focusClass) && isOpen && !keepOpen) {
                close(holder);
            }
        })
    })
}

function setKeepOpenControl({ keepOpenControl, keepOpen }) {
    keepOpenControl.checked = keepOpen;
}

function handleKeepOpen({ holder, keepOpen }) {
    if (keepOpen) {
        open(holder);
        setSessionStorage();
    } else {
        close(holder);
        clearSessionStorage();
    }
}

function open(holder) {
    holder.classList.add(...[MODIFIERS.OPEN, MODIFIERS.KEEP_OPEN]);
}

function close(holder) {
    holder.classList.remove(...[MODIFIERS.OPEN, MODIFIERS.KEEP_OPEN]);
}

function checkSessionStorage() {
    if (sessionStorage.getItem(SESSION_ITEM_NAME)) {
        return true;
    }

    return false;
}

function setSessionStorage() {
    if (!sessionStorage.getItem(SESSION_ITEM_NAME)) {
        sessionStorage.setItem(SESSION_ITEM_NAME, true);
    }
}

function clearSessionStorage() {
    if (sessionStorage.getItem(SESSION_ITEM_NAME)) {
        sessionStorage.removeItem(SESSION_ITEM_NAME);
    }
}
