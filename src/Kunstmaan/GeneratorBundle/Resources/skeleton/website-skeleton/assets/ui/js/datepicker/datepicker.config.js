const CLASSES = {
    ELEMENT: 'js-datepicker',
    INPUT: 'js-datepicker-input',
    CALENDAR_WRAPPER: 'js-datepicker-calendar',
    CLOSE_CALENDAR: 'js-datepicker-control-close',
    OPEN_CALENDAR: 'js-datepicker-control-open',
    MODIFIERS: {
        SHOW_CALENDAR: 'datepicker--open',
    },
};

const ARROWS = {
    NEXT:
    `<span class="datepicker__calendar__direction__item datepicker__calendar__direction__item--next">
        <svg class="datepicker__calendar__direction__item__icon">
            <use xlink:href="icons/symbol-defs.svg#icon--arrow-right"></use>
        </svg>
    </span>`,
    PREV:
    `<span class="datepicker__calendar__direction__item datepicker__calendar__direction__item--prev">
        <svg class="datepicker__calendar__direction__item__icon">
            <use xlink:href="icons/symbol-defs.svg#icon--arrow-left"></use>
        </svg>
    </span>`,
};

export { CLASSES, ARROWS };
