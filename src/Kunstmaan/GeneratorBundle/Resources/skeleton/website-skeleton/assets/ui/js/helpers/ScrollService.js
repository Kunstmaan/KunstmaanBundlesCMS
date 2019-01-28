class ScrollService {
    constructor(target, options, callback) {
        this.options = options;
        this.callback = callback;
        this.observer = new IntersectionObserver(this.callback, this.options);
        this.target = target;

        this.observer.observe(this.target);
    }
}

export { ScrollService };
