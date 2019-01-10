import {TweenLite, Power4} from 'gsap/TweenLite';
import 'gsap/ScrollToPlugin';

export default class MobileNav {

    constructor() {
        const CLASSES = {
            button: 'js-mobile-nav-btn',
            mobileSubnav: 'js-mobile-subnav',
            mobileNav: 'js-mobile-nav',
            activeClass: 'mobile-nav-open'
        };
        const scrollOffset = 80;
        const toTopOffset = 150;

        const button = document.querySelectorAll(`.${CLASSES.button}`)[0];
        const nav = document.querySelectorAll(`.${CLASSES.mobileNav}`)[0];
        const subnav = document.querySelectorAll(`.${CLASSES.mobileSubnav}`)[0];

        this.CLASSES = CLASSES;
        this.scrollOffset = scrollOffset;
        this.toTopOffset = toTopOffset;
        this.button = button;
        this.nav = nav;
        this.subnav = subnav;

        button && this._initMobileNav();
        subnav && this._initMobileSubnav();
    }

    _initMobileNav() {
        this.button.addEventListener('click', () => {
            if (this.button.classList.contains(this.CLASSES.activeClass)) {
                this.nav.classList.remove(this.CLASSES.activeClass);
                this.button.classList.remove(this.CLASSES.activeClass);
            } else {
                this.nav.classList.add(this.CLASSES.activeClass);
                this.button.classList.add(this.CLASSES.activeClass);
            }
        });
    };

    _initMobileSubnav() {
        this.subnav.addEventListener('change', (e) => {
            const target = (e.currentTarget).value;

            if (target) {
                TweenLite.to(window, .3, {
                    scrollTo: {
                        y: `${target}`,
                        offsetY: this.scrollOffset
                    },
                    ease: Power4.easeOut
                });
            }
        }, false);
    };
}
