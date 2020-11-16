import { SELECTORS } from './config';
import { EditImage } from './EditImage';

function initMediaCroppers() {
    document.addEventListener('modalOpen', (e) => {
        const targetModal = e.detail;
        const node = targetModal.querySelector(SELECTORS.CONTAINER);

        if (!node.hasAttribute('data-initialized')) {
            new EditImage(node);
        }
    });
}


export { initMediaCroppers };
