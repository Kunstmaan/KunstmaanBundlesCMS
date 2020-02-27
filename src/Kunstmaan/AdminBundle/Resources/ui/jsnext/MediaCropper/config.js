const SELECTORS = {
    HOOK: '.js-media-chooser-crop-preview-btn',
    CONTAINER: '.js-media-cropper',
    IMAGE: '.js-media-cropper-image',
    META_CONTAINER: '.js-media-cropper-meta',
    META_ITEM: '.js-media-cropper-meta-value',
    VIEW_SELECT: '.js-media-cropper-view-select',
};

const MODIFIERS = {
    CROP_BOX_SMALL_CROPPED_AREA: 'media-cropper--crop-box-expanded',
};

const CROP_BOX_THRESHOLD = 250;

const META_KEYS = ['x', 'y', 'width', 'height'];

const CROPPER_CONFIG = {
    viewMode: 2,
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
