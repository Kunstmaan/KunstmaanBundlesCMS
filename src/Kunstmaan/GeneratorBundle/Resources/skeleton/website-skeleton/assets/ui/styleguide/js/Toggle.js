import TweenLite from 'gsap/TweenLite';
import 'gsap/CSSPlugin';

export default class Toggle {
    constructor() {
        const CLASSES = {
            trigger: 'js-sg-toggle-trigger',
            content: 'js-sg-toggle-content',
        };
        const defaultDuration = 0.3; // in seconds

        Array.prototype.forEach.call(document.querySelectorAll(`.${CLASSES.trigger}`), (el) => {
            const target = document.querySelectorAll(el.getAttribute('data-target'))[0];

            this.addMultiEventistener(el, 'click touchstart mousedown', (e) => {
                e.preventDefault();
            });

            this.addMultiEventistener(el, 'touchend mouseup', () => {
                const targetContent = target.querySelectorAll(`.${CLASSES.content}`)[0];
                const currentTargetIsOpen = el.getAttribute('aria-expanded') === 'true';

                currentTargetIsOpen ? this.hideContent(el, target, targetContent, false) : this.showContent(el, target, targetContent, false);
            });

            // Check if hide/show on load
            if (target) {
                const targetContent = target.querySelectorAll(`.${CLASSES.content}`)[0];

                if (targetContent && el.getAttribute('aria-expanded') === 'true') {
                    this.showContent(el, target, targetContent);
                } else if (targetContent) {
                    this.hideContent(el, target, targetContent);
                }
            }
        });

        this.defaultDuration = defaultDuration;
    }

    hideContent(trigger, target, targetContent, smoothAnimation) {
        trigger.setAttribute('aria-expanded', false);

        if (smoothAnimation) {
            TweenLite.to(targetContent, this.defaultDuration, {
                height: 0,
            });
        } else {
            targetContent.style.height = 0;
        }
    }

    showContent(trigger, target, targetContent, smoothAnimation) {
        trigger.setAttribute('aria-expanded', true);

        if (smoothAnimation) {
            TweenLite.to(targetContent, this.defaultDuration, {
                height: 'auto',
            });
        } else {
            targetContent.style.height = 'auto';
        }
    }

    // Add multiple listeners
    addMultiEventistener(el, s, fn) {
        const evts = s.split(' ');

        for (let i = 0, iLen = evts.length; i < iLen; i += 1) {
            el.addEventListener(evts[i], fn, false);
        }
    }
}
