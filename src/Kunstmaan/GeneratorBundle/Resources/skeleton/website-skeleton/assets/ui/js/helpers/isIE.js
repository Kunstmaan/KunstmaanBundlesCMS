const UAString = navigator.userAgent;

function isIE10() {
    return navigator.appVersion.indexOf('MSIE 10') !== -1;
}

function isIE11() {
    return UAString.indexOf('Trident') !== -1 && UAString.indexOf('rv:11') !== -1;
}

export {
    isIE10,
    isIE11,
};
