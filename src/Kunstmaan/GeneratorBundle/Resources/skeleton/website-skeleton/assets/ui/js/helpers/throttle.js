function throttle(delay, fn) {
    let lastCall = 0;

    return (...args) => {
        const now = (new Date()).getTime();

        if (now - lastCall < delay) {
            return;
        }

        lastCall = now;

        return fn(...args); // eslint-disable-line consistent-return
    };
}

function debounce(delay, fn) {
    let timeout;

    clearTimeout(timeout);

    timeout = setTimeout(fn, delay);
}

export { throttle, debounce };
