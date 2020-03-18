import { SELECTORS, MODIFIERS } from "./config";


class Focuspoint {
    constructor(cropper) {
        this.cropper = cropper;
        this.toggle = cropper.node.querySelector(SELECTORS.SELECT_FOCUS_POINT);
        this.cropperPreview = cropper.node.querySelector(SELECTORS.CROPPER_PREVIEW);
        this.focusPointWrapper = cropper.node.querySelector(SELECTORS.FOCUS_POINT_WRAPPER);

        this.isVisible = false;

        this.addEventListeners = this.addEventListeners.bind(this);
        this.toggleVisibility = this.toggleVisibility.bind(this);

        console.log(this);
    }

    toggleVisibility(e) {
        e.preventDefault();

        if (!this.isVisible) {
            this.cropperPreview.classList.add(MODIFIERS.PREVIEW_HIDDEN);
            this.focusPointWrapper.classList.add(MODIFIERS.FOCUS_POINT_WRAPPER_VISIBLE);
        } else {
            this.cropperPreview.classList.remove(MODIFIERS.PREVIEW_HIDDEN);
            this.focusPointWrapper.classList.remove(MODIFIERS.FOCUS_POINT_WRAPPER_VISIBLE);
        }

        this.isVisible = !this.isVisible;
    }

    addEventListeners() {
        this.toggle.addEventListener('click', this.toggleVisibility);
    }

    init() {
        this.addEventListeners();
    }
}

export { Focuspoint }
