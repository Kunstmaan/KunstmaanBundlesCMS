import { CLASSES, ATTRIBUTES } from '../config';

export function updateSearch(searchItems, searchResults) {
    searchItems.forEach((item) => {
        const ppName = item.getAttribute(ATTRIBUTES.PP_NAME);

        if (searchResults.includes(ppName)) {
            item.classList.remove(CLASSES.PP_SEARCH_ITEM_HIDDEN);
        } else {
            item.classList.add(CLASSES.PP_SEARCH_ITEM_HIDDEN);
        }
    });
}
