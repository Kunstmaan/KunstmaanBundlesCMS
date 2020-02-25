import Croppr from 'croppr';

const SELECTORS = {
    CONTAINER: '.js-media-cropper',
    IMAGE: '.js-media-cropper-image',
    META_CONTAINER: '.js-media-cropper-meta',
    META_ITEM: '.js-media-cropper-meta-value',
};

const META_KEYS = ['x', 'y', 'width', 'height'];

class MediaCropper {
    constructor(node) {
        this.node = node;
        this.image = this.node.querySelector(SELECTORS.IMAGE);
        this.metaContainer = this.node.querySelector(SELECTORS.META_CONTAINER);
        this.croppedData = {};
        this.metaValueNodes = {};
        this.croppr = new Croppr(this.image, {
            startSize: [80, 80, '%'],
            onCropMove: (value) => this.updateValue(value),
        });

        this.getValueNodes();

        console.log(this);
    }

    getValueNodes() {
        META_KEYS.forEach((key) => {
            this.metaValueNodes[key] = this.metaContainer.querySelector(`${SELECTORS.META_ITEM}-${key}`);
        });
    }

    updateValue({x, y, width, height}) {
        this.metaValueNodes.x.textContent = x;
        this.metaValueNodes.y.textContent = y;
        this.metaValueNodes.width.textContent = width;
        this.metaValueNodes.height.textContent = height;
    }
}


function initMediaCroppers(container = window.document) {
    const MEDIA_CROPPERS = [...container.querySelectorAll(SELECTORS.CONTAINER)];

    MEDIA_CROPPERS.forEach((CROPPER) => {
        new MediaCropper(CROPPER);
    })
}

export { initMediaCroppers };
