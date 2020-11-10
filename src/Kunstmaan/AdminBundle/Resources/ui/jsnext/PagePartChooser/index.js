import { SELECTORS } from './config';
import { initSearch } from './pagepart-search/initSearch';

export const initPagePartChoosers = ({ container = window.document } = {}) => {
    const pagePartChoosers = [...container.querySelectorAll(SELECTORS.PP_CHOOSER)];

    pagePartChoosers.forEach((pagePartChooser) => {
        initSearch(pagePartChooser);
    });
};
