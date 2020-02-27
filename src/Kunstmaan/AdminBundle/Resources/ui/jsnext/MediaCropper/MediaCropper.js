import Cropper from 'cropperjs';
import { SELECTORS, MODIFIERS, META_KEYS, CROP_BOX_THRESHOLD } from './config';
import { renderViewSelectOptions } from './renderViewSelectOptions';
class MediaCropper {
    constructor(node, CROPPER_CONFIG) {
        this.node = node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.viewSelect = this.metaContainer.querySelector(SELECTORS.VIEW_SELECT);
        this.metaValueNodes = {};
        this.cropper = new Cropper(this.image, CROPPER_CONFIG);
        this.views = this.node.hasAttribute('data-cropping-views');
        this.viewData = {};


        this.init();
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
            this.node.classList.add(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = true;
        } else {
            this.node.classList.remove(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = false;
        }

        if (this.viewData && this.currentView) {
            this.cropData[this.currentView].x = x;
            this.cropData[this.currentView].y = y;
            this.cropData[this.currentView].width = width;
            this.cropData[this.currentView].height = height;
        }
    }

    addEventListeners() {
        this.image.addEventListener('crop', () => {
            const data = this.getData();
            this.updateValue(data);
        });
    }

    init() {
        this.getValueNodes();

        if (this.views) {
            const viewData = JSON.parse(this.node.dataset.croppingViews);
            viewData.forEach((view) => {
                this.viewData[view.name] = {};
                this.viewData[view.name].aspectRatio = view.height / view.width;
                this.viewData[view.name].width = view.width;
                this.viewData[view.name].height = view.height;
            });
            renderViewSelectOptions(this.viewSelect, this.viewData);
        }

        this.addEventListeners();

        console.log(this);
    }
}

export { MediaCropper };
