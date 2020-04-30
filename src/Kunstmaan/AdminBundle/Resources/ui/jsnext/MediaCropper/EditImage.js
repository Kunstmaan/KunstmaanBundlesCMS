import { SELECTORS, MODIFIERS } from './config';
import { MediaCropper } from './MediaCropper';
import { Focuspoint } from './Focuspoint';

class EditImage {
    constructor(node) {
        this.node = node;
        this.hasCropper = node.hasAttribute('data-cropping-views');
        this.hasFocusSelect = node.hasAttribute('data-focus-point-classes');
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.save = this.metaContainer.querySelector(SELECTORS.SAVE);
        this.input = document.querySelector(`#${node.dataset.inputId}`);
        this.components = {};

        this.init();
    }

    changeView() {
        switch (this.currentView) {
            case 'focus':
                this.components.focus.wrapper.classList.remove(MODIFIERS.FOCUS_HIDDEN);
                this.components.cropper.wrapper.classList.add(MODIFIERS.CROPPER_HIDDEN);
                break;

            default:
                this.components.cropper.wrapper.classList.remove(MODIFIERS.CROPPER_HIDDEN);
                this.components.focus.wrapper.classList.add(MODIFIERS.FOCUS_HIDDEN);
                break;
        }
    }

    init() {
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

        console.log(this);


    }
}

export { EditImage };
