import { sliceArray } from './sliceArray';

const focusable = 'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])';

function disableFocus(TARGET) {
    const focusableNodes = sliceArray(TARGET.querySelectorAll(focusable));

    focusableNodes.forEach((focusableNode) => {
        const node = focusableNode;

        node.tabIndex = '-1';
    });
}

function enableFocus(TARGET) {
    const focusableNodes = sliceArray(TARGET.querySelectorAll(focusable));

    focusableNodes.forEach((focusableNode) => {
        const node = focusableNode;

        node.tabIndex = '0';
    });
}


export {
    disableFocus,
    enableFocus,
};
