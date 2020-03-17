const SELECTORS = {
    HOOK: '.js-media-chooser-crop-preview-btn',
    CONTAINER: '.js-media-cropper',
    IMAGE: '.js-media-cropper-image',
    META_CONTAINER: '.js-media-cropper-meta',
    META_ITEM: '.js-media-cropper-meta-value',
    VIEW_SELECT: '.js-media-cropper-view-select',
    SAVE: '.js-media-cropper-save',
    SELECT_FOCUS_POINT: '.js-media-cropper-choose-focus-point',
    CROPPER_PREVIEW: '.js-media-cropper-preview',
    FOCUS_POINT_WRAPPER: '.js-media-cropper-focus-wrapper',
};

const MODIFIERS = {
    CROP_BOX_SMALL_CROPPED_AREA: 'media-cropper--crop-box-expanded',
    FOCUS_POINT_WRAPPER_VISIBLE: 'media-cropper__focus-wrapper--visible',
    PREVIEW_HIDDEN: 'media-cropper__preview--hidden',
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
