import Cropper from 'cropperjs';
import { SELECTORS, MODIFIERS, META_KEYS, CROP_BOX_THRESHOLD, CROPPER_CONFIG } from './config';
import { renderViewSelectOptions } from './renderViewSelectOptions';

class MediaCropper {
    constructor(EditImage) {
        this.EditImage = EditImage;
        this.node = EditImage.node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.imagePath = this.node.hasAttribute('data-path') ? this.node.dataset.path : false;
        this.viewSelect = this.EditImage.metaContainer.querySelector(SELECTORS.VIEW_SELECT);
        this.metaValueNodes = {};
        this.cropper = null;
        this.viewData = {};
        this.EditImage.cropData = {};
        this.savedCropData = this.EditImage.input.value !== '' ? JSON.parse(this.EditImage.input.value) : false;
        this.initialized = false;
    }

    getValueNodes() {
        META_KEYS.forEach((key) => {
            this.metaValueNodes[key] = this.EditImage.metaContainer.querySelector(`${SELECTORS.META_ITEM}-${key}`);
        });
    }

    updateValue({x, y, width, height}) {
        let small_crop_box_area = false;

        this.metaValueNodes.width.textContent = Math.ceil(width);
        this.metaValueNodes.height.textContent = Math.ceil(height);

        if ((width || height) <= CROP_BOX_THRESHOLD && !small_crop_box_area) {
            this.node.classList.add(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = true;
        } else {
            this.node.classList.remove(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = false;
        }

        if (this.viewData && this.currentView) {
            if (!this.EditImage.cropData.hasOwnProperty(this.currentView)) {
                this.EditImage.cropData[this.currentView] = {};
            }
            this.EditImage.cropData[this.currentView].start = [x, y];
            this.EditImage.cropData[this.currentView].size = [width, height];
        }

        this.EditImage.croppedImageUrl = this.cropper.getCroppedCanvas().toDataURL('image/jpeg');
        console.log(this.EditImage.cropData);
    }

    addEventListeners() {
        this.image.addEventListener('crop', () => {
            const data = this.cropper.getData();
            this.updateValue(data);
        });

        this.viewSelect.addEventListener('change', () => {
            this.currentView = this.viewSelect.value;
            this.cropper.destroy();
            // if (this.selectableFocusPoint) {
            //     this.focusPointComponent.reset();
            //     this.focusPointComponent.toggleVisibility();
            // }
            this.initCropper();
        });

        // this.save.addEventListener('click', (e) => {
        //     e.preventDefault();
        //     this.input.value = JSON.stringify(this.cropData);
        // });
    }

    initCropper() {
        const entries = Object.entries(this.viewData[this.currentView]);
        const config = CROPPER_CONFIG;

        for (const [key, value] of entries) {
            config[key] = value;
        }

        if (this.EditImage.cropData.hasOwnProperty(this.currentView)) {
            const savedValues = this.EditImage.cropData[this.currentView];

            config.data = {
                x: savedValues.start[0],
                y: savedValues.start[1],
                width: savedValues.size[0],
                height: savedValues.size[1],
            };

            // if (savedValues.hasOwnProperty('class')) {
            //     this.focusPointComponent.setSelectedFocus(savedValues.class);
            //     this.focusPointComponent.setChoice(savedValues.class);
            // }
        } else {
            config.data = null;
        }

        this.cropper = new Cropper(this.image, config);
    }

    init() {
        this.getValueNodes();

        const viewData = JSON.parse(this.node.dataset.croppingViews);
        if (viewData.length > 0) {
            viewData.forEach((view) => {
                this.viewData[view.name] = {};
                this.viewData[view.name].aspectRatio = view.lockRatio ? view.height / view.width : NaN;
                this.viewData[view.name].minCropBoxWidth = view.width ? view.width : 200;
                this.viewData[view.name].minCropBoxHeight = view.height ? view.height : 100;
            });
            renderViewSelectOptions(this.viewSelect, this.viewData);

            this.currentView = this.viewSelect.value;
        }


        if (this.imagePath) {
            this.image.src = this.imagePath;
        }

        if (this.savedCropData) {
            this.EditImage.cropData = this.savedCropData;
        }

        this.initCropper();
        this.addEventListeners();

        this.initialized = true;
        this.node.dataset.initialized = true;
    }
}

export { MediaCropper };
