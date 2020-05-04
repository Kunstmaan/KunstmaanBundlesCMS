import Cropper from 'cropperjs';
import { SELECTORS, MODIFIERS, META_KEYS, CROP_BOX_THRESHOLD, CROPPER_CONFIG } from './config';
import { renderViewSelectOptions } from './renderViewSelectOptions';

class MediaCropper {
    constructor(EditImage) {
        this.EditImage = EditImage;
        this.node = EditImage.node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.viewSelect = this.EditImage.metaContainer.querySelector(SELECTORS.VIEW_SELECT);
        this.metaValueNodes = {};
        this.cropper = null;
        this.viewData = {};
    }

    getValueNodes() {
        META_KEYS.forEach((key) => {
            this.metaValueNodes[key] = this.EditImage.metaContainer.querySelector(`${SELECTORS.META_ITEM}-${key}`);
        });
    }

    updateValue({x, y, width, height}) {
        let small_crop_box_area = false;
        const values = {
            x: Math.ceil(x),
            y: Math.ceil(y),
            width: Math.ceil(width),
            height: Math.ceil(height),
        }

        this.metaValueNodes.width.textContent = values.width;
        this.metaValueNodes.height.textContent = values.height;

        if ((width || height) <= CROP_BOX_THRESHOLD && !small_crop_box_area) {
            this.node.classList.add(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = true;
        } else {
            this.node.classList.remove(MODIFIERS.CROP_BOX_SMALL_CROPPED_AREA);
            small_crop_box_area = false;
        }

        if (this.viewData && this.EditImage.currentCropView) {
            if (!this.EditImage.editData.hasOwnProperty(this.EditImage.currentCropView)) {
                this.EditImage.editData[this.EditImage.currentCropView] = {};
            }
            this.EditImage.editData[this.EditImage.currentCropView].start = [values.x, values.y];
            this.EditImage.editData[this.EditImage.currentCropView].size = [values.width, values.height];
        }

        this.EditImage.croppedImageUrl = this.cropper.getCroppedCanvas().toDataURL('image/jpeg');

    }

    addEventListeners() {
        this.image.addEventListener('crop', () => {
            const data = this.cropper.getData();
            this.updateValue(data);
        });

        this.viewSelect.addEventListener('change', () => {
            this.EditImage.currentCropView = this.viewSelect.value;
            this.cropper.destroy();
            if (this.EditImage.hasFocusSelect) {
                this.EditImage.components.focus.component.reset();
            }
            this.initCropper();
        });
    }

    initCropper() {
        const entries = Object.entries(this.viewData[this.EditImage.currentCropView]);
        const config = CROPPER_CONFIG;

        for (const [key, value] of entries) {
            config[key] = value;
        }

        if (this.EditImage.editData.hasOwnProperty(this.EditImage.currentCropView)) {
            const savedValues = this.EditImage.editData[this.EditImage.currentCropView];

            config.data = {
                x: savedValues.start[0],
                y: savedValues.start[1],
                width: savedValues.size[0],
                height: savedValues.size[1],
            };
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

            this.EditImage.currentCropView = this.viewSelect.value;
        }

        if (this.EditImage.imagePath) {
            this.image.src = this.EditImage.imagePath;
        }

        this.initCropper();
        this.addEventListeners();
    }
}

export { MediaCropper };
