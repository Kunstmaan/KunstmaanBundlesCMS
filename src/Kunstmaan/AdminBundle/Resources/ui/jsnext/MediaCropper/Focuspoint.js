import { SELECTORS, MODIFIERS } from "./config";


class Focuspoint {
    constructor(cropper) {
        this.cropper = cropper;
        this.toggle = cropper.node.querySelector(SELECTORS.SELECT_FOCUS_POINT);
        this.cropperPreview = cropper.node.querySelector(SELECTORS.CROPPER_PREVIEW);
        this.focusPointWrapper = cropper.node.querySelector(SELECTORS.FOCUS_POINT_WRAPPER);
        this.img = cropper.node.querySelector(SELECTORS.FOCUS_POINT_IMG);
        this.choices = [...cropper.node.querySelectorAll(SELECTORS.FOCUS_POINT_CHOICE)];
        this.metaValueHolder = cropper.node.querySelector(SELECTORS.META_FOCUS_VALUE);

        this.isVisible = false;
        this.selectedFocus = null;

        this.addEventListeners = this.addEventListeners.bind(this);
        this.toggleVisibility = this.toggleVisibility.bind(this);
        this.setSelectedFocus = this.setSelectedFocus.bind(this);
        this.setImage = this.setImage.bind(this);
        this.setCropperData = this.setCropperData.bind(this);
    }

    toggleVisibility(e) {
        if (e) {
            e.preventDefault();
        }

        const currentTextContent = this.toggle.textContent;
        const nextTextContent = this.toggle.dataset.booleanText;

        if (!this.isVisible) {
            this.cropperPreview.classList.add(MODIFIERS.PREVIEW_HIDDEN);
            this.focusPointWrapper.classList.add(MODIFIERS.FOCUS_POINT_WRAPPER_VISIBLE);
            this.setImage();
        } else {
            this.cropperPreview.classList.remove(MODIFIERS.PREVIEW_HIDDEN);
            this.focusPointWrapper.classList.remove(MODIFIERS.FOCUS_POINT_WRAPPER_VISIBLE);
        }

        this.isVisible = !this.isVisible;
        this.toggle.textContent = nextTextContent;
        this.toggle.dataset.booleanText = currentTextContent;
    }

    setSelectedFocus(value) {
        this.selectedFocus = value;
        this.metaValueHolder.textContent = value;
    }

    setChoice(value) {
        this.choices.forEach((choice) => {
            choice.checked = choice.value === value;
        });
    }

    setCropperData() {
        if (this.selectedFocus !== null) {
            this.cropper.cropData[this.cropper.currentView].class = this.selectedFocus;
        }
    }

    reset() {
        this.selectedFocus = null;
        this.metaValueHolder.textContent = '';

        this.choices.forEach((choice) => {
            choice.checked = false;
        })
    }

    getSelectedFocus() {
        return this.selectedFocus;
    }

    setImage() {
        this.img.src = this.cropper.croppedImageUrl;
    }

    addEventListeners() {
        this.toggle.addEventListener('click', this.toggleVisibility);
        this.choices.forEach((choice) => {
            choice.addEventListener('click', (e) => {
                this.setSelectedFocus(choice.value);
                this.setCropperData();
            });
        })
    }

    init() {
        this.addEventListeners();
    }
}

export { Focuspoint }
