/* global XMLHttpRequest */

/**
 * GET Wrapper
 * @param url
 * @returns Promise
 */
export function get(url) {
    if (url && typeof url === 'string' && url !== '') {
        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest();

            req.onload = () => (
                (req.status >= 200 && req.status < 400) ? resolve(req) : reject(Error(req.statusText))
            );

            req.onerror = (e) => {
                reject(Error(`Network Error: ${e}`));
            };

            req.open('GET', url);
            req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            req.send();
        });
    }

    throw new Error('url parameter cannot be empty and must be of type string');
}

/**
 * POST Wrapper
 * @param url
 * @param data
 * @returns Promise
 */
export function post(url, data) {
    if (url && typeof url === 'string' && url !== '') {
        return new Promise((resolve, reject) => {
            const req = new XMLHttpRequest();

            req.onload = () => (
                req.status === 200 ? resolve(req) : reject(Error(req.statusText))
            );

            req.onerror = (e) => {
                reject(Error(`Network Error: ${e}`));
            };

            req.open('POST', url);
            req.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            req.send(data);
        });
    }
    throw new Error('url parameter cannot be empty and must be of type string');
}
