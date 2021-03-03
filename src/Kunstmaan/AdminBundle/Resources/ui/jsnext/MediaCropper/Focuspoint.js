import { SELECTORS, MODIFIERS } from "./config";


class Focuspoint {
    constructor(EditImage) {
        this.EditImage = EditImage;
        this.toggle = EditImage.node.querySelector(SELECTORS.SELECT_FOCUS_POINT);
        this.img = EditImage.node.querySelector(SELECTORS.FOCUS_POINT_IMG);
        this.choices = [...EditImage.node.querySelectorAll(SELECTORS.FOCUS_POINT_CHOICE)];
        this.metaValueHolder = EditImage.node.querySelector(SELECTORS.META_FOCUS_VALUE);
        this.selectedFocus = null;
        this.addEventListeners = this.addEventListeners.bind(this);
        this.setSelectedFocus = this.setSelectedFocus.bind(this);
        this.setImage = this.setImage.bind(this);
        this.setEditData = this.setEditData.bind(this);
        this.handleSavedValues = this.handleSavedValues.bind(this);
        this.onSelect = this.onSelect.bind(this);
        this.destroy = this.destroy.bind(this);
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

    setEditData() {
        if (this.selectedFocus !== null && this.EditImage.currentCropView) {
            if (!this.EditImage.editData.hasOwnProperty(this.EditImage.currentCropView)) {
                this.EditImage.editData[this.EditImage.currentCropView] = {};
            }
            this.EditImage.editData[this.EditImage.currentCropView].class = this.selectedFocus;
        }
    }

    reset() {
        this.selectedFocus = null;
        this.metaValueHolder.textContent = '';

        this.choices.forEach((choice) => {
            choice.checked = false;
        })

        this.handleSavedValues();
    }

    getSelectedFocus() {
        return this.selectedFocus;
    }

    setImage(url) {
        this.img.src = url;
    }

    addEventListeners() {
        this.choices.forEach((choice) => {
            choice.addEventListener('click', this.onSelect);
        })
    }

    removeEventListeners() {
        this.choices.forEach((choice) => {
            choice.removeEventListener('click', this.onSelect);
        })
    }

    onSelect(e) {
        const choice = e.currentTarget;
        this.setSelectedFocus(choice.value);
        this.setEditData();
    }

    handleSavedValues() {
        const savedValues = this.EditImage.editData[this.EditImage.currentCropView];

        if (savedValues && savedValues.hasOwnProperty('class')) {
            this.setSelectedFocus(savedValues.class);
            this.setChoice(savedValues.class);
        }
    }

    destroy() {
        this.removeEventListeners();
    }

    init() {
        this.addEventListeners();
        this.handleSavedValues();
    }
}

export { Focuspoint }
