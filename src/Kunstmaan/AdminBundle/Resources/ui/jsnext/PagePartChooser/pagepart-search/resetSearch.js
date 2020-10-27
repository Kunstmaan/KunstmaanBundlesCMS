import { CLASSES } from '../config';

export function resetSearch(searchItems) {
    searchItems.forEach((item) => {
        item.classList.remove(CLASSES.PP_SEARCH_ITEM_HIDDEN);
    });
}
