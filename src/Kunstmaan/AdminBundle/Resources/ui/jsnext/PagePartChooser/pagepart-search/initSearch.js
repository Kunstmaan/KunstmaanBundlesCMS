import Fuse from 'fuse.js';
import { ATTRIBUTES, SELECTORS } from '../config';
import { resetSearch } from './resetSearch';
import { updateSearch } from './updateSearch';

export function initSearch(ppChooser) {
    const ppTypes = JSON.parse(ppChooser.getAttribute(ATTRIBUTES.PP_TYPES));

    const ppTypesSearchData = makePagePartDataSearchable(ppTypes);

    const ppList = [...ppChooser.querySelectorAll(SELECTORS.PP_SEARCH_ITEM)];
    const fuse = initFuse(ppTypesSearchData);

    const searchField = ppChooser.querySelector(SELECTORS.PP_SEARCH_FIELD);
    searchField.addEventListener('keyup', searchHandler);

    const searchResetButton = ppChooser.querySelector(SELECTORS.PP_SEARCH_RESET);
    searchResetButton.addEventListener('click', resetHandler);

    $(ppChooser).on('shown.bs.modal', openModalHandler);

    function searchHandler() {
        if (searchField.value.trim().length > 0) {
            const searchResults = fuse.search(searchField.value);
            updateSearch(ppList, searchResults);
        } else {
            resetSearch(ppList);
        }
    }

    function resetHandler() {
        searchField.value = '';
        resetSearch(ppList);
    }

    function openModalHandler() {
        searchField.focus();
    }
}

function makePagePartDataSearchable(ppTypes) {
    return ppTypes.map(({ name, class: className }) => ({
        name,
        className: extractClassNameFromNamespace(className),
    }));
}

function extractClassNameFromNamespace(ppClass) {
    let className = ppClass;

    const lastBackSlashIndex = className.lastIndexOf('\\');
    if (lastBackSlashIndex !== -1) {
        className = className.substring(lastBackSlashIndex + 1);
    }

    return className.replace('PagePart', '');
}

function initFuse(ppSearchData) {
    return new Fuse(ppSearchData, {
        keys: [{
            name: 'name',
            weight: 0.7,
        }, {
            name: 'className',
            weight: 0.3, // The internal name is less important
        }],
        id: 'name',
        threshold: 0.4,
        shouldSort: true,
    });
}
