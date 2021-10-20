/* global document */
export function querySelectorAllArray(selector) {
    return Array.prototype.slice.call(document.querySelectorAll(selector));
}
