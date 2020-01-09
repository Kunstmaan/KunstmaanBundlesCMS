import 'picturefill';
import 'svgxuse/svgxuse';
import 'focus-visible/dist/focus-visible';
import 'intersection-observer/intersection-observer';
// import '../../../vendor/kunstmaan/cookie-bundle/bin/';

import {lazyLoadImage} from './lazyLoadImage/lazyLoadImage';
import {tabs} from './tabs/tabs';
import {navToggle} from './nav-toggle/nav-toggle';
import {modal} from './modals/modals';
import {videoLink} from './video-link/video-link';
import {initDatePickers} from './datepicker/datepicker';
import {initAudioplayers} from './audioplayer/audioplayer';

document.addEventListener('DOMContentLoaded', () => {
    lazyLoadImage();
    tabs();
    navToggle();
    modal();
    videoLink();

    initDatePickers();
    initAudioplayers();
});
