import flatpickr from 'flatpickr';
import {Dutch} from 'flatpickr/dist/l10n/nl';
import {French} from 'flatpickr/dist/l10n/fr';
import {German} from 'flatpickr/dist/l10n/de';
import {English} from 'flatpickr/dist/l10n/default';
import {CLASSES, ARROWS} from './datepicker.config';
import {sliceArray} from '../helpers/sliceArray';

const defaultLocale = 'nl';
const locales = {
    nl: Dutch,
    fr: French,
    de: German,
    en: English
};

class Datepicker {
    constructor(element) {
        this.domNode = element;
        this.calendarNode = this.domNode.querySelector(`.${CLASSES.CALENDAR_WRAPPER}`);

        this.isRangePicker = this.domNode.hasAttribute('data-range');
        this.datePicker = null;
        this.locale = getLocale();

        this.config = {
            minDate: this.domNode.hasAttribute('data-minDate') ? this.domNode.getAttribute('data-minDate') : null,
            maxDate: this.domNode.hasAttribute('data-maxDate') ? this.domNode.getAttribute('data-maxDate') : null,
            nextArrow: ARROWS.NEXT,
            prevArrow: ARROWS.PREV,
            dateFormat: 'd-m-Y',
            locale: locales[this.locale]
        };

        this.initDatePicker();
    }

    initDatePicker() {
        this.datePicker = flatpickr(this.domNode, this.config);
    }
}

function getLocale() {
    return document.documentElement.lang !== '' ? document.documentElement.lang : defaultLocale;
}

function initDatePickers() {
    const DATEPICKER_NODES = sliceArray(document.querySelectorAll(`.${CLASSES.INPUT}`));
    const DATEPICKERS = [];

    DATEPICKER_NODES.forEach((element) => {
        const datepicker = new Datepicker(element);
        DATEPICKERS.push(datepicker);
    });
}

export {initDatePickers};
