import { SELECTORS, CROPPER_CONFIG } from './config';
import { MediaCropper } from './MediaCropper';


function initMediaCroppers(container = window.document) {
    const PREVIEW_BTNS = [...container.querySelectorAll(SELECTORS.HOOK)];

    PREVIEW_BTNS.forEach((btn) => {
        btn.addEventListener('modalOpen', (e) => {
            const targetModal = e.detail;
            const node = targetModal.querySelector(SELECTORS.CONTAINER);

            new MediaCropper(node, CROPPER_CONFIG);
        });
    })

}

export { initMediaCroppers };
