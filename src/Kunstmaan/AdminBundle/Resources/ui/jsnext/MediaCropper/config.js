const SELECTORS = {
    HOOK: '.js-media-chooser-image-edit-btn',
    CONTAINER: '.js-image-edit',
    IMAGE: '.js-image-edit-image',
    META_CONTAINER: '.js-image-edit-meta',
    META_ITEM: '.js-image-edit-meta-value',
    VIEW_SELECT: '.js-image-edit-view-select',
    SAVE: '.js-image-edit-save',
    SELECT_FOCUS_POINT: '.js-image-edit-choose-focus-point',
    CROPPER_PREVIEW: '.js-image-edit-preview',
    CROPPER_WRAPPER: '.js-image-edit-crop-wrapper',
    FOCUS_WRAPPER: '.js-image-edit-focus-wrapper',
    FOCUS_POINT_IMG: '.js-image-edit-focus-media',
    META_FOCUS_VALUE: '.js-image-edit-meta-value-focus',
    FOCUS_POINT_CHOICE: '.js-image-edit-focus-choice',
};

const MODIFIERS = {
    CROP_BOX_SMALL_CROPPED_AREA: 'media-cropper--crop-box-expanded',
    CROPPER_HIDDEN: 'image-edit__crop-wrapper--hidden',
    FOCUS_HIDDEN: 'image-edit__focus-wrapper--hidden',
};

const CROP_BOX_THRESHOLD = 250;

const META_KEYS = ['width', 'height'];

const CROPPER_CONFIG = {
    viewMode: 2,
    movable: false,
    rotatable: false,
    scalable: false,
    zoomable: false,
    zoomOnTouch: false,
    zoomOnWheel: false,
};

export {
    SELECTORS,
    MODIFIERS,
    CROP_BOX_THRESHOLD,
    META_KEYS,
    CROPPER_CONFIG,
};
