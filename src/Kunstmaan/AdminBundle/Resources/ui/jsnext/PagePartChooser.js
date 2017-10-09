import Fuse from 'fuse.js';

const SELECTORS = {
    PP_CHOOSER: '.js-pp-chooser',
    PP_SEARCH_FIELD: '.js-pp-search',
    PP_SEARCH_ITEM: '.js-pp-search-item',
    PP_SEARCH_RESET: '.js-pp-search__reset'
};

const CLASSES = {
    PP_SEARCH_ITEM_HIDDEN: 'pp-search-item--hidden'
};

const ATTRIBUTES = {
    PP_TYPES: 'data-pp-types',
    PP_NAME: 'data-pp-name'
}

let ppList;

export default class PagePartChooser {
    static init(container = window.document) {
        const ppChooser = container.querySelector(SELECTORS.PP_CHOOSER);

        if(ppChooser) {
            initSearch(ppChooser);
        }
    }
}
// TODO Refactor to a separate PagePartSearch class?
const initSearch = (ppChooser) => {
    const ppTypes = JSON.parse(ppChooser.getAttribute(ATTRIBUTES.PP_TYPES));

    //Clean up the dataset
    ppTypes.forEach((type) => {
        const ppClass = type['class'];
        const index = ppClass.lastIndexOf('\\');

        if(-1 !== index) {
            // We also search by class name because PageParts can be renamed
            // Remove the namespace / 'PagePart' from the classname
            type['class'] = ppClass.substring(index + 1).replace('PagePart', '');
        }
    });

    const searchField = ppChooser.querySelector(SELECTORS.PP_SEARCH_FIELD);
    ppList = Array.prototype.slice.call(ppChooser.querySelectorAll(SELECTORS.PP_SEARCH_ITEM));

    const fuse = new Fuse(ppTypes, {
        keys: [{
            name: 'name',
            weight: 0.7
        }, {
            name: 'class',
            weight: 0.3 //The internal name is less important
        }],
        id: 'name',
        threshold: 0.4,
        shouldSort: true
    });

    const searchHandler = () => {
        if(searchField.value.trim().length > 0) {
            const searchResults = fuse.search(searchField.value);
            updateSearch(ppList, searchResults);
        } else {
            resetSearch(ppList);
        }
    };

    searchField.addEventListener('keyup', searchHandler);

    const searchReset = ppChooser.querySelector(SELECTORS.PP_SEARCH_RESET);

    const resetHandler = () => {
        searchField.value = '';
        resetSearch(ppList);
    };

    searchReset.addEventListener('click', resetHandler);

};

function updateSearch(searchItems, searchResults) {
    searchItems.forEach((item) => {
        const ppName = item.getAttribute(ATTRIBUTES.PP_NAME);

        //TODO include a polyfill for [].includes()
        if(searchResults.includes(ppName)) {
            item.classList.remove(CLASSES.PP_SEARCH_ITEM_HIDDEN);
        } else {
            item.classList.add(CLASSES.PP_SEARCH_ITEM_HIDDEN);
        }
    });

}

function resetSearch(searchItems) {
    searchItems.forEach((item) => {
        item.classList.remove(CLASSES.PP_SEARCH_ITEM_HIDDEN);
    });
}
