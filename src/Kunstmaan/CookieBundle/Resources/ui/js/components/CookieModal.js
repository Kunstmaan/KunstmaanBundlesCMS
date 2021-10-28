/* global document */
/* eslint class-methods-use-this: 0 */

import { querySelectorAllArray } from '../utils';

import Component from './Component';

import { select, SELECT_VISIBILITY_SCOPE, SELECT_COOKIE_MODAL_DETAIL_CONTENT } from '../state';
import {
    COOKIE_MODAL_VISIBILITY_SCOPE,
    COOKIE_MODAL_VISIBILITY_SCOPE_DETAIL,
} from '../state/state.config';
import {
    COOKIE_MODAL_IDENTIFIER,
    TOC_IDENTIFIER,
    DETAIL_CONTENT_WRAPPER_IDENTIFIER,
    MODAL_CONTENT_WRAPPER_IDENTIFIER,
    CLASSES,
} from '../config/cookiemodal.config';

class CookieModal extends Component {
    constructor({ configuration } = { configuration: { modalContent: undefined } }) {
        super({
            identifier: COOKIE_MODAL_IDENTIFIER,
            configuration: Object.assign(configuration, {
                visibilityScopes: {
                    [COOKIE_MODAL_VISIBILITY_SCOPE]: [CLASSES.VISIBLE],
                    [COOKIE_MODAL_VISIBILITY_SCOPE_DETAIL]: [CLASSES.VISIBLE, CLASSES.DETAIL_OPEN],
                },
            }),
        });

        this.initializeScrollHandlers();
        this.modalDetailContentWrapper = this.collectModalDetailContentWrapper();

        if (this.configuration.isOnCookiePage) {
            // This means we are on the cookie settings page. There is no need to show the content in the
            // modal because it is already in the page.
            // We only need the modal to show detailcontent.
            this.addPageClasses();
        } else {
            // This means we are not on the cookie settings pages but somewhere else in the website.
            // In this case we use the modal to show the privacy content.
            this.modalContentWrapper = this.collectModalContentWrapper();
            this.loadContentIntoModal(configuration.modalContent);
        }
    }

    // this is overriden for the subviews.
    handleComponentState(state) {
        super.handleComponentState(state);

        this.checkIfDetailViewShouldOpen();
    }

    loadContentIntoModal(modalContent) {
        this.modalContentWrapper.innerHTML = typeof modalContent === 'string' ? modalContent : modalContent.outerHTML;
    }

    addPageClasses() {
        this.vdom.classList.add(CLASSES.PAGE);
    }

    checkIfDetailViewShouldOpen() {
        const { visibilityScope } = select(SELECT_VISIBILITY_SCOPE);

        if (visibilityScope === COOKIE_MODAL_VISIBILITY_SCOPE_DETAIL) {
            const { cookieModalDetailPageContent } = select(SELECT_COOKIE_MODAL_DETAIL_CONTENT);

            this.modalDetailContentWrapper.innerHTML = cookieModalDetailPageContent;
        }
    }

    collectModalContentWrapper() {
        return document.getElementById(MODAL_CONTENT_WRAPPER_IDENTIFIER);
    }

    collectModalDetailContentWrapper() {
        return document.getElementById(DETAIL_CONTENT_WRAPPER_IDENTIFIER);
    }

    initializeScrollHandlers() {
        const allTocItems = querySelectorAllArray(TOC_IDENTIFIER);

        allTocItems.forEach((tocItem) => {
            tocItem.addEventListener('click', this.handleTOCClick);
        });
    }

    handleTOCClick(e) {
        if (typeof e.currentTarget.scrollIntoView === 'function') {
            e.preventDefault();

            const targetElement = document.querySelector(e.currentTarget.getAttribute('href'));

            targetElement.scrollIntoView({
                behavior: 'smooth',
                inline: 'end',
                block: 'start',
            });
        }
    }
}

export default CookieModal;
