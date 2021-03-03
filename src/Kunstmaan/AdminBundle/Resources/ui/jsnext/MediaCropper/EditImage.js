import { SELECTORS, MODIFIERS } from './config';
import { MediaCropper } from './MediaCropper';
import { Focuspoint } from './Focuspoint';

class EditImage {
    constructor(node) {
        this.node = node;
        this.hasCropper = node.hasAttribute('data-use-cropping');
        this.hasFocusSelect = node.hasAttribute('data-focus-point-classes');
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.save = this.metaContainer.querySelector(SELECTORS.SAVE);
        this.input = document.querySelector(`#${node.dataset.inputId}`);
        this.imagePath = this.node.hasAttribute('data-path') ? this.node.dataset.path : false;
        this.initialized = false;
        this.components = {};
        this.editData = {};
        this.viewData = {};
        this.savedEditData = this.input.value !== '' ? JSON.parse(this.input.value) : false;
        this.saveEdit = this.saveEdit.bind(this);
        this.destroy = this.destroy.bind(this);
        this.changeView = this.changeView.bind(this);

        this.init();
    }

    changeView(e) {
        e.preventDefault();

        const currentTextContent = this.viewSwitch.textContent;
        const nextTextContent = this.viewSwitch.dataset.booleanText;

        this.viewSwitch.textContent = nextTextContent;
        this.viewSwitch.dataset.booleanText = currentTextContent;

        switch (this.currentView) {
            case 'focus':
                if (this.components.cropper.component.viewSelect && Object.keys(this.viewData).length > 1) {
                    this.components.cropper.component.viewSelect.disabled = false;
                }

                this.components.focus.wrapper.classList.add(MODIFIERS.FOCUS_HIDDEN);
                this.components.cropper.wrapper.classList.remove(MODIFIERS.CROPPER_HIDDEN);
                this.currentView = 'cropper';
                break;

            default:
                if (this.croppedImageUrl !== 'undefined') {
                    this.components.focus.component.setImage(this.croppedImageUrl);
                }

                if (this.components.cropper.component.viewSelect) {
                    this.components.cropper.component.viewSelect.disabled = true;
                }

                this.components.cropper.wrapper.classList.add(MODIFIERS.CROPPER_HIDDEN);
                this.components.focus.wrapper.classList.remove(MODIFIERS.FOCUS_HIDDEN);
                this.currentView = 'focus';
                break;
        }
    }

    saveEdit(e) {
        e.preventDefault();

        this.input.value = JSON.stringify(this.editData);
    }

    destroy() {
        this.initialized = false;
        this.node.removeAttribute('data-initialized');
        this.save.removeEventListener('click', this.save);

        if (this.hasCropper) {
            this.components.cropper.component.destroy();
        }

        if (this.hasFocusSelect) {
            this.components.focus.component.destroy();
        }

        if (this.hasCropper && this.hasFocusSelect) {
            this.viewSwitch.removeEventListener('click', this.changeView);
        }

        this.node.removeEventListener('destroy', this.destroy);
    }

    init() {
        const viewData = JSON.parse(this.node.dataset.croppingViews);

        if (viewData.length > 0) {
            viewData.forEach((view) => {
                this.viewData[view.name] = {};
                this.viewData[view.name].aspectRatio = view.lock_ratio ? view.height / view.width : NaN;
                this.viewData[view.name].minCropBoxWidth = view.width ? view.width : 200;
                this.viewData[view.name].minCropBoxHeight = view.height ? view.height : 100;
            });
        }

        this.currentCropView = Object.keys(this.viewData)[0];

        if (this.savedEditData) {
            this.editData = this.savedEditData;
        }

        if (this.hasCropper) {
            this.components.cropper = {};
            this.components.cropper.wrapper = this.node.querySelector(
                SELECTORS.CROPPER_WRAPPER
            );
            this.components.cropper.component = new MediaCropper(this);
            this.components.cropper.component.init();

            this.currentView = 'cropper';

        } else {
            this.currentView = 'focus';
        }

        if (this.hasFocusSelect) {
            this.components.focus = {};
            this.components.focus.wrapper = this.node.querySelector(
                SELECTORS.FOCUS_WRAPPER
            );
            this.components.focus.component = new Focuspoint(this);
            this.components.focus.component.init();
        }

        if (this.hasCropper && this.hasFocusSelect) {
            this.viewSwitch = this.node.querySelector(SELECTORS.SELECT_FOCUS_POINT);

            this.viewSwitch.addEventListener('click', this.changeView);
        }

        this.save.addEventListener('click', this.saveEdit);

        this.initialized = true;
        this.node.dataset.initialized = true;

        this.node.addEventListener('destroy', this.destroy);
    }


}

export { EditImage };
