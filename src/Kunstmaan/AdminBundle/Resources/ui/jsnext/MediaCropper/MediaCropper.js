import Cropper from 'cropperjs';
import { SELECTORS, MODIFIERS, META_KEYS, CROP_BOX_THRESHOLD } from './config';
import { renderViewSelectOptions } from './renderViewSelectOptions';
class MediaCropper {
    constructor(node, CROPPER_CONFIG) {
        this.node = node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.viewSelect = this.metaContainer.querySelector(SELECTORS.VIEW_SELECT);
        this.save = this.metaContainer.querySelector(SELECTORS.SAVE);
        this.input = null;
        this.metaValueNodes = {};
        this.cropperConfig = CROPPER_CONFIG;
        this.cropper = null;
        this.viewData = {};
        this.cropData = {};


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
            if (!this.cropData.hasOwnProperty(this.currentView)) {
                this.cropData[this.currentView] = {};
            }
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

        this.viewSelect.addEventListener('change', () => {
            this.currentView = this.viewSelect.value;
            this.cropper.destroy();
            this.initCropper();
        });

        this.save.addEventListener('click', (e) => {
            e.preventDefault();
            this.cropper.destroy();
            console.log(this.cropData);
        });
    }

    initCropper() {
        const entries = Object.entries(this.viewData[this.currentView]);

        for (const [key, value] of entries) {
            this.cropperConfig[key] = value;
        }

        this.cropper = new Cropper(this.image, this.cropperConfig);
    }

    init() {
        this.getValueNodes();

        const viewData = JSON.parse(this.node.dataset.croppingViews);
        if (viewData.length > 0) {
            viewData.forEach((view) => {
                this.viewData[view.name] = {};
                this.viewData[view.name].aspectRatio = view.lockRatio ? view.height / view.width : NaN;
                this.viewData[view.name].minContainerWidth = view.width ? view.width : 200;
                this.viewData[view.name].minContainerHeight = view.height ? view.height : 100;
            });
            renderViewSelectOptions(this.viewSelect, this.viewData);

            this.currentView = this.viewSelect.value;
        }

        this.initCropper();
        this.addEventListeners();
    }
}

export { MediaCropper };
