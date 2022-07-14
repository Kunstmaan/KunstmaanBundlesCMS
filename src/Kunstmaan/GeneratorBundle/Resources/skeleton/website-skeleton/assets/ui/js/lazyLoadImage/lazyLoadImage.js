import {sliceArray} from '../helpers/sliceArray';
import {ScrollService} from '../helpers/ScrollService';

const OBSERVER_OPTIONS = {
    threshold: [0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1]
};
const imageHolders = sliceArray(document.querySelectorAll('.js-lazy-load-img-wrapper'));
const OBSERVERS = [];

function lazyLoadImage() {
    if (imageHolders.length === 0) {
        return false;
    }

    imageHolders.forEach((imageHolder) => {
        const observer = new ScrollService(imageHolder, OBSERVER_OPTIONS, handleItem);
        OBSERVERS.push(observer);
    });

    return OBSERVERS;
}

function handleItem(entries, observer) {
    entries.forEach((entry) => {
        const TARGET = entry.target;
        const images = sliceArray(TARGET.querySelectorAll('.js-lazy-load-img'));

        if (entry.isIntersecting) {
            loadHighRes(images);

            observer.unobserve(TARGET);
        }
    });
}

function loadHighRes(images) {
    images.forEach((image) => {
        const srcSet = image.hasAttribute('data-srcset') ? image.dataset.srcset : false;
        // eslint-disable-next-line prefer-destructuring
        const src = image.dataset.src;

        if (srcSet) {
            image.setAttribute('srcset', srcSet);
        }

        image.setAttribute('src', src);
    });
}

export {lazyLoadImage};
