import Cropper from 'cropperjs';
import { SELECTORS, MODIFIERS, META_KEYS, CROP_BOX_THRESHOLD } from './config';
class MediaCropper {
    constructor(node, CROPPER_CONFIG) {
        this.node = node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.metaValueNodes = {};
        this.cropper = new Cropper(this.image, CROPPER_CONFIG);

        this.getValueNodes();

        this.image.addEventListener('crop', () => {
            const data = this.getData();
            this.updateValue(data);
        });
    }

    getValueNodes() {
        META_KEYS.forEach((key) => {
            this.metaValueNodes[key] = this.metaContainer.querySelector(`${SELECTORS.META_ITEM}-${key}`);
        });
    }

    getData() {
        return this.cropper.getData([true]);
    }

    updateValue({x, y, width, height}) {
        let small_crop_box_area = false;

        this.metaValueNodes.x.textContent = x;
        this.metaValueNodes.y.textContent = y;
        this.metaValueNodes.width.textContent = width;
        this.metaValueNodes.height.textContent = height;

        if ((width || height) <= CROP_BOX_THRESHOLD && !small_crop_box_area) {
            this.cropper.cropBox.classList.add(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = true;
        } else {
            this.cropper.cropBox.classList.remove(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = false;
        }
    }
}

export { MediaCropper };
