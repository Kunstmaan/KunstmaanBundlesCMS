function getViewportHeight() {
    return Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
}

function getViewportWidth() {
    return Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
}

export {
    getViewportHeight,
    getViewportWidth,
};
